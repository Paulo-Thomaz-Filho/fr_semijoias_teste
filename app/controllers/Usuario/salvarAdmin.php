<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

require_once $rootPath . '/app/models/Usuario.php';
require_once $rootPath . '/app/models/UsuarioDAO.php';

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
$id_nivel        = $data->id_nivel        ?? null;
$endereco        = $data->endereco        ?? null;
$telefone        = $data->telefone        ?? null;
$cpf             = $data->cpf             ?? null;
$data_nascimento = $data->data_nascimento ?? null;

if (!$nome || !$email || !$senha || !$id_nivel) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados incompletos. Nome, email, senha e nível de acesso são obrigatórios.'], JSON_UNESCAPED_UNICODE);
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
    $novoUsuario->setEndereco($endereco);
    $novoUsuario->setTelefone($telefone);
    $novoUsuario->setCpf($cpf);
    $novoUsuario->setDataNascimento($data_nascimento);
    $novoUsuario->setStatus('ativo'); 
    $novoUsuario->setTokenAtivacao(null);
    
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