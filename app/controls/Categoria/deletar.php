<?php
header('Content-Type: application/json');

require_once '../../models/CategoriaDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

$json_data = file_get_contents('php://input');
$data = json_decode($json_data);

if (!$data || !isset($data->idCategoria)) {
    http_response_code(400);
    echo json_encode(['erro' => 'O idCategoria é obrigatório para deletar.']);
    exit;
}

$categoriaDAO = new \app\models\CategoriaDAO();

if ($categoriaDAO->delete($data->idCategoria)) {
    echo json_encode(['sucesso' => 'Categoria deletada com sucesso.']);
} else {
    http_response_code(500);
    echo json_encode(['erro' => 'Ocorreu um erro ao deletar a categoria.']);
}