<?php
header('Content-Type: application/json');

require_once '../../models/Categoria.php';
require_once '../../models/CategoriaDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

$json_data = file_get_contents('php://input');
$data = json_decode($json_data);

if (!$data || !isset($data->nome)) {
    http_response_code(400);
    echo json_encode(['erro' => 'O nome da categoria é obrigatório.']);
    exit;
}

$novaCategoria = new \app\models\Categoria();
$novaCategoria->setNome($data->nome);

$categoriaDAO = new \app\models\CategoriaDAO();
$idInserido = $categoriaDAO->insert($novaCategoria);

if ($idInserido) {
    http_response_code(201);
    $novaCategoria->setIdCategoria($idInserido);
    echo json_encode($novaCategoria->toArray());
} else {
    http_response_code(500);
    echo json_encode(['erro' => 'Ocorreu um erro ao salvar a categoria.']);
}