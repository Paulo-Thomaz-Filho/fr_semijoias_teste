<?php
header('Content-Type: application/json');

require_once __DIR__.'/../../models/Pedido.php';
require_once __DIR__.'/../../models/PedidoDAO.php';

try {
    $pedidoDAO = new \app\models\PedidoDAO();
    $pedidos = $pedidoDAO->getAll();

    $pedidosArray = [];
    foreach ($pedidos as $pedido) {
        $pedidosArray[] = $pedido->toArray();
    }

    echo json_encode($pedidosArray);

} catch (\Throwable $e) { 
    http_response_code(500);

    // Força a exibição do erro detalhado para descobrirmos a causa
    echo json_encode([
        'success' => false,
        'error' => 'Ocorreu um erro interno no servidor (MODO DEBUG ATIVADO)',
        'debug_message' => $e->getMessage(), // A mensagem real do erro
        'debug_file' => $e->getFile(),         // O arquivo onde o erro ocorreu
        'debug_line' => $e->getLine()          // A linha exata do erro
    ]);
} 