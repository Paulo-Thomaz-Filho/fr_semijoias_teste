<?php
// Ficheiro: app/controls/Dashboard/estatisticas.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__.'/../../models/PedidoDAO.php';
require_once __DIR__.'/../../models/UsuarioDAO.php';

try {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $pedidoDAO = new \app\models\PedidoDAO();
    $usuarioDAO = new \app\models\UsuarioDAO();

    // Chama os novos métodos otimizados
    $totalGanhos = $pedidoDAO->getTotalGanhos();
    $totalVendidos = $pedidoDAO->getTotalVendas();
    $totalCadastrados = $usuarioDAO->getTotalCadastrados();

    // Monta o objeto de resposta
    $resposta = [
        'totalGanhos' => (float) $totalGanhos,
        'totalVendidos' => (int) $totalVendidos,
        'totalCadastrados' => (int) $totalCadastrados
    ];

    echo json_encode($resposta, JSON_UNESCAPED_UNICODE);

} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar estatísticas', 'details' => $e->getMessage()]);
}