<?php
header('Content-Type: application/json; charset=utf-8');

$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/vendor/autoload.php';
require_once $rootPath . '/app/etc/config.php';
require_once $rootPath . '/app/models/Usuario.php';
require_once $rootPath . '/app/models/UsuarioDAO.php';
require_once $rootPath . '/app/core/utils/CodeGenerator.php';
require_once $rootPath . '/app/core/utils/Mail.php';
require_once $rootPath . '/app/core/utils/EmailTemplate.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$json_data = file_get_contents('php://input');
$data = json_decode($json_data);
$email = $data->email ?? null;

if (!$email) {
    http_response_code(400);
    echo json_encode(['erro' => 'E-mail não fornecido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $usuarioDAO = new \app\models\UsuarioDAO();
    $usuario = $usuarioDAO->getByEmail($email);

    if (!$usuario) {
        http_response_code(404);
        echo json_encode(['erro' => 'E-mail não encontrado.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Verificar se já está ativo
    if ($usuario->getStatus() === 'ativo') {
        http_response_code(400);
        echo json_encode([
            'erro' => 'Conta já está ativa.',
            'mensagem' => 'Sua conta já foi ativada. Você pode fazer login normalmente.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Gerar novo token
    $generator = new app\core\utils\CodeGenerator();
    $novoToken = $generator->run(6);
    
    $usuario->setTokenAtivacao($novoToken);
    
    if ($usuarioDAO->update($usuario)) {
        // Enviar novo email de ativação
        $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $linkDeAtivacao = $baseUrl . "/ativar?token=" . $novoToken;
        
        $corpoEmail = app\core\utils\EmailTemplate::emailAtivacaoConta($usuario->getNome(), $linkDeAtivacao, $novoToken);
        
        try {
            $mail = new app\core\utils\Mail($email, 'Ative sua conta - FR Semijoias', $corpoEmail);
            $mail->send();
            
            http_response_code(200);
            echo json_encode([
                'sucesso' => 'Email reenviado com sucesso!',
                'mensagem' => 'Enviamos um novo email de ativação para ' . $email
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'erro' => 'Erro ao enviar email: ' . $e->getMessage(),
                'mensagem' => 'Não foi possível enviar o email de ativação.'
            ], JSON_UNESCAPED_UNICODE);
        }
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao atualizar token.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
