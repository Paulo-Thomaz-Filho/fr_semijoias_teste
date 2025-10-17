<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

require_once $rootPath . '/app/models/Produto.php';
require_once $rootPath . '/app/models/ProdutoDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$idProduto = $_POST['idProduto'] ?? null;
$nome = $_POST['nome'] ?? null;
$descricao = $_POST['descricao'] ?? '';
$categoria = $_POST['categoria'] ?? null;
$marca = $_POST['marca'] ?? null;
$preco = $_POST['preco'] ?? null;
$idPromocao = (isset($_POST['idPromocao']) && $_POST['idPromocao'] !== '') ? $_POST['idPromocao'] : null;
$estoque = $_POST['estoque'] ?? 0;
$disponivel = $_POST['disponivel'] ?? 1;

if (!$idProduto) {
    http_response_code(400);
    echo json_encode(['erro' => 'O idProduto é obrigatório para atualização.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!$nome || !$categoria || !$marca || !$preco) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados incompletos. Nome, Categoria, Marca e Preço são obrigatórios.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $produtoDAO = new \app\models\ProdutoDAO();
    $produtoExistente = $produtoDAO->getById($idProduto);

    if (!$produtoExistente) {
        http_response_code(404);
        echo json_encode(['erro' => 'Produto não encontrado para atualização.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $produtoAtualizado = new \app\models\Produto(
        $idProduto,
        $nome,
        $descricao,
        $preco,
        $marca,
        $categoria,
        $idPromocao,
        null,
    $estoque,
        $disponivel
    );

    if ($produtoDAO->update($produtoAtualizado)) {
        echo json_encode(['sucesso' => 'Produto atualizado com sucesso!'], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao atualizar o produto.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
