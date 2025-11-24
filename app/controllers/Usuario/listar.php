<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';
require_once $rootPath . '/app/models/Usuario.php';
require_once $rootPath . '/app/models/UsuarioDAO.php';

try {
    $usuarioDAO = new \app\models\UsuarioDAO();
    $usuarios = $usuarioDAO->getAll();

    $usuariosArray = [];
    foreach ($usuarios as $usuario) {
        $usuariosArray[] = $usuario->toArray();
    }

    echo json_encode($usuariosArray, JSON_UNESCAPED_UNICODE);

} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
