<?php

header('Content-Type: application/json; charset=utf-8');

// Permite receber dados JSON no corpo da requisição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST)) {
    $input = json_decode(file_get_contents('php://input'), true);
    if (is_array($input)) {
        foreach ($input as $key => $value) {
            $_POST[$key] = $value;
        }
    }
}

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

// Permite receber vários produtos ou produto único
$id_cliente = $_POST['id_cliente'] ?? null;
$endereco = $_POST['endereco'] ?? '';
$data_pedido = $_POST['data_pedido'] ?? date('Y-m-d H:i:s');
$descricao = $_POST['descricao'] ?? '';
$statusNome = $_POST['status'] ?? 'Pendente';

// Se vier array de produtos
$produtos = [];
if (isset($_POST['produtos'])) {
    $produtos = json_decode($_POST['produtos'], true);
}
// Se vier campos individuais
if (empty($produtos) && isset($_POST['produto_nome'], $_POST['preco'], $_POST['quantidade'])) {
    $produtos = [[
        'produto_nome' => $_POST['produto_nome'],
        'preco' => $_POST['preco'],
        'quantidade' => $_POST['quantidade'],
        'endereco' => $endereco,
        'data_pedido' => $data_pedido,
        'descricao' => $descricao,
        'status' => $statusNome
    ]];
}

if (!is_array($produtos) || count($produtos) === 0 || !$id_cliente) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados incompletos. Produtos e Cliente são obrigatórios.'], JSON_UNESCAPED_UNICODE);
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

    $pedidoDAO = new \app\models\PedidoDAO();
    $idsInseridos = [];
    foreach ($produtos as $produto) {
        $produto_nome = $produto['produto_nome'] ?? null;
        $preco = $produto['preco'] ?? null;
        $quantidade = $produto['quantidade'] ?? null;
        if (!$produto_nome || !$preco || !$quantidade) {
            continue; // pula produtos incompletos
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
        $idInserido = $pedidoDAO->insert($novoPedido);
        if ($idInserido) {
            $idsInseridos[] = $idInserido;
        }
    }
    if (count($idsInseridos) > 0) {
        http_response_code(201);
        echo json_encode(['sucesso' => 'Pedido salvo com sucesso!', 'ids' => $idsInseridos], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao salvar o pedido. Nenhum produto foi inserido.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
