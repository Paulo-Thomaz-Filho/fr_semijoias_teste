<?php
header('Content-Type: application/json');

require_once '../../models/MarcaDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

$json_data = file_get_contents('php://input');
$data = json_decode($json_data);

if (!$data || !isset($data->idMarca)) {
    http_response_code(400);
    echo json_encode(['erro' => 'O idMarca é obrigatório para deletar.']);
    exit;
}

$marcaDAO = new \app\models\MarcaDAO();

if ($marcaDAO->delete($data->idMarca)) {
    echo json_encode(['sucesso' => 'Marca deletada com sucesso.']);
} else {
    http_response_code(500);
    echo json_encode(['erro' => 'Ocorreu um erro ao deletar a marca.']);
}