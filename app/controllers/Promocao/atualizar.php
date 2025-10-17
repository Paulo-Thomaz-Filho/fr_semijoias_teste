<?php
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

$idPromocao = $_POST['idPromocao'] ?? null;
$nome = $_POST['nome'] ?? null;
$dataInicio = $_POST['data_inicio'] ?? null;
$dataFim = $_POST['data_fim'] ?? null;
$tipo = $_POST['tipo'] ?? null;
$valor = $_POST['valor_desconto'] ?? null;

if (!$idPromocao) {
    http_response_code(400);
    echo json_encode(['erro' => 'O idPromocao é obrigatório para atualização.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!$nome || !$dataInicio || !$dataFim || !$tipo || !$valor) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados incompletos. Nome, datas, tipo e valor são obrigatórios.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $promocaoDAO = new \app\models\PromocaoDAO();
    $promocaoExistente = $promocaoDAO->getById($idPromocao);

    if (!$promocaoExistente) {
        http_response_code(404);
        echo json_encode(['erro' => 'Promoção não encontrada para atualização.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $promocaoExistente->setNome($nome);
    $promocaoExistente->setDataInicio($dataInicio);
    $promocaoExistente->setDataFim($dataFim);
    $promocaoExistente->setTipo($tipo);
    $promocaoExistente->setValor($valor);

    if ($promocaoDAO->update($promocaoExistente)) {
        echo json_encode(['sucesso' => 'Promoção atualizada com sucesso!'], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Ocorreu um erro ao atualizar a promoção.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
