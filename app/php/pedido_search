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
    echo json_encode(["success" => false, "error" => "Erro de conexão com o banco de dados."]);
    exit;
}

// 2. Verificação de login
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: " . $_SESSION['url']['root'] . "login.html");
    exit;
}

// 3. Recebe o ID do pedido via GET ou POST
require_once "../core/utils/Sanitize.php";
$sanitizer = new \core\utils\Sanitize(true, true, true);
$data = $sanitizer->getCleanRequestVars();

$pedido_id = isset($data['pedido_id']) ? (int)$data['pedido_id'] : null;

if (!$pedido_id) {
    echo json_encode(["success" => false, "error" => "ID do pedido inválido."]);
    exit;
}

// 4. Buscar pedido específico
try {
    $sql = "SELECT * FROM pedido WHERE pedido_id = :pedido_id LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':pedido_id', $pedido_id, PDO::PARAM_INT);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // Sanitização dos campos do pedido
        $pedido = [
            'pedido_id'        => (int) $row['pedido_id'],
            'usuario_id'       => (int) $row['usuario_id'],
            'data_pedido'      => $sanitizer->sanitizeString($row['data_pedido']),
            'status'           => $sanitizer->sanitizeString($row['status']),
            'valor_total'      => (float) $row['valor_total'],
            'endereco_entrega' => $sanitizer->sanitizeString($row['endereco_entrega'])
        ];

        echo json_encode(["success" => true, "pedido" => $pedido]);
    } else {
        echo json_encode(["success" => false, "error" => "Pedido não encontrado."]);
    }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Erro interno no banco de dados."]);
}
