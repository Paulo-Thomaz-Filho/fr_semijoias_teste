<?php
header('Content-Type: application/json');

require_once __DIR__.'/../../models/Promocao.php';
require_once __DIR__.'/../../models/PromocaoDAO.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['erro' => 'O ID da promoção é obrigatório.']);
    exit;
}

$promocaoDAO = new \app\models\PromocaoDAO();
$promocao = $promocaoDAO->getById($id);

if ($promocao) {
    echo json_encode($promocao->toArray());
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Promoção não encontrada.']);
}