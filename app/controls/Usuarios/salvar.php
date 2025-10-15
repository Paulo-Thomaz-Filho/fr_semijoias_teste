<?php
// Caminho: app/controls/Usuarios/salvar.php

define('APP_ENV', 'development');

header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

require_once $rootPath . '/app/models/Usuario.php';
require_once $rootPath . '/app/models/UsuarioDAO.php';

try {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $nome = $_POST['nome'] ?? null;
    $email = $_POST['email'] ?? null;
    $senha = $_POST['senha'] ?? null;
    $acesso = $_POST['acesso'] ?? 'cliente';
    
    if (!$nome || !$email || !$senha) {
        http_response_code(400);
        echo json_encode(['erro' => 'Dados incompletos. Nome, email e senha são obrigatórios.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $usuarioDAO = new \app\models\UsuarioDAO();

    // Verificação de e-mail duplicado
    $usuarioExistente = $usuarioDAO->getByEmail($email);

    if ($usuarioExistente) {
        http_response_code(409);
        echo json_encode(['erro' => 'Este e-mail já está cadastrado.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $novoUsuario = new \app\models\Usuario();
    $novoUsuario->setNome($nome);
    $novoUsuario->setEmail($email);
    $novoUsuario->setSenha($senha);
    $novoUsuario->setAcesso($acesso);
    
    $idInserido = $usuarioDAO->insert($novoUsuario);
    
    if ($idInserido) {
        http_response_code(201);
        echo json_encode(['sucesso' => 'Usuário salvo com sucesso!', 'id' => $idInserido], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Ocorreu um erro ao salvar o usuário.'], JSON_UNESCAPED_UNICODE);
    }

} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}