<?php

// 1. Conexão com o banco de dados e sessão
require_once "../etc/config.php"; 
require_once "../core/utils/Sanitize.php";

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

// 3. Atualizar status do produto (somente POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "error" => "Método inválido. Use POST."]);
    exit;
}

// 4. Sanitização automática
$sanitizer = new \core\utils\Sanitize(true, true, true); // requestVars, code, sql
$data = $sanitizer->getCleanRequestVars();

// 5. Receber dados já sanitizados
$produto_id = $data['produto_id'] ?? null;

// 6. Validação
if (!$produto_id) {
    echo json_encode(["success" => false, "error" => "ID do produto inválido."]);
    exit;
}

// 7. Atualização do status do produto para inativo (0)
try {
    $sql = "UPDATE produto 
               SET status = 0 
             WHERE id = :produto_id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':produto_id', $produto_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Produto desativado com sucesso."]);
    } else {
        echo json_encode(["success" => false, "error" => "Não foi possível desativar o produto."]);
    }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Erro interno no banco de dados."]);
}
