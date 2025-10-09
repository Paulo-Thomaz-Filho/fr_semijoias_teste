<?php
header('Content-Type: application/json');

require_once '../../models/Marca.php';
require_once '../../models/MarcaDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

$json_data = file_get_contents('php://input');
$data = json_decode($json_data);

if (!$data || !isset($data->nome)) {
    http_response_code(400);
    echo json_encode(['erro' => 'O nome da marca é obrigatório.']);
    exit;
}

$novaMarca = new \app\models\Marca();
$novaMarca->setNome($data->nome);

$marcaDAO = new \app\models\MarcaDAO();
$idInserido = $marcaDAO->insert($novaMarca);

if ($idInserido) {
    http_response_code(201);
    $novaMarca->setIdMarca($idInserido);
    echo json_encode($novaMarca->toArray());
} else {
    http_response_code(500);
    echo json_encode(['erro' => 'Ocorreu um erro ao salvar a marca.']);
}