<?php
define('APP_ENV', 'development');

error_reporting(0); 
ini_set('display_errors', 0);

header('Content-Type: application/json');

try {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    require_once __DIR__.'/../../models/Usuario.php';
    require_once __DIR__.'/../../models/UsuarioDAO.php';

    // Verifica se o método da requisição é POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Método não permitido.']);
        exit;
    }

    // Lê os dados JSON da requisição
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data);

    if (!$data || !isset($data->email) || !isset($data->senha)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'E-mail e senha são obrigatórios.']);
        exit;
    }
    
    // Sanitiza o e-mail
    $usuarioDAO = new \app\models\UsuarioDAO();
    $usuario = $usuarioDAO->getByEmail($data->email);

    // Verifica se o usuário existe e se a senha está correta
    if ($usuario && $usuario->verificarSenha($data->senha)) {
        
        $_SESSION['usuario_id'] = $usuario->getId();
        $_SESSION['usuario_nome'] = $usuario->getNome();
        $_SESSION['usuario_acesso'] = $usuario->getAcesso();

        $isAdmin = ($usuario->getAcesso() === 'admin');
        
        echo json_encode([
            'success' => true,
            'isAdmin' => $isAdmin
        ]);

    } else {
        http_response_code(401); // Unauthorized
        echo json_encode(['success' => false, 'error' => 'E-mail ou senha inválidos.']);
    }

} catch (\Throwable $e) { // O \Throwable pega qualquer tipo de erro ou exceção
    
    http_response_code(500); // Internal Server Error

    // Verifica se estamos em ambiente de desenvolvimento
    if (defined('APP_ENV') && APP_ENV === 'development') {
        // Se sim, mostra o erro detalhado
        echo json_encode([
            'success' => false,
            'error' => 'Erro interno do servidor',
            'debug' => [
                'message' => $e->getMessage(), // A mensagem do erro
                'file' => $e->getFile(),         // O arquivo onde o erro ocorreu
                'line' => $e->getLine()          // A linha do erro
            ]
        ]);
    } else {
        // Se não (estamos em produção), mostra a mensagem genérica
        echo json_encode(['success' => false, 'error' => 'Ocorreu um erro interno no servidor.']);
    }
}