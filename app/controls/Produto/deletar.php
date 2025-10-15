<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

require_once __DIR__.'/../../models/Produto.php';
require_once __DIR__.'/../../models/ProdutoDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$idProduto = $_GET['idProduto'] ?? $_POST['idProduto'] ?? null;

if (!$idProduto) {
    http_response_code(400);
    echo json_encode(['erro' => 'O idProduto é obrigatório para exclusão.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $produtoDAO = new \app\models\ProdutoDAO();
    $produtoExistente = $produtoDAO->getById($idProduto);

    if (!$produtoExistente) {
        http_response_code(404);
        echo json_encode(['erro' => 'Produto não encontrado para exclusão.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($produtoDAO->delete($idProduto)) {
        echo json_encode(['sucesso' => 'Produto excluído com sucesso.'], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao excluir o produto.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}