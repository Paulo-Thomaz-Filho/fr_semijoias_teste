<?php
// Em: app/controls/Promocao/salvar.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__.'/../../models/Promocao.php';
require_once __DIR__.'/../../models/PromocaoDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

// MUDANÇA: Lendo dados de $_POST, que é preenchido pelo FormData
$nome = $_POST['nome'] ?? null;
$dataInicio = $_POST['data_inicio'] ?? null;
$dataFim = $_POST['data_fim'] ?? null;
$tipo = $_POST['tipo'] ?? null;
$valor = $_POST['valor_desconto'] ?? null;

// A verificação agora usa as variáveis que lemos de $_POST
if (!$nome || !$dataInicio || !$dataFim || !$tipo || !$valor) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados incompletos. Nome, datas, tipo e valor são obrigatórios.']);
    exit;
}

try {
    $novaPromocao = new \app\models\Promocao();
    $novaPromocao->setNome($nome);
    $novaPromocao->setDataInicio($dataInicio);
    $novaPromocao->setDataFim($dataFim);
    $novaPromocao->setTipo($tipo);
    $novaPromocao->setValor($valor);
    // $novaPromocao->setProdutoId(null); // Opcional, se você tiver essa lógica

    $promocaoDAO = new \app\models\PromocaoDAO();
    $idInserido = $promocaoDAO->insert($novaPromocao);

    if ($idInserido) {
        http_response_code(201); // Código para "Created"
        $novaPromocao->setIdPromocao($idInserido);
        echo json_encode($novaPromocao->toArray());
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Ocorreu um erro ao salvar a promoção.']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Ocorreu um erro no servidor.', 'details' => $e->getMessage()]);
}