<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

require_once $rootPath . '/app/models/Usuario.php';
require_once $rootPath . '/app/models/UsuarioDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$idUsuario = $_GET['id'] ?? $_GET['idUsuario'] ?? $_POST['id'] ?? $_POST['idUsuario'] ?? null;

if (!$idUsuario) {
    http_response_code(400);
    echo json_encode(['erro' => 'O idUsuario é obrigatório para inativar.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $usuarioDAO = new \app\models\UsuarioDAO();
    
    // Verificar se existe antes de inativar
    $usuarioExistente = $usuarioDAO->getById($idUsuario);
    if (!$usuarioExistente) {
        http_response_code(404);
        echo json_encode(['erro' => 'Usuário não encontrado.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($usuarioDAO->inativar($idUsuario)) {
        echo json_encode(['sucesso' => 'Usuário inativado com sucesso.'], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao inativar o usuário.'], JSON_UNESCAPED_UNICODE);
    }

} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}