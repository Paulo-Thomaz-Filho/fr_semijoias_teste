<?php

// 1. Conexão com o banco de dados e sessão
require_once "../etc/config.php"; 

try {
    // Monta o DSN 
    $dsn = "mysql:host=" . $_SESSION['database']['host'] .
           ";dbname=" . $_SESSION['database']['schema'] .
           ";port=" . $_SESSION['database']['port'];

    // Cria a conexão PDO
    $pdo = new PDO($dsn, $_SESSION['database']['user'], $_SESSION['database']['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Erro interno de conexão."]);
    exit;
}

// 2. Verificação de login
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    // Se não estiver logado, redireciona para a página de login
    header("Location: " . $_SESSION['url']['root'] . "login.html");
    exit;
}

// 3. Verificação do método HTTP
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 4. Recebendo e validando os dados enviados pelo formulário
    $usuario_id       = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT); 
    $data_pedido      = $_POST['data_pedido'] ?? null; 
    $status           = $_POST['status'] ?? null; 
    $valor_total      = filter_input(INPUT_POST, 'valor_total', FILTER_VALIDATE_FLOAT); 
    $endereco_entrega = htmlspecialchars(trim($_POST['endereco_entrega'] ?? ''), ENT_QUOTES, 'UTF-8'); 

    $statusPermitidos = ['pendente', 'pago', 'cancelado'];

    if (!$usuario_id || !$data_pedido || !$status || !$valor_total || !$endereco_entrega) {
        echo json_encode(["success" => false, "error" => "Todos os campos são obrigatórios."]);
        exit;
    }
    if (!in_array($status, $statusPermitidos)) {
        echo json_encode(["success" => false, "error" => "Status inválido."]);
        exit;
    }
    if (!DateTime::createFromFormat('Y-m-d', $data_pedido)) {
        echo json_encode(["success" => false, "error" => "Data inválida."]);
        exit;
    }

    // 5. Inserção no banco de dados
    try {
        $sql = "INSERT INTO pedido (usuario_id, data_pedido, status, valor_total, endereco_entrega) 
                VALUES (:usuario_id, :data_pedido, :status, :valor_total, :endereco_entrega)";
        
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindParam(':data_pedido', $data_pedido);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':valor_total', $valor_total);
        $stmt->bindParam(':endereco_entrega', $endereco_entrega);

        if ($stmt->execute()) {
            // Sucesso
            echo json_encode(["success" => true, "message" => "Pedido cadastrado com sucesso."]);
        } else {
            // falha da execução
            echo json_encode(["success" => false, "error" => "Erro ao cadastrar pedido."]);
        }
    } catch (PDOException $e) {
        // erro do banco
        echo json_encode(["success" => false, "error" => "Erro interno no banco de dados."]);
    }

} else {
    echo json_encode(["success" => false, "error" => "Método inválido. Use POST."]);
}
