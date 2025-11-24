<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';
require_once $rootPath . '/app/models/Usuario.php';
require_once $rootPath . '/app/models/UsuarioDAO.php';

try {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data);

    if (!$data || !isset($data->email) || !isset($data->senha)) {
        http_response_code(400);
        echo json_encode(['erro' => 'E-mail e senha são obrigatórios.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $usuarioDAO = new \app\models\UsuarioDAO();
    $usuarioExistente = $usuarioDAO->getByEmail($data->email);

    // Verifica se o usuário existe E se a senha está correta
    if ($usuarioExistente && $usuarioExistente->verificarSenha($data->senha)) {
        
        // --- VERIFICAÇÃO DE STATUS ADICIONADA ---
        // Verifica se a conta do usuário já foi ativada
        if ($usuarioExistente->getStatus() !== 'ativo') {
            http_response_code(401); // Não autorizado
            echo json_encode(['erro' => 'Sua conta ainda não foi ativada. Por favor, verifique seu e-mail.'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        // --- FIM DA VERIFICAÇÃO ---

        // Se passou em ambas as verificações, cria a sessão
        $_SESSION['user_logged_in'] = true;
        $_SESSION['usuario_id'] = $usuarioExistente->getIdUsuario();
        $_SESSION['usuario_nome'] = $usuarioExistente->getNome();
        $_SESSION['usuario_acesso'] = $usuarioExistente->getAcesso();
        $_SESSION['usuario_nivel'] = $usuarioExistente->getIdNivel();

        $isAdmin = ($usuarioExistente->getIdNivel() == 1);

        if ($isAdmin) {
            $_SESSION['isAdmin'] = true;
        } else {
            $_SESSION['isAdmin'] = false;
        }

        echo json_encode([
            'sucesso' => true,
            'isAdmin' => $isAdmin,
            'usuario_nome' => $usuarioExistente->getNome()
        ], JSON_UNESCAPED_UNICODE);
        
    } else {
        // Se o usuário não existe OU a senha está errada
        http_response_code(401);
        echo json_encode(['erro' => 'E-mail ou senha inválidos.'], JSON_UNESCAPED_UNICODE);
    }

} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}