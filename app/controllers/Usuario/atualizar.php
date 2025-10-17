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

$idUsuario = $_POST['idUsuario'] ?? null;
$nome = $_POST['nome'] ?? null;
$email = $_POST['email'] ?? null;
$senha = $_POST['senha'] ?? null;
$telefone = $_POST['telefone'] ?? null;
$cpf = $_POST['cpf'] ?? null;
$endereco = $_POST['endereco'] ?? null;
$data_nascimento = $_POST['data_nascimento'] ?? null;
$id_nivel = $_POST['id_nivel'] ?? null;

if (!$idUsuario) {
    http_response_code(400);
    echo json_encode(['erro' => 'O idUsuario é obrigatório para atualização.'], JSON_UNESCAPED_UNICODE);
    exit;
}
if (!$nome || !$email || !$telefone || !$cpf || !$endereco || !$data_nascimento || !$id_nivel) {
    http_response_code(400);
    echo json_encode(['erro' => 'Todos os campos são obrigatórios: nome, email, telefone, cpf, endereço, data_nascimento, id_nivel.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $usuarioDAO = new \app\models\UsuarioDAO();
    $usuarioExistente = $usuarioDAO->getById($idUsuario);

    if (!$usuarioExistente) {
        http_response_code(404);
        echo json_encode(['erro' => 'Usuário não encontrado para atualização.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $usuarioExistente->setNome($nome);
    $usuarioExistente->setEmail($email);
    $usuarioExistente->setTelefone($telefone);
    $usuarioExistente->setCpf($cpf);
    $usuarioExistente->setEndereco($endereco);
    $usuarioExistente->setDataNascimento($data_nascimento);
    $usuarioExistente->setIdNivel($id_nivel);
    if (!empty($senha)) {
    $usuarioExistente->setSenha(md5($senha));
    }

    if ($usuarioDAO->update($usuarioExistente)) {
        echo json_encode(['sucesso' => 'Usuário atualizado com sucesso!'], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao atualizar o usuário.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
