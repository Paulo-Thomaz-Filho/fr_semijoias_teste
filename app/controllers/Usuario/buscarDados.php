<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';
require_once $rootPath . '/app/models/Usuario.php';
require_once $rootPath . '/app/models/UsuarioDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
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

try {
    $usuarioDao = new \app\models\UsuarioDAO();
    $usuario = $usuarioDao->getById($idUsuario);

    if (!$usuario) {
        http_response_code(404);
        echo json_encode(['erro' => 'Usuário não encontrado.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $dados = [
        'idUsuario'  => $usuario->getIdUsuario(),
        'nome'       => $usuario->getNome(),
        'email'      => $usuario->getEmail(),
        'cpf'        => $usuario->getCpf(),
        'nascimento' => $usuario->getDataNascimento(),
        'telefone'   => $usuario->getTelefone(),
        'endereco'   => $usuario->getEndereco(),
        'senha'      => $usuario->getSenha()
    ];

    http_response_code(200);
    echo json_encode(['sucesso' => true, 'dados' => $dados], JSON_UNESCAPED_UNICODE);
} catch (\Exception $e) {
    http_response_code(500);
    error_log("Erro ao buscar dados do usuário: " . $e->getMessage());
    echo json_encode(['erro' => 'Erro interno ao buscar dados.'], JSON_UNESCAPED_UNICODE);
}
?>