<?php
header('Content-Type: application/json');

require_once __DIR__.'/../../models/ItemPedido.php';
require_once __DIR__.'/../../models/ItemPedidoDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'));

// CORRIGIDO: Validando os nomes corretos (snake_case) que o JavaScript envia
if (!$data || !isset($data->pedido_id) || !isset($data->produto_id) || !isset($data->quantidade) || !isset($data->valor_unitario)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados incompletos. pedido_id, produto_id, quantidade e valor_unitario são obrigatórios.']);
    exit;
}

$novoItem = new \app\models\ItemPedido();
$novoItem->set_pedido_id($data->pedido_id);
$novoItem->set_produto_id($data->produto_id);
$novoItem->set_quantidade($data->quantidade);
$novoItem->set_valor_unitario($data->valor_unitario);

$itemPedidoDAO = new \app\models\ItemPedidoDAO();
$idInserido = $itemPedidoDAO->insert($novoItem);

if ($idInserido) {
    http_response_code(201);
    $novoItem->set_id_item_pedido($idInserido);
    echo json_encode($novoItem->toArray());
} else {
    http_response_code(500);
    echo json_encode(['erro' => 'Ocorreu um erro ao salvar o item do pedido.']);
}