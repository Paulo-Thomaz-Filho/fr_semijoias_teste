<?php
define('APP_ENV', 'development');

error_reporting(0); 
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

try {
    require_once $rootPath . '/app/models/Usuario.php';
    require_once $rootPath . '/app/models/UsuarioDAO.php';

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

    if ($usuarioExistente && $usuarioExistente->verificarSenha($data->senha)) {
        $_SESSION['user_logged_in'] = true;
        $_SESSION['usuario_id'] = $usuarioExistente->getIdUsuario();
        $_SESSION['usuario_nome'] = $usuarioExistente->getNome();
        $_SESSION['usuario_acesso'] = $usuarioExistente->getAcesso();

        $isAdmin = ($usuarioExistente->getAcesso() === 'admin');

        echo json_encode([
            'sucesso' => true,
            'isAdmin' => $isAdmin,
            'usuario_nome' => $usuarioExistente->getNome()
        ], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(401);
        echo json_encode(['erro' => 'E-mail ou senha inválidos.'], JSON_UNESCAPED_UNICODE);
    }

} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
