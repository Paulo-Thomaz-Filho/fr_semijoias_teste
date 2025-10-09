<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__.'/../../models/Promocao.php';
require_once __DIR__.'/../../models/PromocaoDAO.php';

$id = $_GET['idPromocao'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['erro' => 'O idPromocao é obrigatório para atualização.']);
    exit;
}

$nome = $_POST['nome'] ?? null;
$dataInicio = $_POST['data_inicio'] ?? null;
$dataFim = $_POST['data_fim'] ?? null;
$tipo = $_POST['tipo'] ?? null;
$valor = $_POST['valor_desconto'] ?? null;

if (!$nome || !$dataInicio || !$dataFim || !$tipo || !$valor) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados incompletos. Nome, datas, tipo e valor são obrigatórios.']);
    exit;
}

try {
    $promocaoDAO = new \app\models\PromocaoDAO();
    $promocaoExistente = $promocaoDAO->getById($id);

    if (!$promocaoExistente) {
        http_response_code(404);
        echo json_encode(['erro' => 'Promoção não encontrada para atualização.']);
        exit;
    }

    $promocaoExistente->setNome($nome);
    $promocaoExistente->setDataInicio($dataInicio);
    $promocaoExistente->setDataFim($dataFim);
    $promocaoExistente->setTipo($tipo);
    $promocaoExistente->setValor($valor);

    if ($promocaoDAO->update($promocaoExistente)) {
        echo json_encode($promocaoExistente->toArray());
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Ocorreu um erro ao atualizar a promoção.']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Ocorreu um erro no servidor.', 'details' => $e->getMessage()]);
}