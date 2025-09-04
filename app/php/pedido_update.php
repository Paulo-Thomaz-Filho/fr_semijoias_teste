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
    // Retorna um erro JSON em caso de falha na conexão
    echo json_encode(["success" => false, "error" => "Erro interno de conexão."]);
    exit;
}

// 2. Verificação de login
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    // Redireciona o usuário para a página de login
    header("Location: " . $_SESSION['url']['root'] . "login.html");
    exit;
}

// Define o cabeçalho para responder com JSON
header('Content-Type: application/json');

// 3. Atualização (somente POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 4. Receber e decodificar os dados JSON
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    // Verifica se a decodificação foi bem-sucedida e se os dados são um array
    if ($data === null || !is_array($data)) {
        echo json_encode(["success" => false, "error" => "Formato de dados inválido."]);
        exit;
    }

    // 5. Receber dados
    $pedido_id        = $data['pedido_id'] ?? null;
    $usuario_id       = $data['usuario_id'] ?? null;
    $data_pedido      = $data['data_pedido'] ?? null;
    $status           = $data['status'] ?? null;
    $valor_total      = $data['valor_total'] ?? null;
    $endereco_entrega = $data['endereco_entrega'] ?? null;

    $statusPermitidos = ['pendente', 'pago', 'cancelado'];

    // 6. Validações
    // Verifica se todos os campos necessários estão presentes
    if (!$pedido_id || !$usuario_id || !$data_pedido || !$status || !$valor_total || !$endereco_entrega) {
        echo json_encode(["success" => false, "error" => "Todos os campos são obrigatórios."]);
        exit;
    }
    // Valida o status
    if (!in_array($status, $statusPermitidos)) {
        echo json_encode(["success" => false, "error" => "Status inválido."]);
        exit;
    }
    // Valida o formato da data
    if (!DateTime::createFromFormat('Y-m-d', $data_pedido)) {
        echo json_encode(["success" => false, "error" => "Data inválida."]);
        exit;
    }

    // 7. Update no banco de dados
    try {
        $sql = "UPDATE pedido 
                   SET usuario_id = :usuario_id, 
                       data_pedido = :data_pedido, 
                       status = :status, 
                       valor_total = :valor_total, 
                       endereco_entrega = :endereco_entrega
                 WHERE pedido_id = :pedido_id";

        $stmt = $pdo->prepare($sql);

        // Bind dos parâmetros para evitar injeção de SQL
        $stmt->bindParam(':pedido_id', $pedido_id, PDO::PARAM_INT);
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindParam(':data_pedido', $data_pedido);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':valor_total', $valor_total);
        $stmt->bindParam(':endereco_entrega', $endereco_entrega);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Pedido atualizado com sucesso."]);
        } else {
            echo json_encode(["success" => false, "error" => "Erro ao atualizar pedido."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => "Erro interno no banco de dados."]);
    }

} else {
    // Retorna um erro se o método não for POST
    echo json_encode(["success" => false, "error" => "Método inválido. Use POST."]);
}
?>