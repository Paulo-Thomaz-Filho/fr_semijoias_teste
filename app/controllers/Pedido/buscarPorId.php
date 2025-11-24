<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';
require_once $rootPath . '/app/models/Pedido.php';
require_once $rootPath . '/app/models/PedidoDAO.php';

$idPedido = $_GET['id'] ?? $_GET['idPedido'] ?? null;

if (!$idPedido) {
    http_response_code(400);
    echo json_encode(['erro' => 'O idPedido é obrigatório.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $pedidoDAO = new \app\models\PedidoDAO();
    $pedido = $pedidoDAO->getById($idPedido);

    if ($pedido) {
        // Envia o array diretamente
        echo json_encode($pedido, JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(404);
        echo json_encode(['erro' => 'Pedido não encontrado.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
