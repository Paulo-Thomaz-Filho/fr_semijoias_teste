<?php
header('Content-Type: application/json');

require_once '../../models/Pedido.php';
require_once '../../models/PedidoDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['erro' => 'O ID do pedido é obrigatório para exclusão.']);
    exit;
}

$pedidoDAO = new \app\models\PedidoDAO();
$pedidoExistente = $pedidoDAO->getById($id);

if (!$pedidoExistente) {
    http_response_code(404);
    echo json_encode(['erro' => 'Pedido não encontrado para exclusão.']);
    exit;
}

if ($pedidoDAO->delete($id)) {
    echo json_encode(['mensagem' => 'Pedido excluído com sucesso.']);
} else {
    http_response_code(500);
    echo json_encode(['erro' => 'Ocorreu um erro ao excluir o pedido.']);
}
