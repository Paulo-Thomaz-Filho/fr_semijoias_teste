<?php
header('Content-Type: application/json; charset=utf-8');

$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';
require_once $rootPath . '/app/models/Promocao.php';
require_once $rootPath . '/app/models/PromocaoDAO.php';

$idPromocao = $_GET['id'] ?? $_GET['idPromocao'] ?? null;

if (!$idPromocao) {
    http_response_code(400);
    echo json_encode(['erro' => 'O idPromocao é obrigatório.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $promocaoDAO = new \app\models\PromocaoDAO();
    $promocao = $promocaoDAO->getById($idPromocao);

    if ($promocao) {
        echo json_encode($promocao->toArray(), JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(404);
        echo json_encode(['erro' => 'Promoção não encontrada.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
