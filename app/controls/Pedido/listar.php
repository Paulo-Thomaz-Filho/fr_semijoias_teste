<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

require_once $rootPath . '/app/models/Pedido.php';
require_once $rootPath . '/app/models/PedidoDAO.php';

try {
    $pedidoDAO = new \app\models\PedidoDAO();
    
    // Verifica se quer apenas a lista de status
    if (isset($_GET['only_status']) && $_GET['only_status'] === 'true') {
        $status = $pedidoDAO->getAllStatus();
        echo json_encode($status, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Verifica se há filtro por status
    $status = $_GET['status'] ?? null;
    
    if ($status) {
        $pedidos = $pedidoDAO->getAllByStatus($status);
    } else {
        $pedidos = $pedidoDAO->getAll();
    }

    $pedidosArray = [];
    foreach ($pedidos as $pedido) {
        $pedidosArray[] = $pedido->toArray();
    }

    echo json_encode($pedidosArray, JSON_UNESCAPED_UNICODE);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
} 