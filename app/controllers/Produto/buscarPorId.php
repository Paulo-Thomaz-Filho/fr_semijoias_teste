<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';
require_once $rootPath . '/app/models/Produto.php';
require_once $rootPath . '/app/models/ProdutoDAO.php';

$idProduto = $_GET['id'] ?? $_GET['idProduto'] ?? null;

if (!$idProduto) {
    http_response_code(400);
    echo json_encode(['erro' => 'O idProduto é obrigatório.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $produtoDAO = new \app\models\ProdutoDAO();
    $produto = $produtoDAO->getById($idProduto);

    if ($produto) {
        echo json_encode($produto->toArray(), JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(404);
        echo json_encode(['erro' => 'Produto não encontrado.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
