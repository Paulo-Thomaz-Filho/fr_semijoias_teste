<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/vendor/autoload.php';
require_once $rootPath . '/app/etc/config.php';
require_once $rootPath . '/app/models/Usuario.php';
require_once $rootPath . '/app/models/UsuarioDAO.php';
require_once $rootPath . '/app/core/utils/Mail.php';
require_once $rootPath . '/app/core/utils/EmailTemplate.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data);
    $token = $data->token ?? null;
} else {
    $token = $_GET['token'] ?? null;
}

if (!$token) {
    http_response_code(400);
    echo json_encode(['erro' => 'Token de ativação não fornecido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $usuarioDAO = new \app\models\UsuarioDAO();
    $usuario = $usuarioDAO->getByToken($token);

    if (!$usuario) {
        http_response_code(404);
        echo json_encode([
            'erro' => 'Token inválido ou expirado.',
            'mensagem' => 'O código de ativação não foi encontrado. Solicite um novo email de ativação.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Verificar se já está ativo
    if ($usuario->getStatus() === 'ativo') {
        http_response_code(200);
        echo json_encode([
            'sucesso' => 'Conta já estava ativa.',
            'mensagem' => 'Sua conta já foi ativada anteriormente. Você pode fazer login normalmente.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $usuario->setStatus('ativo');
    $usuario->setTokenAtivacao(null);
    
    if ($usuarioDAO->update($usuario)) {
        $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $linkLogin = $baseUrl . "/login";
        $nomeUsuario = $usuario->getNome();
        $emailUsuario = $usuario->getEmail();
        // Enviar email de conta ativada
        try {
            $corpoEmailAtivada = app\core\utils\EmailTemplate::emailContaAtivada($nomeUsuario, $linkLogin);
            $mailAtivada = new app\core\utils\Mail($emailUsuario, 'Conta Ativada - FR Semijoias', $corpoEmailAtivada);
            $mailAtivada->send();
        } catch (\Exception $e) {
            error_log("Erro ao enviar email de confirmação de ativação: " . $e->getMessage());
        }

        // Enviar email de boas-vindas
        try {
            $corpoEmailBoasVindas = app\core\utils\EmailTemplate::emailBoasVindas($nomeUsuario, $emailUsuario, $linkLogin);
            $mailBoasVindas = new app\core\utils\Mail($emailUsuario, 'Bem-vindo(a) à FR Semijoias!', $corpoEmailBoasVindas);
            $mailBoasVindas->send();
        } catch (\Exception $e) {
            error_log("Erro ao enviar email de boas-vindas: " . $e->getMessage());
        }

        echo json_encode([
            'sucesso' => 'Conta ativada com sucesso!',
            'mensagem' => 'Sua conta foi ativada. Agora você pode fazer login.'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao ativar a conta.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
