<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';
require_once $rootPath . '/app/models/Pedido.php';
require_once $rootPath . '/app/models/PedidoDAO.php';
require_once $rootPath . '/app/models/StatusDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$produto_nome = $_POST['produto_nome'] ?? null;
$id_cliente = $_POST['id_cliente'] ?? null;
$preco = $_POST['preco'] ?? null;
$endereco = $_POST['endereco'] ?? '';
$quantidade = $_POST['quantidade'] ?? null;
$data_pedido = $_POST['data_pedido'] ?? date('Y-m-d H:i:s');
$descricao = $_POST['descricao'] ?? '';
$statusNome = $_POST['status'] ?? 'N/A';

if (!$produto_nome || !$id_cliente || !$quantidade || !$preco) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados incompletos. Produto, Cliente, Quantidade e Preço são obrigatórios.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {

    $statusDAO = new \app\models\StatusDAO();
    $statusObj = $statusDAO->getByName($statusNome);
    if (!$statusObj) {
        http_response_code(400);
        echo json_encode(['erro' => 'Status inválido.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $novoPedido = new \app\models\Pedido();
    $novoPedido->setProdutoNome($produto_nome);
    $novoPedido->setIdCliente($id_cliente);
    $novoPedido->setPreco($preco);
    $novoPedido->setEndereco($endereco);
    $novoPedido->setDataPedido($data_pedido);
    $novoPedido->setQuantidade($quantidade);
    $novoPedido->setIdStatus($statusObj->getIdStatus());
    $novoPedido->setDescricao($descricao);

    $pedidoDAO = new \app\models\PedidoDAO();
    $idInserido = $pedidoDAO->insert($novoPedido);

    if ($idInserido) {
        http_response_code(201);
        echo json_encode(['sucesso' => 'Pedido salvo com sucesso!', 'id' => $idInserido], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao salvar o pedido.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
