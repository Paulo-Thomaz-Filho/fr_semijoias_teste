<?php
header('Content-Type: application/json; charset=utf-8');

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

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$idUsuario = $_SESSION['usuario_id'] ?? null;

$enderecoCompleto = $data->endereco ?? null;

if (!$idUsuario || !$enderecoCompleto) {
    http_response_code(400); // Bad Request
    echo json_encode(['erro' => 'ID do usuário ou dados do endereço incompletos.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // 1. Instanciar o DAO
    $usuarioDao = new \app\models\UsuarioDAO();

    // 2. Obter o usuário atual
    $usuario = $usuarioDao->getById($idUsuario);

    if (!$usuario) {
        http_response_code(404); // Not Found
        echo json_encode(['erro' => 'Usuário não encontrado.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 3. Atualizar o campo 'endereco' no objeto Usuario
    $usuario->setEndereco($enderecoCompleto);

    // 4. Salvar a atualização no banco de dados
    $linhasAfetadas = $usuarioDao->update($usuario);
    
    // 5. Responder com sucesso
    if ($linhasAfetadas > 0) {
        http_response_code(200);
        echo json_encode(['sucesso' => 'Endereço atualizado com sucesso!', 'endereco' => $enderecoCompleto], JSON_UNESCAPED_UNICODE);
    } else {
        // Se a atualização não afetou linhas (mas não lançou erro), pode significar que o dado era o mesmo
        http_response_code(200); 
        echo json_encode(['aviso' => 'Nenhuma alteração foi feita, o endereço pode ser o mesmo.', 'endereco' => $enderecoCompleto], JSON_UNESCAPED_UNICODE);
    }
    
} catch (\Exception $e) {
    http_response_code(500); // Internal Server Error
    error_log("Erro ao atualizar endereço: " . $e->getMessage());
    echo json_encode(['erro' => 'Erro interno ao salvar o endereço. Detalhes: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}