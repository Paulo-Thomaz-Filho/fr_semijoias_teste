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

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$idUsuario = $_SESSION['usuario_id'] ?? null;

if (!$idUsuario) {
    http_response_code(401);
    echo json_encode(['erro' => 'Usuário não autenticado.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Os dados estão sendo enviados via application/x-www-form-urlencoded (data: {...})

$nome = $_POST['nome'] ?? null;
$email = $_POST['email'] ?? null;
$cpf = $_POST['cpf'] ?? null;
$nascimento = $_POST['nascimento'] ?? null;
$telefone = $_POST['telefone'] ?? null;
$senha = $_POST['senha'] ?? null; // Nova senha, se enviada

if (!$nome || !$email || !$cpf || !$nascimento || !$telefone) {
    http_response_code(400); // Bad Request
    echo json_encode(['erro' => 'Todos os campos são obrigatórios.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $usuarioDao = new \app\models\UsuarioDAO();
    $usuario = $usuarioDao->getById($idUsuario);

    if (!$usuario) {
        http_response_code(404);
        echo json_encode(['erro' => 'Usuário não encontrado.'], JSON_UNESCAPED_UNICODE);
        exit;
    }


    // 1. Atualizar o objeto Usuario com os novos dados
    $usuario->setNome($nome);
    $usuario->setEmail($email);
    $usuario->setCpf($cpf);
    $usuario->setDataNascimento($nascimento);
    $usuario->setTelefone($telefone);
    // Atualizar senha apenas se enviada e não vazia
    if (!empty($senha)) {
        // Criptografa a senha antes de salvar
        $usuario->setSenha(password_hash($senha, PASSWORD_DEFAULT));
    }
    // Preservar outros campos (endereco, idNivel, status, token_ativacao)

    // 2. Salvar a atualização no banco de dados
    $linhasAfetadas = $usuarioDao->update($usuario);

    if ($linhasAfetadas >= 0) {
        http_response_code(200);
        echo json_encode(['sucesso' => 'Dados atualizados com sucesso!'], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Nenhuma alteração foi salva.'], JSON_UNESCAPED_UNICODE);
    }

} catch (\Exception $e) {
    http_response_code(500);
    error_log("Erro ao atualizar dados do usuário: " . $e->getMessage());
    echo json_encode(['erro' => 'Erro interno ao salvar os dados. Detalhes: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>