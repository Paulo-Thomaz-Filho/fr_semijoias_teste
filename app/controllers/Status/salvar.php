<?php
header('Content-Type: application/json; charset=utf-8');

$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';
require_once $rootPath . '/app/models/StatusDAO.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['nome']) || empty(trim($data['nome']))) {
        http_response_code(400);
        echo json_encode(['erro' => 'Nome do status é obrigatório.']);
        exit;
    }
    $statusDAO = new \app\models\StatusDAO();
    $id = $statusDAO->salvar($data['nome']);
    echo json_encode(['id_status' => $id, 'nome' => $data['nome']]);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
