<?php
header('Content-Type: application/json');

require_once __DIR__.'/../../models/Usuario.php';
require_once __DIR__.'/../../models/UsuarioDAO.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['erro' => 'O ID do usuário é obrigatório.']);
    exit;
}

$usuarioDAO = new \app\models\UsuarioDAO();
$usuario = $usuarioDAO->getById($id);

if ($usuario) {
    echo json_encode($usuario->toArray());
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Usuário não encontrado.']);
}