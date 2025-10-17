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

$nome = $_POST['nome'] ?? null;
$email = $_POST['email'] ?? null;
$senha = $_POST['senha'] ?? null;
$telefone = $_POST['telefone'] ?? null;
$cpf = $_POST['cpf'] ?? null;
$endereco = $_POST['endereco'] ?? null;
$data_nascimento = $_POST['data_nascimento'] ?? null;
$id_nivel = $_POST['id_nivel'] ?? null;


if (!$nome || !$email || !$senha || !$telefone || !$cpf || !$endereco || !$data_nascimento || !$id_nivel) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados incompletos. Todos os campos são obrigatórios: nome, email, senha, telefone, cpf, endereço, data_nascimento, id_nivel.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Validação de senha forte
$senhaForteRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};':\"\\|,.<>\/?]).{8,}$/";
if (!preg_match($senhaForteRegex, $senha)) {
    http_response_code(400);
    echo json_encode(['erro' => 'A senha deve ter pelo menos 8 caracteres, incluindo uma letra maiúscula, uma minúscula e um caractere especial.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $usuarioDAO = new \app\models\UsuarioDAO();
    $usuarioExistente = $usuarioDAO->getByEmail($email);
    if ($usuarioExistente) {
        http_response_code(409);
        echo json_encode(['erro' => 'E-mail já cadastrado.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $novoUsuario = new \app\models\Usuario();
    $novoUsuario->setNome($nome);
    $novoUsuario->setEmail($email);
    $novoUsuario->setSenha(md5($senha));
    $novoUsuario->setTelefone($telefone);
    $novoUsuario->setCpf($cpf);
    $novoUsuario->setEndereco($endereco);
    $novoUsuario->setDataNascimento($data_nascimento);
    $novoUsuario->setIdNivel($id_nivel);
    $idInserido = $usuarioDAO->insert($novoUsuario);
    if ($idInserido) {
        http_response_code(201);
        echo json_encode(['sucesso' => 'Usuário cadastrado com sucesso.', 'id' => $idInserido], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao cadastrar o usuário.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
