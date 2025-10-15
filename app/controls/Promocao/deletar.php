<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

require_once $rootPath . '/app/models/Promocao.php';
require_once $rootPath . '/app/models/PromocaoDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$idPromocao = $_GET['id'] ?? $_GET['idPromocao'] ?? $_POST['id'] ?? $_POST['idPromocao'] ?? null;

if (!$idPromocao) {
    http_response_code(400);
    echo json_encode(['erro' => 'O idPromocao é obrigatório para inativar.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $promocaoDAO = new \app\models\PromocaoDAO();
    
    // Verificar se existe antes de inativar
    $promocaoExistente = $promocaoDAO->getById($idPromocao);
    if (!$promocaoExistente) {
        http_response_code(404);
        echo json_encode(['erro' => 'Promoção não encontrada.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($promocaoDAO->inativar($idPromocao)) {
        echo json_encode(['sucesso' => 'Promoção inativada com sucesso.'], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao inativar a promoção.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}