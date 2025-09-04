<?php
declare(strict_types=1);

session_start();
header("Content-Type: application/json; charset=utf-8");

// 1. Verificação de login
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    http_response_code(401);
    echo json_encode(["success" => false, "error" => "Não autenticado."]);
    exit;
}

// 2. Conexão com o banco de dados
try {
    require_once "../etc/config.php";

    $dsn = "mysql:host=" . $_SESSION['database']['host'] .
           ";dbname=" . $_SESSION['database']['schema'] .
           ";port=" . $_SESSION['database']['port'];

    $pdo = new PDO($dsn, $_SESSION['database']['user'], $_SESSION['database']['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    $pdo->exec("SET NAMES utf8mb4");
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Falha na conexão com o banco de dados."]);
    exit;
}

// 3. Aceitar somente requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Método inválido. Use POST."]);
    exit;
}

// 4. Receber JSON
$inputJSON = file_get_contents('php://input');
$data = json_decode($inputJSON, true);

// 5. Sanitização simples do ID
$pedido_id = isset($data['pedido_id']) ? (int)$data['pedido_id'] : null;

if (!$pedido_id) {
    echo json_encode(["success" => false, "error" => "ID do pedido inválido."]);
    exit;
}

// 6. Atualiza o status do pedido para 'Concluido'
try {
    $sql = "UPDATE pedido SET status = 'Concluido' WHERE pedido_id = :pedido_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':pedido_id', $pedido_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Pedido Concluido com sucesso."]);
    } else {
        echo json_encode(["success" => false, "error" => "Não foi possível Concluido o pedido."]);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Erro interno no banco de dados."]);
}
