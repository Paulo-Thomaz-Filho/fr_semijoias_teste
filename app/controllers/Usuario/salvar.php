<?php
header('Content-Type: application/json; charset=utf-8');

$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

// --- ARQUIVOS NECESSÁRIOS ---
require_once $rootPath . '/app/models/Usuario.php';
require_once $rootPath . '/app/models/UsuarioDAO.php';
require_once $rootPath . '/app/core/utils/CodeGenerator.php'; // <-- SUA CLASSE
require_once $rootPath . '/app/core/utils/Mail.php';          // <-- SUA CLASSE

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

// Validação de senha forte (seu código já estava correto)
$senhaForteRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};':\"\\|,.<>\/?]).{8,}$/";
if (!preg_match($senhaForteRegex, $senha)) {
    http_response_code(400);
    echo json_encode(['erro' => 'A senha deve ter pelo menos 8 caracteres, incluindo uma letra maiúscula, uma minúscula e um caractere especial.'], JSON_UNESCAPED_UNICODE);
    exit;
}

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
        // 3. SE SALVOU NO BANCO, ENVIAR O E-MAIL
        // ATENÇÃO: Altere 'localhost/FR_Semijoias' para o seu domínio real
        $linkDeAtivacao = "http://localhost/FR_Semijoias/public/ativar.php?token=" . $token;
        
        $corpoEmail = "
            <h1>Bem-vindo à FR Semijoias, {$nome}!</h1>
            <p>Seu cadastro foi realizado. Por favor, clique no botão abaixo para ativar sua conta:</p>
            <a href='{$linkDeAtivacao}' 
               style='background-color: #0d6efd; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>
               Ativar Minha Conta
            </a>
            <br><br>
            <small>Se o botão não funcionar, copie e cole este link no seu navegador: {$linkDeAtivacao}</small>
        ";
        
        // Use sua classe Mail
        $mail = new app\core\utils\Mail($email, 'Ative sua conta - FR Semijoias', $corpoEmail);
        $mail->addHeader('From: paulinhothomazfilho@gmail.com'); // Mude para seu e-mail
        $mail->send();

        http_response_code(201);
        echo json_encode(['sucesso' => 'Cadastro realizado! Por favor, verifique seu e-mail para ativar a conta.'], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao cadastrar o usuário.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}