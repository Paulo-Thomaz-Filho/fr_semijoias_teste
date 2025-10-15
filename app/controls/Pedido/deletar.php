<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

require_once $rootPath . '/app/models/Pedido.php';
require_once $rootPath . '/app/models/PedidoDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$idPedido = $_GET['id'] ?? $_GET['idPedido'] ?? $_POST['id'] ?? $_POST['idPedido'] ?? null;

if (!$idPedido) {
    http_response_code(400);
    echo json_encode(['erro' => 'O idPedido é obrigatório para exclusão.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $pedidoDAO = new \app\models\PedidoDAO();
    $pedidoExistente = $pedidoDAO->getById($idPedido);

    if (!$pedidoExistente) {
        http_response_code(404);
        echo json_encode(['erro' => 'Pedido não encontrado para exclusão.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($pedidoDAO->delete($idPedido)) {
        echo json_encode(['sucesso' => 'Pedido excluído com sucesso.'], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao excluir o pedido.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
