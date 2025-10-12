<?php
// Em: app/controls/Pedido/salvar.php

header('Content-Type: application/json; charset=utf-8');

// Supondo que você criará os modelos Pedido.php e PedidoDAO.php
require_once __DIR__.'/../../models/Pedido.php';
require_once __DIR__.'/../../models/PedidoDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

// Lendo o JSON enviado pelo JavaScript
$dadosRecebidos = json_decode(file_get_contents('php://input'));

// --- CORREÇÃO NA VALIDAÇÃO ---
// Verificando os nomes corretos que o JavaScript envia: usuario_id, endereco_id, valor_total
if (!$dadosRecebidos || !isset($dadosRecebidos->usuario_id) || !isset($dadosRecebidos->endereco_id) || !isset($dadosRecebidos->valor_total)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados incompletos. Cliente, Endereço e Valor Total são obrigatórios.']);
    exit;
}

try {
    // --- CORREÇÃO NA CRIAÇÃO DO OBJETO ---
    // Instanciando um novo Pedido (supondo que a classe e os setters existam)
    $novoPedido = new \app\models\Pedido();
    $novoPedido->setUsuarioId($dadosRecebidos->usuario_id);
    $novoPedido->setEnderecoId($dadosRecebidos->endereco_id);
    $novoPedido->setValorTotal($dadosRecebidos->valor_total);
    $novoPedido->setStatus($dadosRecebidos->status ?? 'Pendente');
    $novoPedido->setDataPedido($dadosRecebidos->data_pedido);
    // Não precisamos enviar a data, o banco de dados faz isso automaticamente.

    $pedidoDAO = new \app\models\PedidoDAO();
    $idInserido = $pedidoDAO->insert($novoPedido);

    if ($idInserido) {
        http_response_code(201);
        echo json_encode(['sucesso' => 'Pedido salvo com sucesso!', 'id' => $idInserido]);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Ocorreu um erro ao salvar o pedido.']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Ocorreu um erro no servidor.', 'details' => $e->getMessage()]);
}