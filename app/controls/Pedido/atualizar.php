<?php
header('Content-Type: application/json');

require_once __DIR__.'/../../models/Pedido.php';
require_once __DIR__.'/../../models/PedidoDAO.php';
require_once __DIR__.'/../../models/Produto.php';
require_once __DIR__.'/../../models/ProdutoDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); exit;
}

$data = json_decode(file_get_contents('php://input'));

if (!$data || !isset($data->idPedido)) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID do pedido é obrigatório.']);
    exit;
}

$pedidoDAO = new \app\models\PedidoDAO();
$pedido = $pedidoDAO->getById($data->idPedido);

if (!$pedido) {
    http_response_code(404);
    echo json_encode(['erro' => 'Pedido não encontrado.']);
    exit;
}

// Atualiza o objeto com os novos dados
$pedido->setUsuarioId($data->clienteId);
$pedido->setEnderecoId($data->clienteId);
$pedido->setValorTotal($data->valor);
$pedido->setQuantidade($data->quantidade);
$pedido->setDescricao($data->descricao);
$pedido->setDataPedido($data->data);
$pedido->setStatus($data->status); 

if ($pedidoDAO->update($pedido)) {
    echo json_encode($pedido->toArray());
} else {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao atualizar o pedido.']);
}