<?php
// Em: app/controls/Usuarios/deletar.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__.'/../../models/Usuario.php';
require_once __DIR__.'/../../models/UsuarioDAO.php';

$id = $_POST['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['erro' => 'O ID do usuário é obrigatório.']);
    exit;
}

try {
    $usuarioDAO = new \app\models\UsuarioDAO();
    
    // Chama o novo método para INATIVAR em vez de DELETAR
    if ($usuarioDAO->inativar($id)) {
        echo json_encode(['sucesso' => 'Usuário inativado com sucesso.']);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao inativar o usuário.']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Ocorreu um erro no servidor.', 'details' => $e->getMessage()]);
}