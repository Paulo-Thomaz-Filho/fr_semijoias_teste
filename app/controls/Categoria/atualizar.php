<?php
header('Content-Type: application/json');

require_once '../../models/Categoria.php';
require_once '../../models/CategoriaDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

$json_data = file_get_contents('php://input');
$data = json_decode($json_data);

if (!$data || !isset($data->idCategoria)) {
    http_response_code(400);
    echo json_encode(['erro' => 'O idCategoria é obrigatório para atualização.']);
    exit;
}

$categoriaDAO = new \app\models\CategoriaDAO();
$categoriaExistente = $categoriaDAO->getById($data->idCategoria);

if (!$categoriaExistente) {
    http_response_code(404);
    echo json_encode(['erro' => 'Categoria não encontrada para atualização.']);
    exit;
}

$categoriaExistente->setNome($data->nome ?? $categoriaExistente->getNome());

if ($categoriaDAO->update($categoriaExistente)) {
    echo json_encode($categoriaExistente->toArray());
} else {
    http_response_code(500);
    echo json_encode(['erro' => 'Ocorreu um erro ao atualizar a categoria.']);
}