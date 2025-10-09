<?php
header('Content-Type: application/json');

require_once __DIR__.'/../../models/ItemPedido.php';
require_once __DIR__.'/../../models/ItemPedidoDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

$json_data = file_get_contents('php://input');
$data = json_decode($json_data);

if (!$data || !isset($data->pedidoId) || !isset($data->produtoId) || !isset($data->quantidade) || !isset($data->valorUnitario)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados incompletos. pedidoId, produtoId, quantidade e valorUnitario são obrigatórios.']);
    exit;
}

$novoItem = new \app\models\ItemPedido();
$novoItem->setPedidoId($data->pedidoId);
$novoItem->setProdutoId($data->produtoId);
$novoItem->setQuantidade($data->quantidade);
$novoItem->setValorUnitario($data->valorUnitario);

$itemPedidoDAO = new \app\models\ItemPedidoDAO();
$idInserido = $itemPedidoDAO->insert($novoItem);

if ($idInserido) {
    http_response_code(201);
    $novoItem->setIdItemPedido($idInserido);
    echo json_encode($novoItem->toArray());
} else {
    http_response_code(500);
    echo json_encode(['erro' => 'Ocorreu um erro ao salvar o item do pedido.']);
}