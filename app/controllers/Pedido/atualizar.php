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

$id_pedido = $_POST['id_pedido'] ?? null;
$produto_nome = $_POST['produto_nome'] ?? null;
$id_cliente = $_POST['id_cliente'] ?? null;
$preco = $_POST['preco'] ?? null;
$endereco = $_POST['endereco'] ?? '';
$quantidade = $_POST['quantidade'] ?? null;
$data_pedido = $_POST['data_pedido'] ?? null;
$descricao = $_POST['descricao'] ?? '';
$id_status = $_POST['status'] ?? null;

if (!$id_pedido) {
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
    $pedidoArray = $pedidoDAO->getById($id_pedido);

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

    $statusNome = strtolower($statusObj->getNome());
    $emailEnviado = false;
    if ($statusNome === 'aprovado' || $statusNome === 'sucesso') {
        // Enviar email de confirmação de pedido realizado
        require_once $rootPath . '/app/models/UsuarioDAO.php';
        require_once $rootPath . '/app/core/utils/EmailTemplate.php';
        require_once $rootPath . '/app/core/utils/Mail.php';
        $usuarioDAO = new \app\models\UsuarioDAO();
        $usuario = $usuarioDAO->getById($id_cliente);
        if ($usuario) {
            $nomeUsuario = $usuario->getNome();
            $emailUsuario = $usuario->getEmail();
            $numeroPedido = $pedidoArray['idPedido'];
            $linkPedido = $_ENV['SITE_URL'] . '/pedido.html?id=' . $numeroPedido;
            $htmlEmail = \app\core\utils\EmailTemplate::emailPedidoRealizado($nomeUsuario, $numeroPedido, $linkPedido);
            $mail = new \app\core\utils\Mail($emailUsuario, 'Pedido Confirmado - FR Semijoias', $htmlEmail);
            $emailEnviado = $mail->send();
        }
    }

    if ($pedidoDAO->update($pedidoExistente)) {
        $response = ['sucesso' => 'Pedido atualizado com sucesso!'];
        if ($emailEnviado) {
            $response['email'] = 'Email de confirmação enviado.';
        }
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao atualizar o pedido.'], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
