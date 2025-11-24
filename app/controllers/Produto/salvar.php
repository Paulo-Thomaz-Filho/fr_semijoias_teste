<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';
require_once $rootPath . '/app/models/Produto.php';
require_once $rootPath . '/app/models/ProdutoDAO.php';
require_once $rootPath . '/app/core/utils/Base64Files.php';

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
$id_promocao = $_POST['id_promocao'] ?? null;
$estoque = $_POST['estoque'] ?? 0;
$disponivel = $_POST['disponivel'] ?? 1;

if (!$nome || !$preco || !$marca || !$categoria) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados incompletos. Nome, Preço, Marca e Categoria são obrigatórios.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$caminho_imagem_salva = null;

if (isset($_FILES['caminho_imagem']) && $_FILES['caminho_imagem']['error'] == 0) {
    try {
        $file = $_FILES['caminho_imagem'];
        $uploadPathDir = $rootPath . '/public/assets/images/'; // Caminho para sua pasta
        
        $tempPath = $file['tmp_name'];
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        
        $fileBase64Name = "produto_" . uniqid() . date('YmdHis') . ".{$fileExtension}.base64";
        $caminho_completo = $uploadPathDir . $fileBase64Name;

        // Usa sua classe Base64Files para converter e salvar
        $base64Files = new \core\utils\Base64Files();
        $fileContent = $base64Files->fileToBase64($tempPath);
        $base64Files->base64ToFile($fileContent, $caminho_completo);

        $caminho_imagem_salva = $fileBase64Name; 

    } catch (Exception $e) {
        http_response_code(500); 
        echo json_encode(['erro' => 'Falha ao processar a imagem.', 'details' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

try {
    $novoProduto = new \app\models\Produto();
    $novoProduto->setNome($nome);
    $novoProduto->setDescricao($descricao);
    $novoProduto->setPreco($preco);
    $novoProduto->setMarca($marca);
    $novoProduto->setCategoria($categoria);
    $novoProduto->setEstoque($estoque);
    $novoProduto->setDisponivel($disponivel);
    $novoProduto->setIdPromocao($id_promocao ?: null); 
    $novoProduto->setCaminhoImagem($caminho_imagem_salva); 

    $produtoDAO = new \app\models\ProdutoDAO();
    $idInserido = $produtoDAO->insert($novoProduto);

    if ($idInserido) {
        http_response_code(201); 
        echo json_encode(['sucesso' => 'Produto salvo com sucesso!', 'id' => $idInserido], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Ocorreu um erro ao salvar o produto.'], JSON_UNESCAPED_UNICODE);
    }

} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro interno: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_UNESCAPED_UNICODE);
}
