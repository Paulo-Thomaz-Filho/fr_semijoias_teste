<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

require_once $rootPath . '/app/models/Marca.php';
require_once $rootPath . '/app/models/MarcaDAO.php';

$idMarca = $_GET['id'] ?? $_GET['idMarca'] ?? null;

if (!$idMarca) {
    http_response_code(400);
    echo json_encode(['erro' => 'O idMarca é obrigatório.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $marcaDAO = new \app\models\MarcaDAO();
    $marca = $marcaDAO->getById($idMarca);

    if ($marca) {
        echo json_encode($marca->toArray(), JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(404);
        echo json_encode(['erro' => 'Marca não encontrada.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}