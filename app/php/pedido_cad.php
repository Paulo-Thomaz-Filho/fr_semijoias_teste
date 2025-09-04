<?php
declare(strict_types=1);
session_start();
header("Content-Type: application/json; charset=utf-8");

// Verificação de login
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    http_response_code(401);
    echo json_encode(["success" => false, "error" => "Não autenticado."]);
    exit;
}

// Conexão com banco
try {
    require_once "../etc/config.php";
    $dsn = "mysql:host=".$_SESSION['database']['host'].
           ";dbname=".$_SESSION['database']['schema'].
           ";port=".$_SESSION['database']['port'];
    $pdo = new PDO($dsn, $_SESSION['database']['user'], $_SESSION['database']['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    $pdo->exec("SET NAMES utf8mb4");
} catch(Throwable $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Erro ao conectar ao banco."]);
    exit;
}

// Aceitar apenas POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success"=>false,"error"=>"Método inválido. Use POST."]);
    exit;
}

// Receber JSON
$inputJSON = file_get_contents('php://input');
$data = json_decode($inputJSON, true);

// Sanitização simples
$nome = $data['nome'] ?? null;
$quantidade = (int)($data['quantidade'] ?? 0);
$valor = (float)($data['valor'] ?? 0);
$cliente = $data['cliente'] ?? null;
$descricao = $data['descricao'] ?? null;
$data_pedido = $data['data'] ?? date('Y-m-d H:i:s');

if (!$nome || !$quantidade || !$valor || !$cliente) {
    echo json_encode(["success"=>false,"error"=>"Campos obrigatórios faltando."]);
    exit;
}

// Inserção
try {
    $sql = "INSERT INTO pedido (usuario_id, data_pedido, status, valor_total, endereco_entrega)
            VALUES (:usuario_id, :data_pedido, 'pendente', :valor_total, :endereco_entrega)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':usuario_id' => $_SESSION['usuario_id'],
        ':data_pedido' => $data_pedido,
        ':valor_total' => $valor,
        ':endereco_entrega' => $descricao ?? ''
    ]);
    echo json_encode(["success"=>true, "message"=>"Pedido cadastrado com sucesso."]);
} catch(Throwable $e) {
    http_response_code(500);
    echo json_encode(["success"=>false,"error"=>"Erro interno no banco de dados."]);
}
