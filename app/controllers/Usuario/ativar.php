<?php
// Em: public/ativar.php

$rootPath = dirname(__DIR__);
require_once $rootPath . '/app/etc/config.php';
require_once $rootPath . '/app/models/Usuario.php';
require_once $rootPath . '/app/models/UsuarioDAO.php';

$token = $_GET['token'] ?? null;

if (!$token) {
    die("Token de ativação não fornecido.");
}

try {
    $usuarioDAO = new \app\models\UsuarioDAO();
    
    // 1. Busca o usuário pelo token (Método novo - veja abaixo)
    $usuario = $usuarioDAO->getByToken($token);

    if ($usuario) {
        // 2. Se encontrou, ativa a conta e limpa o token
        $usuario->setStatus('ativo');
        $usuario->setTokenAtivacao(null); // Token de uso único
        
        $usuarioDAO->update($usuario);
        
        // 3. Redireciona para o login com mensagem de sucesso
        // (Assumindo que sua página de login está em /login)
        header('Location: login?sucesso=ativado');
        exit;
    } else {
        // Redireciona para o login com mensagem de erro
        header('Location: login?erro=token');
        exit;
    }
} catch (\Throwable $e) {
    die("Ocorreu um erro ao ativar sua conta: " . $e->getMessage());
}