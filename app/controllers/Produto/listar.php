<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

require_once $rootPath . '/app/models/Produto.php';
require_once $rootPath . '/app/models/ProdutoDAO.php';

try {
    $produtoDAO = new \app\models\ProdutoDAO();
    $produtos = $produtoDAO->getAllWithDetails();

    echo json_encode($produtos, JSON_UNESCAPED_UNICODE);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
