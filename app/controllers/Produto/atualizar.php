<?php
header('Content-Type: application/json; charset=utf-8');

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

// --- 2. RECEBER DADOS DO FORMULÁRIO (FormData) ---
$id_produto = $_POST['id_produto'] ?? null;
$nome = $_POST['nome'] ?? null;
$preco = $_POST['preco'] ?? null;
$marca = $_POST['marca'] ?? null;
$categoria = $_POST['categoria'] ?? null;
$descricao = $_POST['descricao'] ?? '';
$id_promocao = $_POST['id_promocao'] ?? null;
$estoque = $_POST['estoque'] ?? 0;
$disponivel = $_POST['disponivel'] ?? 1;

if (!$id_produto) {
    http_response_code(400);
    echo json_encode(['erro' => 'O id_produto é obrigatório para atualização.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $produtoDAO = new \app\models\ProdutoDAO();
    $produtoExistente = $produtoDAO->getById($id_produto);

    if (!$produtoExistente) {
        http_response_code(404);
        echo json_encode(['erro' => 'Produto não encontrado para atualização.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Pega o caminho da imagem antiga
    $caminho_imagem_salva = $produtoExistente->get_caminho_imagem(); 

    // --- 3. PROCESSAR O ARQUIVO DE IMAGEM (se um NOVO foi enviado) ---
    if (isset($_FILES['caminho_imagem']) && $_FILES['caminho_imagem']['error'] == 0) {
        try {
            $file = $_FILES['caminho_imagem'];
            $uploadPathDir = $rootPath . '/app/uploads/';

            if ($caminho_imagem_salva && file_exists($uploadPathDir . $caminho_imagem_salva)) {
                 unlink($uploadPathDir . $caminho_imagem_salva);
            }

            $tempPath = $file['tmp_name'];
            $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
            
            $fileBase64Name = "produto_" . uniqid() . date('YmdHis') . ".{$fileExtension}.base64";
            $caminho_completo = $uploadPathDir . $fileBase64Name;

            $base64Files = new \core\utils\Base64Files();
            $fileContent = $base64Files->fileToBase64($tempPath);
            $base64Files->base64ToFile($fileContent, $caminho_completo);

            $caminho_imagem_salva = $fileBase64Name;

        } catch (Exception $e) {
            http_response_code(500); 
            echo json_encode(['erro' => 'Falha ao processar a nova imagem.', 'details' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    // --- 4. ATUALIZAR O OBJETO PRODUTO ---
    $produtoExistente->setNome($nome);
    $produtoExistente->setDescricao($descricao);
    $produtoExistente->setPreco($preco);
    $produtoExistente->setMarca($marca);
    $produtoExistente->setCategoria($categoria);
    $produtoExistente->setIdPromocao($id_promocao ?: null);
    $produtoExistente->setCaminhoImagem($caminho_imagem_salva); 
    $produtoExistente->setEstoque($estoque);
    $produtoExistente->setDisponivel($disponivel);

    // --- ATUALIZAR NO BANCO ---
    $produtoDAO->update($produtoExistente);
    echo json_encode(['sucesso' => 'Produto atualizado com sucesso!'], JSON_UNESCAPED_UNICODE);
    
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro interno: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_UNESCAPED_UNICODE);
}