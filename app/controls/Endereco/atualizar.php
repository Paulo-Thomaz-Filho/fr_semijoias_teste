<?php
header('Content-Type: application/json');

require_once '../../models/Endereco.php';
require_once '../../models/EnderecoDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

$json_data = file_get_contents('php://input');
$data = json_decode($json_data);

if (!$data || !isset($data->idEnderecos)) {
    http_response_code(400);
    echo json_encode(['erro' => 'O idEnderecos é obrigatório para atualização.']);
    exit;
}

$enderecoDAO = new \app\models\EnderecoDAO();
$enderecoExistente = $enderecoDAO->getById($data->idEnderecos);

if (!$enderecoExistente) {
    http_response_code(404);
    echo json_encode(['erro' => 'Endereço não encontrado para atualização.']);
    exit;
}

// Atualiza os dados do objeto existente
$enderecoExistente->setCep($data->cep ?? $enderecoExistente->getCep());
$enderecoExistente->setLogradouro($data->logradouro ?? $enderecoExistente->getLogradouro());
$enderecoExistente->setNumero($data->numero ?? $enderecoExistente->getNumero());
$enderecoExistente->setComplemento($data->complemento ?? $enderecoExistente->getComplemento());
$enderecoExistente->setBairro($data->bairro ?? $enderecoExistente->getBairro());
$enderecoExistente->setCidade($data->cidade ?? $enderecoExistente->getCidade());
$enderecoExistente->setEstado($data->estado ?? $enderecoExistente->getEstado());

if ($enderecoDAO->update($enderecoExistente)) {
    echo json_encode($enderecoExistente->toArray());
} else {
    http_response_code(500);
    echo json_encode(['erro' => 'Ocorreu um erro ao atualizar o endereço.']);
}