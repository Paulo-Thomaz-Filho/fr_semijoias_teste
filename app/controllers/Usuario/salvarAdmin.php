<?php
// Define o namespace que você já iniciou
namespace app\controllers\Usuario;

// --- CONFIGURAÇÃO E INCLUDES ---
header('Content-Type: application/json; charset=utf-8');

$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';
require_once $rootPath . '/app/models/Usuario.php';
require_once $rootPath . '/app/models/UsuarioDAO.php';
// Não precisamos do Mail.php ou CodeGenerator.php aqui

// --- VERIFICAÇÃO DO MÉTODO ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// --- LEITURA DOS DADOS (JSON) ---
$json_data = file_get_contents('php://input');
$data = json_decode($json_data);

// =================================================================
// DIFERENÇA 1: Lendo todos os campos do formulário de admin
// =================================================================
$nome            = $data->nome            ?? null;
$email           = $data->email           ?? null;
$senha           = $data->senha           ?? null;
$id_nivel        = $data->id_nivel        ?? null; // Nível vem do admin
$endereco        = $data->endereco        ?? null;
$telefone        = $data->telefone        ?? null;
$cpf             = $data->cpf             ?? null;
$data_nascimento = $data->data_nascimento ?? null;

// --- VALIDAÇÃO ---
// No admin, o nível também é obrigatório
if (!$nome || !$email || !$senha || !$id_nivel) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados incompletos. Nome, email, senha e nível de acesso são obrigatórios.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Validação de senha forte (opcional para o admin, mas recomendado)
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

    $novoUsuario = new \app\models\Usuario();
    $novoUsuario->setNome($nome);
    $novoUsuario->setEmail($email);
    $novoUsuario->setSenha(password_hash($senha, PASSWORD_DEFAULT));
    $novoUsuario->setIdNivel($id_nivel);
    
    // Setando os campos adicionais
    $novoUsuario->setEndereco($endereco);
    $novoUsuario->setTelefone($telefone);
    $novoUsuario->setCpf($cpf);
    $novoUsuario->setdataNascimento($data_nascimento); // Verifique se o nome do setter está correto
    
    // =================================================================
    // DIFERENÇA 2 e 3: Status 'ativo' e sem envio de e-mail
    // =================================================================
    $novoUsuario->setStatus('ativo'); 
    $novoUsuario->setTokenAtivacao(null); // Não precisa de token
    
    $idInserido = $usuarioDAO->insert($novoUsuario);
    
    if ($idInserido) {
        http_response_code(201);
        echo json_encode(['sucesso' => 'Cliente cadastrado com sucesso!'], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao cadastrar o usuário.'], JSON_UNESCAPED_UNICODE);
    }

} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}