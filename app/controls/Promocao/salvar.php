<?php
// Em: app/controls/Promocao/salvar.php

header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

require_once $rootPath . '/app/models/Promocao.php';
require_once $rootPath . '/app/models/PromocaoDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$nome = $_POST['nome'] ?? null;
$dataInicio = $_POST['data_inicio'] ?? null;
$dataFim = $_POST['data_fim'] ?? null;
$tipo = $_POST['tipo'] ?? null;
$valor = $_POST['valor_desconto'] ?? null;

if (!$nome || !$dataInicio || !$dataFim || !$tipo || !$valor) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados incompletos. Nome, datas, tipo e valor são obrigatórios.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $novaPromocao = new \app\models\Promocao();
    $novaPromocao->setNome($nome);
    $novaPromocao->setDataInicio($dataInicio);
    $novaPromocao->setDataFim($dataFim);
    $novaPromocao->setTipo($tipo);
    $novaPromocao->setValor($valor);

    $promocaoDAO = new \app\models\PromocaoDAO();
    $idInserido = $promocaoDAO->insert($novaPromocao);

    if ($idInserido) {
        http_response_code(201);
        echo json_encode(['sucesso' => 'Promoção salva com sucesso!', 'id' => $idInserido], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Ocorreu um erro ao salvar a promoção.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}