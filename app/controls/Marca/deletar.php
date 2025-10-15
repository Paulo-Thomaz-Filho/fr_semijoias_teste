<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

require_once $rootPath . '/app/models/Marca.php';
require_once $rootPath . '/app/models/MarcaDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$idMarca = $_GET['idMarca'] ?? $_POST['idMarca'] ?? null;

if (!$idMarca) {
    http_response_code(400);
    echo json_encode(['erro' => 'O idMarca é obrigatório para exclusão.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $marcaDAO = new \app\models\MarcaDAO();
    
    // Verificar se existe antes de deletar
    $marcaExistente = $marcaDAO->getById($idMarca);
    if (!$marcaExistente) {
        http_response_code(404);
        echo json_encode(['erro' => 'Marca não encontrada.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($marcaDAO->delete($idMarca)) {
        echo json_encode(['sucesso' => 'Marca excluída com sucesso.'], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Ocorreu um erro ao deletar a marca.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}