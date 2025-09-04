<?php

// 1. Conexão com o banco de dados e sessão
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

// 3. Cancelar pedido (somente POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 4. Sanitização automática
    require_once "../core/utils/Sanitize.php";
    $sanitizer = new \core\utils\Sanitize(true, true, true); // requestVars, code, sql
    $data = $sanitizer->getCleanRequestVars();

    // 5. Receber dados já sanitizados
    $pedido_id = $data['pedido_id'] ?? null;

    // 6. Validação
    if (!$pedido_id) {
        echo json_encode(["success" => false, "error" => "ID do pedido inválido."]);
        exit;
    }

    try {
        // 7. Atualiza o status para 'cancelado'
        $sql = "UPDATE pedido 
                   SET status = 'cancelado' 
                 WHERE id = :pedido_id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':pedido_id', $pedido_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Pedido cancelado com sucesso."]);
        } else {
            echo json_encode(["success" => false, "error" => "Não foi possível cancelar o pedido."]);
        }

    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => "Erro interno no banco de dados."]);
    }

} else {
    echo json_encode(["success" => false, "error" => "Método inválido. Use POST."]);
}
