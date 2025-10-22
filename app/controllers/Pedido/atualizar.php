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

$idPedido = $_POST['idPedido'] ?? null;
$produto_nome = $_POST['produto_nome'] ?? null;
$id_cliente = $_POST['id_cliente'] ?? null;
$preco = $_POST['preco'] ?? null;
$endereco = $_POST['endereco'] ?? '';
$quantidade = $_POST['quantidade'] ?? null;
$data_pedido = $_POST['data_pedido'] ?? null;
$descricao = $_POST['descricao'] ?? '';
$id_status = $_POST['status'] ?? null;

if (!$idPedido) {
    http_response_code(400);
    echo json_encode(['erro' => 'O idPedido é obrigatório para atualização.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!$produto_nome || !$id_cliente || !$preco || !$quantidade) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados incompletos. Produto, Cliente, Preço e Quantidade são obrigatórios.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $pedidoDAO = new \app\models\PedidoDAO();
    $pedidoArray = $pedidoDAO->getById($idPedido);

    if (!$pedidoArray) {
        http_response_code(404);
        echo json_encode(['erro' => 'Pedido não encontrado para atualização.'], JSON_UNESCAPED_UNICODE);
        exit;
    }


    if (!$id_status || !is_numeric($id_status)) {
        http_response_code(400);
        echo json_encode(['erro' => 'Status inválido.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $statusDAO = new \app\models\StatusDAO();
    $statusObj = $statusDAO->getById($id_status);
    if (!$statusObj) {
        http_response_code(400);
        echo json_encode(['erro' => 'Status inválido.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Reconstrói o objeto Pedido a partir do array
    $pedidoExistente = new \app\models\Pedido(
        $pedidoArray['idPedido'],
        $produto_nome,
        $id_cliente,
        $preco,
        $endereco,
        $data_pedido,
        $quantidade,
        $id_status,
        $descricao
    );

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
