<?php
// Atualiza status do pedido conforme rota de cancelado
require_once __DIR__ . '/../../../app/models/PedidoDAO.php';
require_once __DIR__ . '/../../../app/models/StatusDAO.php';

// Recebe id_pedido via GET
$idPedido = $_GET['id_pedido'] ?? null;
if ($idPedido) {
    $pedidoDAO = new PedidoDAO();
    $pedido = $pedidoDAO->getById($idPedido);
    if ($pedido) {
        $statusDAO = new StatusDAO();
        $status = $statusDAO->getByName('Cancelado');
        if ($status) {
            $pedidoDAO->update($idPedido, ['id_status' => $status['id_status']]);
        }
    }
}
// Redireciona para página de cancelado
header('Location: /views/cancelado.html');
exit;
