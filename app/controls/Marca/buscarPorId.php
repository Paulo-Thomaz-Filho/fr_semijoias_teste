<?php
header('Content-Type: application/json');

require_once '../../models/Marca.php';
require_once '../../models/MarcaDAO.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['erro' => 'O ID da marca é obrigatório.']);
    exit;
}

$marcaDAO = new \app\models\MarcaDAO();
$marca = $marcaDAO->getById($id);

if ($marca) {
    echo json_encode($marca->toArray());
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Marca não encontrada.']);
}