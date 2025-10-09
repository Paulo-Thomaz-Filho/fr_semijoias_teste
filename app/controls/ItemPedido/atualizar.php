<?php
// Caminho: app/controls/ItemPedido/atualizar.php
header('Content-Type: application/json');

require_once __DIR__.'/../../models/ItemPedido.php';
require_once __DIR__.'/../../models/ItemPedidoDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

$json_data = file_get_contents('php://input');
$data = json_decode($json_data);

if (!$data || !isset($data->idItemPedido)) {
    http_response_code(400);
    echo json_encode(['erro' => 'O idItemPedido é obrigatório para atualização.']);
    exit;
}

$itemPedidoDAO = new \app\models\ItemPedidoDAO();
$itemExistente = $itemPedidoDAO->getById($data->idItemPedido);

if (!$itemExistente) {
    http_response_code(404);
    echo json_encode(['erro' => 'Item de pedido não encontrado para atualização.']);
    exit;
}

// Atualiza os dados do objeto existente com os dados recebidos
$itemExistente->setPedidoId($data->pedidoId ?? $itemExistente->getPedidoId());
$itemExistente->setProdutoId($data->produtoId ?? $itemExistente->getProdutoId());
$itemExistente->setQuantidade($data->quantidade ?? $itemExistente->getQuantidade());
$itemExistente->setValorUnitario($data->valorUnitario ?? $itemExistente->getValorUnitario());

if ($itemPedidoDAO->update($itemExistente)) {
    echo json_encode($itemExistente->toArray());
} else {
    http_response_code(500);
    echo json_encode(['erro' => 'Ocorreu um erro ao atualizar o item do pedido.']);
}