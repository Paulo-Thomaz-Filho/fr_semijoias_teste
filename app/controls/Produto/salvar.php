<?php
header('Content-Type: application/json');

require_once __DIR__.'/../../models/Produto.php';
require_once __DIR__.'/../../models/ProdutoDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

$nome = $_POST['nome'] ?? null;
$valor = $_POST['valor'] ?? null;
$marca = $_POST['marca'] ?? null;
$categoria = $_POST['categoria'] ?? null;
$descricao = $_POST['descricao'] ?? '';
$idPromocao = $_POST['idPromocao'] ?? null;

if (!$nome || !$valor || !$marca || !$categoria) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados incompletos. Nome, Valor, Marca e Categoria são obrigatórios.']);
    exit;
}

$novoProduto = new \app\models\Produto(
    null,
    $nome,
    $descricao,
    $valor,
    $marca,
    $categoria,
    $idPromocao ?: null 
);

$produtoDAO = new \app\models\ProdutoDAO();
$idInserido = $produtoDAO->insert($novoProduto);

if ($idInserido) {
    http_response_code(201);
    // Para retornar o objeto completo, precisaríamos buscá-lo novamente ou construir manualmente
    echo json_encode(['sucesso' => 'Produto salvo com sucesso!', 'id' => $idInserido]);
} else {
    http_response_code(500);
    echo json_encode(['erro' => 'Ocorreu um erro ao salvar o produto.']);
}