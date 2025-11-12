<?php
header('Content-Type: application/json; charset=utf-8');

$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

// --- ARQUIVOS NECESSÁRIOS ---
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

$nome            = $data->nome            ?? null;
$email           = $data->email           ?? null;
$senha           = $data->senha           ?? null;
$id_nivel        = $data->id_nivel        ?? 2;    // 2 para novos clientes

// --- CORREÇÃO 2: Ajustar a validação para os campos que realmente vêm do JS ---
if (!$nome || !$email || !$senha) {
    http_response_code(400);
    // Mensagem mais simples, pois os outros campos não vêm do cadastro
    echo json_encode(['erro' => 'Dados incompletos. Nome, email e senha são obrigatórios.'], JSON_UNESCAPED_UNICODE);
    exit;
}
// --- Fim da Correção 2 ---

try {
    $usuarioDAO = new \app\models\UsuarioDAO();
    if ($usuarioDAO->getByEmail($email)) {
        http_response_code(409);
        echo json_encode(['erro' => 'E-mail já cadastrado.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 1. GERAR O TOKEN DE ATIVAÇÃO
    $generator = new app\core\utils\CodeGenerator();
    $token = $generator->run(6); // Gera um código aleatório de 64 caracteres

    $novoUsuario = new \app\models\Usuario();
    $novoUsuario->setNome($nome);
    $novoUsuario->setEmail($email);
    $novoUsuario->setSenha(password_hash($senha, PASSWORD_DEFAULT));
    $novoUsuario->setIdNivel($id_nivel);
    
    // 2. DEFINIR O STATUS COMO PENDENTE E SALVAR O TOKEN
    $novoUsuario->setStatus('pendente');
    $novoUsuario->setTokenAtivacao($token);
    
    $idInserido = $usuarioDAO->insert($novoUsuario);
    
    if ($idInserido) {
        // 3. SE SALVOU NO BANCO, ENVIAR O E-MAIL DE ATIVAÇÃO
        // URL base do sistema (ajuste conforme seu ambiente)
        $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $linkDeAtivacao = $baseUrl . "/ativar?token=" . $token;
        
        // Gerar o HTML do email usando o template profissional
        $corpoEmail = app\core\utils\EmailTemplate::emailAtivacaoConta($nome, $linkDeAtivacao, $token);
        
        // Enviar email usando PHPMailer
        try {
            $mail = new app\core\utils\Mail($email, 'Ative sua conta - FR Semijoias', $corpoEmail);
            $mail->send();
            
            http_response_code(201);
            echo json_encode([
                'sucesso' => 'Cadastro realizado com sucesso!', 
                'mensagem' => 'Enviamos um email para ' . $email . ' com as instruções para ativar sua conta.'
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            // Mesmo que o email falhe, o usuário foi cadastrado
            http_response_code(201);
            echo json_encode([
                'sucesso' => 'Cadastro realizado!',
                'aviso' => 'Não foi possível enviar o email de ativação. Entre em contato com o suporte.',
                'erro_email' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao cadastrar o usuário.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}