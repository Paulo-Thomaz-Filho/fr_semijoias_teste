<?php
// /app/controllers/Usuario/solicitarRedefinicaoSenha.php
require_once __DIR__ . '/../../core/database/DBConnection.php';
use core\database\DBConnection;
require_once __DIR__ . '/../../core/utils/EmailTemplate.php';
require_once __DIR__ . '/../../core/utils/Mail.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$email = isset($data['email']) ? trim($data['email']) : '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'E-mail inválido']);
    exit;
}


$dbObj = new DBConnection();
$db = $dbObj->getConn();

$stmt = $db->prepare('SELECT id_usuario, nome FROM usuarios WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$user) {
    // Não revela se o email existe, mas retorna mensagem genérica
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'mensagem' => 'Se o e-mail estiver cadastrado, você receberá as instruções para redefinir sua senha.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$token = bin2hex(random_bytes(32));
$expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

// Salva token na tabela de redefinição (crie se não existir)
$stmt = $db->prepare('INSERT INTO redefinicao_senha (usuario_id, token, expira) VALUES (?, ?, ?)');
$stmt->execute([$user['id_usuario'], $token, $expires]);

// Monta link correto para redefinição de senha
$link = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/redefinir-senha?token=' . $token;



try {
    $subject = 'Recuperação de senha - FR Semijoias';
    $body = \app\core\utils\EmailTemplate::emailRecuperacaoSenha($user['nome'], $link, $token);
    $mail = new \app\core\utils\Mail($email, $subject, $body);
    if ($mail->send()) {
        http_response_code(200);
        echo json_encode([
            'sucesso' => 'Solicitação enviada!',
            'mensagem' => 'Enviamos um e-mail para ' . $email . ' com as instruções para redefinir sua senha.'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        error_log('Erro ao enviar e-mail de recuperação: ' . $mail->getError());
        http_response_code(500);
        echo json_encode([
            'erro' => 'Erro ao enviar e-mail de recuperação de senha.',
            'mensagem' => $mail->getError()
        ], JSON_UNESCAPED_UNICODE);
    }
} catch (\Exception $e) {
    error_log('Erro ao enviar e-mail de recuperação: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro ao enviar e-mail de recuperação de senha.',
        'mensagem' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
