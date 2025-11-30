<?php
// Utilitário para processar notificações do Mercado Pago e atualizar pedidos
namespace app\core\utils;

use app\models\PedidoDAO;
use app\models\StatusDAO;
use app\models\Pedido;

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../models/pedidoDAO.php';
require_once __DIR__ . '/../../models/StatusDAO.php';
require_once __DIR__ . '/../../models/Pedido.php';

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
        if ($statusPagamento === 'approved') {
            $novoStatus = $statusDAO->getByName('Aprovado');
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
        }
    }
}
