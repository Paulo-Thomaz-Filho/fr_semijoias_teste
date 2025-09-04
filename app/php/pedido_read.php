<?php
declare(strict_types=1);

session_start();

// Simula login
$_SESSION['logado'] = true;

header("Content-Type: application/json; charset=utf-8");

try {
    require_once __DIR__ . "/../etc/config.php";

    $dsn = "mysql:host=" . $_SESSION['database']['host'] .
           ";dbname=" . $_SESSION['database']['schema'] .
           ";port=" . $_SESSION['database']['port'];

    $pdo = new PDO($dsn, $_SESSION['database']['user'], $_SESSION['database']['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo->exec("SET NAMES utf8mb4");
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "error" => "Falha de conexão/config."]);
    exit;
}

if (empty($_SESSION['logado'])) {
    http_response_code(401);
    echo json_encode(["status" => "error", "error" => "Não autenticado."]);
    exit;
}

try {
    $sql = "SELECT 
                pedido_id AS id,
                usuario_id,
                valor_total AS valor,
                endereco_entrega AS endereco,
                data_pedido AS data,
                status
            FROM pedido
            ORDER BY pedido_id DESC";

    $rows = $pdo->query($sql)->fetchAll();

    echo json_encode([
        "status" => "success",
        "data" => $rows
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "error" => "Erro interno no banco de dados."]);
}
