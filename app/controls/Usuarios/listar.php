<?php
header('Content-Type: application/json');

require_once __DIR__.'/../../models/Usuario.php';
require_once __DIR__.'/../../models/UsuarioDAO.php';

try {
    $usuarioDAO = new \app\models\UsuarioDAO();
    $usuarios = $usuarioDAO->getAll();

    $usuariosArray = [];
    foreach ($usuarios as $usuario) {
        $usuariosArray[] = $usuario->toArray(); 
    }

    echo json_encode($usuariosArray);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Ocorreu um erro no servidor.']);
}