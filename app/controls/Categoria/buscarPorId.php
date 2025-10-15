<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

require_once $rootPath . '/app/models/Categoria.php';
require_once $rootPath . '/app/models/CategoriaDAO.php';

$idCategoria = $_GET['id'] ?? $_GET['idCategoria'] ?? null;

if (!$idCategoria) {
    http_response_code(400);
    echo json_encode(['erro' => 'O idCategoria é obrigatório.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $categoriaDAO = new \app\models\CategoriaDAO();
    $categoria = $categoriaDAO->getById($idCategoria);

    if ($categoria) {
        echo json_encode($categoria->toArray(), JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(404);
        echo json_encode(['erro' => 'Categoria não encontrada.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}