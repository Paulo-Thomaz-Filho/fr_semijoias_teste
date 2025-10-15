<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

require_once __DIR__.'/../../models/Produto.php';
require_once __DIR__.'/../../models/ProdutoDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$nome = $_POST['nome'] ?? null;
$preco = $_POST['preco'] ?? null;
$marca = $_POST['marca'] ?? null;
$categoria = $_POST['categoria'] ?? null;
$descricao = $_POST['descricao'] ?? '';
$idPromocao = (isset($_POST['idPromocao']) && $_POST['idPromocao'] !== '') ? $_POST['idPromocao'] : null;
$unidade_estoque = $_POST['unidade_estoque'] ?? 0;
$disponivel = $_POST['disponivel'] ?? 1;

if (!$nome || !$preco || !$marca || !$categoria) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados incompletos. Nome, Preço, Marca e Categoria são obrigatórios.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $novoProduto = new \app\models\Produto(
        null,
        $nome,
        $descricao,
        $preco,
        $marca,
        $categoria,
        $idPromocao,
        null,
        $unidade_estoque,
        $disponivel
    );

    $produtoDAO = new \app\models\ProdutoDAO();
    $idInserido = $produtoDAO->insert($novoProduto);

    if ($idInserido) {
        http_response_code(201);
        echo json_encode(['sucesso' => 'Produto salvo com sucesso!', 'id' => $idInserido], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao salvar o produto.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}