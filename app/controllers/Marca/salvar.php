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

$nome = $_POST['nome'] ?? null;

if (!$nome) {
    http_response_code(400);
    echo json_encode(['erro' => 'O nome da marca é obrigatório.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $novaMarca = new \app\models\Marca();
    $novaMarca->setNome($nome);

    $marcaDAO = new \app\models\MarcaDAO();
    $idInserido = $marcaDAO->insert($novaMarca);

    if ($idInserido) {
        http_response_code(201);
        echo json_encode(['sucesso' => 'Marca salva com sucesso!', 'idMarca' => $idInserido], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Ocorreu um erro ao salvar a marca.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
