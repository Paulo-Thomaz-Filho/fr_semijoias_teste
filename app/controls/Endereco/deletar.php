<?php
header('Content-Type: application/json');

require_once '../../models/EnderecoDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

$json_data = file_get_contents('php://input');
$data = json_decode($json_data);

if (!$data || !isset($data->idEnderecos)) {
    http_response_code(400);
    echo json_encode(['erro' => 'O idEnderecos é obrigatório para deletar.']);
    exit;
}

$enderecoDAO = new \app\models\EnderecoDAO();

if ($enderecoDAO->delete($data->idEnderecos)) {
    echo json_encode(['sucesso' => 'Endereço deletado com sucesso.']);
} else {
    http_response_code(500);
    echo json_encode(['erro' => 'Ocorreu um erro ao deletar o endereço.']);
}