<?php
// Utilitário para processar notificações do Mercado Pago e atualizar pedidos
namespace app\core\utils;

use app\models\PedidoDAO;
use app\models\StatusDAO;
use app\models\Pedido;
use app\models\UsuarioDAO;

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../models/PedidoDAO.php';
require_once __DIR__ . '/../../models/StatusDAO.php';
require_once __DIR__ . '/../../models/Pedido.php';
require_once __DIR__ . '/../../models/UsuarioDAO.php';
require_once __DIR__ . '/Mail.php';
require_once __DIR__ . '/EmailTemplate.php';

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Resources\Payment;

class WebhookHandler {
    public static function atualizarPedidoPorPagamento($paymentId) {
        // Defina seu access token aqui ou use variável de ambiente
        $accessToken = getenv('MERCADO_PAGO_ACCESS_TOKEN');
        if (!$accessToken) {
            throw new \Exception('Access token do Mercado Pago não definido.');
        }
        MercadoPagoConfig::setAccessToken($accessToken);

        // Consulta o pagamento
        $payment = Payment::find_by_id($paymentId);
        if (!$payment) {
            throw new \Exception('Pagamento não encontrado no Mercado Pago.');
        }
        // O external_reference é o id do pedido no seu sistema
        $idPedido = $payment->external_reference;
        $statusPagamento = $payment->status; // 'pending', 'approved', 'rejected', etc.

        // Busca o pedido
        $pedidoDAO = new PedidoDAO();
        $pedidoArr = $pedidoDAO->getById($idPedido);
        if (!$pedidoArr) {
            throw new \Exception('Pedido não encontrado no sistema.');
        }
        $pedido = new Pedido(
            $pedidoArr['idPedido'],
            $pedidoArr['produtoNome'],
            $pedidoArr['idCliente'],
            $pedidoArr['preco'],
            $pedidoArr['endereco'],
            $pedidoArr['dataPedido'],
            $pedidoArr['quantidade'],
            $pedidoArr['idStatus'],
            $pedidoArr['descricao']
        );

        // Atualiza o status do pedido conforme o status do pagamento
        $statusDAO = new StatusDAO();
        $novoStatus = null;
        $enviarEmail = false;
        
        if ($statusPagamento === 'approved') {
            $novoStatus = $statusDAO->getByName('Aprovado');
            $enviarEmail = true; // Enviar email apenas quando aprovado
        } elseif ($statusPagamento === 'pending') {
            $novoStatus = $statusDAO->getByName('Pendente');
        } elseif ($statusPagamento === 'rejected') {
            $novoStatus = $statusDAO->getByName('Rejeitado');
        } else {
            $novoStatus = $statusDAO->getByName('Pendente');
        }
        
        if ($novoStatus) {
            $pedido->setIdStatus($novoStatus->getIdStatus());
            $pedidoDAO->update($pedido);
            
            // Enviar email de confirmação se o pagamento foi aprovado
            if ($enviarEmail) {
                try {
                    // Buscar dados do usuário
                    $usuarioDAO = new UsuarioDAO();
                    $usuarioArr = $usuarioDAO->getById($pedido->getIdCliente());
                    
                    if ($usuarioArr) {
                        $nomeUsuario = $usuarioArr['nome'];
                        $emailUsuario = $usuarioArr['email'];
                        $numeroPedido = str_pad($pedido->getIdPedido(), 6, '0', STR_PAD_LEFT);
                        
                        // Link para ver o pedido (ajuste conforme sua URL)
                        $linkPedido = 'https://frsemijoias.ifhost.gru.br/pedido';
                        
                        // Gerar o corpo do email usando o template
                        $corpoEmail = EmailTemplate::emailPedidoRealizado(
                            $nomeUsuario,
                            $numeroPedido,
                            $linkPedido
                        );
                        
                        // Enviar o email
                        $mail = new Mail(
                            $emailUsuario,
                            'Pedido Confirmado - FR Semijoias',
                            $corpoEmail
                        );
                        
                        $mail->send();
                        
                        // Log de sucesso
                        file_put_contents(
                            __DIR__ . '/../../../public/notificacao_log.txt',
                            "Email enviado para $emailUsuario - Pedido #$numeroPedido\n",
                            FILE_APPEND
                        );
                    }
                } catch (\Exception $e) {
                    // Log de erro mas não interrompe o processamento
                    file_put_contents(
                        __DIR__ . '/../../../public/notificacao_log.txt',
                        "Erro ao enviar email: " . $e->getMessage() . "\n",
                        FILE_APPEND
                    );
                }
            }
        }
    }
}
