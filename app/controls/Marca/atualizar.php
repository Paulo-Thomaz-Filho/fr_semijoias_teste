<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

require_once $rootPath . '/app/models/Marca.php';
require_once $rootPath . '/app/models/MarcaDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$idMarca = $_POST['idMarca'] ?? null;
$nome = $_POST['nome'] ?? null;

if (!$idMarca) {
    http_response_code(400);
    echo json_encode(['erro' => 'O idMarca é obrigatório para atualização.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!$nome) {
    http_response_code(400);
    echo json_encode(['erro' => 'O nome é obrigatório.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $marcaDAO = new \app\models\MarcaDAO();
    $marcaExistente = $marcaDAO->getById($idMarca);

    if (!$marcaExistente) {
        http_response_code(404);
        echo json_encode(['erro' => 'Marca não encontrada para atualização.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $marcaExistente->setNome($nome);

    if ($marcaDAO->update($marcaExistente)) {
        echo json_encode(['sucesso' => 'Marca atualizada com sucesso!'], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Ocorreu um erro ao atualizar a marca.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
