<?php
header('Content-Type: application/json');

require_once __DIR__.'/../../models/Pedido.php';
require_once __DIR__.'/../../models/PedidoDAO.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['erro' => 'O ID do pedido é obrigatório.']);
    exit;
}

$pedidoDAO = new \app\models\PedidoDAO();
$pedido = $pedidoDAO->getById($id);

if ($pedido) {
    echo json_encode($pedido->toArray());
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Pedido não encontrado.']);
}