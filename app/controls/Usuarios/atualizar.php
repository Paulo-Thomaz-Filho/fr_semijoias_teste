<?php
// Em: app/controls/Usuarios/atualizar.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__.'/../../models/Usuario.php';
require_once __DIR__.'/../../models/UsuarioDAO.php';

// 1. MUDANÇA: Buscar o ID a partir de $_POST, não mais de $_GET.
$id = $_POST['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['erro' => 'O ID do usuário é obrigatório.']);
    exit;
}

// 2. MUDANÇA: Buscar nome e email de $_POST.
$nome = $_POST['clienteNome'] ?? null;
$email = $_POST['clienteEmail'] ?? null;
$senha = $_POST['clienteSenha'] ?? null; // O nome do campo de senha no seu HTML é 'clienteSenha'

if (!$nome || !$email) {
    http_response_code(400);
    echo json_encode(['erro' => 'Nome e email são obrigatórios.']);
    exit;
}

try {
    $usuarioDAO = new \app\models\UsuarioDAO();
    $usuario = $usuarioDAO->getById($id);

    if (!$usuario) {
        http_response_code(404);
        echo json_encode(['erro' => 'Usuário não encontrado.']);
        exit;
    }

    // 3. Atualiza o objeto com os dados recebidos via $_POST
    $usuario->setNome($nome);
    $usuario->setEmail($email);

    // Se uma nova senha foi enviada, o método setSenha() já faz o hash
    if (!empty($senha)) {
        $usuario->setSenha($senha);
    }

    // 4. MUDANÇA no UsuarioDAO->update() para passar o objeto Usuario
    if ($usuarioDAO->update($usuario)) {
        echo json_encode(['sucesso' => 'Usuário atualizado com sucesso.']);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao atualizar o usuário.']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Ocorreu um erro no servidor.', 'details' => $e->getMessage()]);
}