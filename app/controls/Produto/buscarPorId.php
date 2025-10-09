<?php
header('Content-Type: application/json');

require_once __DIR__.'/../../models/Produto.php';
require_once __DIR__.'/../../models/ProdutoDAO.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['erro' => 'O ID do produto é obrigatório.']);
    exit;
}

$produtoDAO = new \app\models\ProdutoDAO();
$produto = $produtoDAO->getById($id);

if ($produto) {
    echo json_encode($produto->toArray());
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Produto não encontrado.']);
}