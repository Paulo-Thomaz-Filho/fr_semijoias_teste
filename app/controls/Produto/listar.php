<?php
header('Content-Type: application/json');

require_once __DIR__.'/../../models/Produto.php';
require_once __DIR__.'/../../models/ProdutoDAO.php';

try {
    $produtoDAO = new \app\models\ProdutoDAO();
    $produtos = $produtoDAO->getAll();

    $produtosArray = [];
    foreach ($produtos as $produto) {
        $produtosArray[] = $produto->toArray();
    }

    echo json_encode($produtosArray);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Ocorreu um erro no servidor.']);
}