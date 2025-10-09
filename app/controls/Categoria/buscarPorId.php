<?php
header('Content-Type: application/json');

require_once '../../models/Categoria.php';
require_once '../../models/CategoriaDAO.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['erro' => 'O ID da categoria é obrigatório.']);
    exit;
}

$categoriaDAO = new \app\models\CategoriaDAO();
$categoria = $categoriaDAO->getById($id);

if ($categoria) {
    echo json_encode($categoria->toArray());
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Categoria não encontrada.']);
}