<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

require_once $rootPath . '/app/models/Categoria.php';
require_once $rootPath . '/app/models/CategoriaDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$idCategoria = $_GET['id'] ?? $_GET['idCategoria'] ?? $_POST['id'] ?? $_POST['idCategoria'] ?? null;

if (!$idCategoria) {
    http_response_code(400);
    echo json_encode(['erro' => 'O idCategoria é obrigatório para exclusão.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $categoriaDAO = new \app\models\CategoriaDAO();
    
    // Verificar se existe antes de deletar
    $categoriaExistente = $categoriaDAO->getById($idCategoria);
    if (!$categoriaExistente) {
        http_response_code(404);
        echo json_encode(['erro' => 'Categoria não encontrada.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($categoriaDAO->delete($idCategoria)) {
        echo json_encode(['sucesso' => 'Categoria excluída com sucesso.'], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Ocorreu um erro ao deletar a categoria.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}