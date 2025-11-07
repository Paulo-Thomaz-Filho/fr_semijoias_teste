<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

require_once $rootPath . '/app/models/Usuario.php';
require_once $rootPath . '/app/models/UsuarioDAO.php';

// Aceita POST (pois o JS envia POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// =================================================================
// CORREÇÃO: Lendo o ID a partir do corpo JSON
// =================================================================
$json_data = file_get_contents('php://input');
$data = json_decode($json_data);
$idUsuario = $data->idUsuario ?? null;
// =================================================================

if (!$idUsuario) {
    http_response_code(400);
    echo json_encode(['erro' => 'O idUsuario é obrigatório para exclusão.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $usuarioDAO = new \app\models\UsuarioDAO();
    $usuarioExistente = $usuarioDAO->getById($idUsuario);

    if (!$usuarioExistente) {
        http_response_code(404);
        echo json_encode(['erro' => 'Usuário não encontrado para exclusão.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($usuarioDAO->delete($idUsuario)) {
        echo json_encode(['sucesso' => 'Usuário excluído com sucesso.'], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        // Este é o erro que você está vendo
        echo json_encode(['erro' => 'Erro ao excluir o usuário. (Possível restrição de chave estrangeira, ex: usuário com pedidos existentes)'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    // Se a restrição de FK não for tratada e gerar uma exceção
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}