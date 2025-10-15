<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

require_once $rootPath . '/app/models/Categoria.php';
require_once $rootPath . '/app/models/CategoriaDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$nome = $_POST['nome'] ?? null;

if (!$nome) {
    http_response_code(400);
    echo json_encode(['erro' => 'O nome da categoria é obrigatório.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $novaCategoria = new \app\models\Categoria();
    $novaCategoria->setNome($nome);

    $categoriaDAO = new \app\models\CategoriaDAO();
    $idInserido = $categoriaDAO->insert($novaCategoria);

    if ($idInserido) {
        http_response_code(201);
        echo json_encode(['sucesso' => 'Categoria salva com sucesso!', 'id' => $idInserido], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Ocorreu um erro ao salvar a categoria.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}