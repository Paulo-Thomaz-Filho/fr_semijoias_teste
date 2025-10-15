<?php
// Em: app/controls/Usuarios/atualizar.php

header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

require_once $rootPath . '/app/models/Usuario.php';
require_once $rootPath . '/app/models/UsuarioDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$idUsuario = $_POST['idUsuario'] ?? null;
$nome = $_POST['nome'] ?? null;
$email = $_POST['email'] ?? null;
$senha = $_POST['senha'] ?? null;

if (!$idUsuario) {
    http_response_code(400);
    echo json_encode(['erro' => 'O idUsuario é obrigatório para atualização.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!$nome || !$email) {
    http_response_code(400);
    echo json_encode(['erro' => 'Nome e email são obrigatórios.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $usuarioDAO = new \app\models\UsuarioDAO();
    $usuario = $usuarioDAO->getById($idUsuario);

    if (!$usuario) {
        http_response_code(404);
        echo json_encode(['erro' => 'Usuário não encontrado.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $usuario->setNome($nome);
    $usuario->setEmail($email);

    if (!empty($senha)) {
        $usuario->setSenha($senha);
    }

    if ($usuarioDAO->update($usuario)) {
        echo json_encode(['sucesso' => 'Usuário atualizado com sucesso!'], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao atualizar o usuário.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}