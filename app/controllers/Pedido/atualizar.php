<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

require_once $rootPath . '/app/models/Pedido.php';
require_once $rootPath . '/app/models/PedidoDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$idPedido = $_POST['idPedido'] ?? null;
$produto_nome = $_POST['produto_nome'] ?? null;
$cliente_nome = $_POST['cliente_nome'] ?? null;
$preco = $_POST['preco'] ?? null;
$endereco = $_POST['endereco'] ?? '';
$quantidade = $_POST['quantidade'] ?? null;
$data_pedido = $_POST['data_pedido'] ?? null;
$descricao = $_POST['descricao'] ?? '';
$status = $_POST['status'] ?? 'Pendente';

if (!$idPedido) {
    http_response_code(400);
    echo json_encode(['erro' => 'O idPedido é obrigatório para atualização.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!$produto_nome || !$cliente_nome || !$preco || !$quantidade) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados incompletos. Produto, Cliente, Preço e Quantidade são obrigatórios.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $pedidoDAO = new \app\models\PedidoDAO();
    $pedidoExistente = $pedidoDAO->getById($idPedido);

    if (!$pedidoExistente) {
        http_response_code(404);
        echo json_encode(['erro' => 'Pedido não encontrado para atualização.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $pedidoExistente->setProdutoNome($produto_nome);
    $pedidoExistente->setClienteNome($cliente_nome);
    $pedidoExistente->setPreco($preco);
    $pedidoExistente->setEndereco($endereco);
    $pedidoExistente->setQuantidade($quantidade);
    $pedidoExistente->setDataPedido($data_pedido);
    $pedidoExistente->setDescricao($descricao);
    $pedidoExistente->setStatus($status);

    if ($pedidoDAO->update($pedidoExistente)) {
        echo json_encode(['sucesso' => 'Pedido atualizado com sucesso!'], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao atualizar o pedido.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
