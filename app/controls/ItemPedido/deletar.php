<?php
// Caminho: app/controls/ItemPedido/deletar.php
header('Content-Type: application/json');

require_once __DIR__.'/../../models/ItemPedidoDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

$json_data = file_get_contents('php://input');
$data = json_decode($json_data);

if (!$data || !isset($data->idItemPedido)) {
    http_response_code(400);
    echo json_encode(['erro' => 'O idItemPedido é obrigatório para deletar.']);
    exit;
}

$itemPedidoDAO = new \app\models\ItemPedidoDAO();

if ($itemPedidoDAO->delete($data->idItemPedido)) {
    echo json_encode(['sucesso' => 'Item de pedido deletado com sucesso.']);
} else {
    http_response_code(500);
    echo json_encode(['erro' => 'Ocorreu um erro ao deletar o item do pedido.']);
}