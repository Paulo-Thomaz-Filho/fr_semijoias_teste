<?php
// Em: app/controls/Promocao/deletar.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__.'/../../models/PromocaoDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

$id = $_POST['id'] ?? null; // Lembre-se que o seu JS envia 'id'

if (!$id) {
    http_response_code(400);
    echo json_encode(['erro' => 'O ID da promoção é obrigatório para inativar.']);
    exit;
}

try {
    $promocaoDAO = new \app\models\PromocaoDAO();

    // Chama o novo método para INATIVAR
    if ($promocaoDAO->inativar($id)) {
        echo json_encode(['sucesso' => 'Promoção inativada com sucesso.']);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao inativar a promoção.']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Ocorreu um erro no servidor.', 'details' => $e->getMessage()]);
}