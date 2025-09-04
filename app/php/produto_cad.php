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

// 4. Receber JSON do front-end
$inputJSON = file_get_contents('php://input');
$data = json_decode($inputJSON, true);

// 5. Sanitização e validação dos campos
$nome_produto  = trim($data['nome'] ?? '');
$quantidade    = (int)($data['quantidade'] ?? 0);
$valor_total   = (float)($data['valor'] ?? 0);
$cliente       = trim($data['cliente'] ?? '');
$descricao     = trim($data['descricao'] ?? '');
$descricao     = $descricao ?: 'Não informado'; // default se vazio
$data_pedido   = $data['data'] ?? date('Y-m-d H:i:s');

$usuario_id = $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
    echo json_encode(["success" => false, "error" => "Usuário não identificado."]);
    exit;
}

// Valida obrigatórios
if (!$nome_produto || !$quantidade || !$valor_total || !$cliente) {
    echo json_encode(["success" => false, "error" => "Todos os campos obrigatórios devem ser preenchidos."]);
    exit;
}

// Valida data
if (!DateTime::createFromFormat('Y-m-d', substr($data_pedido,0,10))) {
    echo json_encode(["success" => false, "error" => "Data inválida."]);
    exit;
}

// 6. Inserção no banco de dados
try {
    $sql = "INSERT INTO pedido (usuario_id, data_pedido, status, valor_total, endereco_entrega)
            VALUES (:usuario_id, :data_pedido, :status, :valor_total, :endereco_entrega)";
    $stmt = $pdo->prepare($sql);

    $status = 'pendente';

    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->bindParam(':data_pedido', $data_pedido);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':valor_total', $valor_total);
    $stmt->bindParam(':endereco_entrega', $descricao);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Pedido cadastrado com sucesso."]);
    } else {
        echo json_encode(["success" => false, "error" => "Erro ao cadastrar pedido."]);
    }

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Erro interno no banco de dados: " . $e->getMessage()]);
}
