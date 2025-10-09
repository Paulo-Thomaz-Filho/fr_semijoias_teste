<?php
header('Content-Type: application/json');

require_once __DIR__.'/../../models/ItemPedido.php';
require_once __DIR__.'/../../models/ItemPedidoDAO.php';

$pedidoId = $_GET['pedidoId'] ?? null;

if (!$pedidoId) {
    http_response_code(400);
    echo json_encode(['erro' => 'O ID do pedido (pedidoId) é obrigatório.']);
    exit;
}

$itemPedidoDAO = new \app\models\ItemPedidoDAO();
$itens = $itemPedidoDAO->getByPedidoId($pedidoId);

// Mesmo que não encontre itens, retorna um array vazio, o que é um comportamento esperado.
echo json_encode(array_map(fn($item) => $item->toArray(), $itens));