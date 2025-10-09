<?php
// Caminho: app/controls/Usuarios/salvar.php

define('APP_ENV', 'development');

header('Content-Type: application/json');

try {
    // Usando caminhos absolutos para segurança
    require_once dirname(__DIR__, 2) . '/models/Usuario.php';
    require_once dirname(__DIR__, 2) . '/models/UsuarioDAO.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Método não permitido.']);
        exit;
    }

    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data);
    
    if (!$data || !isset($data->nome) || !isset($data->email) || !isset($data->senha)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Dados incompletos ou em formato inválido.']);
        exit;
    }

    $usuarioDAO = new \app\models\UsuarioDAO();

    // 1. VERIFICAÇÃO DE E-MAIL DUPLICADO
    $usuarioExistente = $usuarioDAO->getByEmail($data->email);

    if ($usuarioExistente) {
        http_response_code(409); // Conflict
        echo json_encode(['success' => false, 'error' => 'Este e-mail já está cadastrado.']);
        exit;
    }

    // Se o e-mail não existe, prossiga com a criação
    $novoUsuario = new \app\models\Usuario();
    $novoUsuario->setNome($data->nome);
    $novoUsuario->setEmail($data->email);
    $novoUsuario->setSenha($data->senha);
    $novoUsuario->setAcesso($data->acesso ?? 'cliente');
    
    $idInserido = $usuarioDAO->insert($novoUsuario);
    
    if ($idInserido) {
        http_response_code(201);
        $novoUsuario->setId($idInserido);
        
        // 2. RESPOSTA DE SUCESSO CORRIGIDA
        // ANTES: echo json_encode($novoUsuario->toArray());
        // AGORA:
        echo json_encode([
            'success' => true,
            'message' => 'Cadastro realizado com sucesso!',
            'usuario' => $novoUsuario->toArray()
        ]);

    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Ocorreu um erro ao salvar o usuário.']);
    }

} catch (\Throwable $e) {
    http_response_code(500);
    if (defined('APP_ENV') && APP_ENV === 'development') {
        echo json_encode([
            'success' => false,
            'error' => 'Erro interno do servidor',
            'debug' => ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Ocorreu um erro interno no servidor.']);
    }
}