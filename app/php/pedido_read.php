<?php
// 1. Conexão com o banco de dados
require_once "../etc/config.php"; 

try {
    $dsn = "mysql:host=" . $_SESSION['database']['host'] .
           ";dbname=" . $_SESSION['database']['schema'] .
           ";port=" . $_SESSION['database']['port'];

    $pdo = new PDO($dsn, $_SESSION['database']['user'], $_SESSION['database']['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Erro interno de conexão."]);
    exit;
}

// 2. Verificação de login
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: " . $_SESSION['url']['root'] . "login.html");
    exit;
}

// 3. Buscar dados de pedidos
try {
    $sql = "SELECT * FROM pedido";
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $pedidos = [];
    foreach ($rows as $row) {
        $pedido = new pedido();
        $pedido->load(
            $row['pedido_id'],
            $row['usuario_id'],
            $row['data_pedido'],
            $row['status'],
            $row['valor_total'],
            $row['endereco_entrega']
        );
        $pedidos[] = $pedido->toArray(); 
    }

    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($pedidos);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Erro interno no banco de dados."]);
}
