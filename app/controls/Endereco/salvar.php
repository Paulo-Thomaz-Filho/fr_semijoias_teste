<?php
header('Content-Type: application/json');

require_once '../../models/Endereco.php';
require_once '../../models/EnderecoDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

$json_data = file_get_contents('php://input');
$data = json_decode($json_data);

if (!$data || !isset($data->usuarioId) || !isset($data->cep) || !isset($data->logradouro) || !isset($data->numero) || !isset($data->bairro) || !isset($data->cidade) || !isset($data->estado)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados incompletos. usuarioId, cep, logradouro, numero, bairro, cidade e estado são obrigatórios.']);
    exit;
}

$novoEndereco = new \app\models\Endereco();
$novoEndereco->setUsuarioId($data->usuarioId);
$novoEndereco->setCep($data->cep);
$novoEndereco->setLogradouro($data->logradouro);
$novoEndereco->setNumero($data->numero);
$novoEndereco->setComplemento($data->complemento ?? ''); // Complemento pode ser opcional
$novoEndereco->setBairro($data->bairro);
$novoEndereco->setCidade($data->cidade);
$novoEndereco->setEstado($data->estado);

$enderecoDAO = new \app\models\EnderecoDAO();
$idInserido = $enderecoDAO->insert($novoEndereco);

if ($idInserido) {
    http_response_code(201);
    $novoEndereco->setIdEnderecos($idInserido);
    echo json_encode($novoEndereco->toArray());
} else {
    http_response_code(500);
    echo json_encode(['erro' => 'Ocorreu um erro ao salvar o endereço.']);
}