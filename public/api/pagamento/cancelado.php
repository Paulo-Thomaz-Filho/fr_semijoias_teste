<?php
// Atualiza status do pedido conforme rota de cancelado
require_once __DIR__ . '/../../../app/models/PedidoDAO.php';
require_once __DIR__ . '/../../../app/models/StatusDAO.php';

// Recebe id_pedido via GET
$idPedido = $_GET['id_pedido'] ?? null;
if ($idPedido) {
        $pedidoDAO = new \app\models\PedidoDAO();
        $pedido = $pedidoDAO->getById($idPedido);
        if ($pedido && isset($pedido['id_pedido'])) {
            $statusDAO = new \app\models\StatusDAO();
            $status = $statusDAO->getByName('Cancelado');
            if ($status) {
                $pedidoObj = new \app\models\Pedido(
                    $pedido['id_pedido'],
                    $pedido['produto_nome'],
                    $pedido['id_cliente'],
                    $pedido['preco'],
                    $pedido['endereco'],
                    $pedido['data_pedido'],
                    $pedido['quantidade'],
                    $status->getIdStatus(),
                    $pedido['descricao']
                );
                $pedidoDAO->update($pedidoObj);
            }
        } else {
            error_log('Pedido não encontrado ou dados incompletos para id_pedido=' . $idPedido . ' | Retorno: ' . print_r($pedido, true));
        }
}
// Redireciona para página de cancelado
header('Location: /views/cancelado.html');
exit;
