<?php
// Conexão com o banco de dados
require_once "../etc/config.php";

try {
    $dsn = "mysql:host=" . $_SESSION['database']['host'] .
           ";dbname=" . $_SESSION['database']['schema'] .
           ";port=" . $_SESSION['database']['port'];

    $pdo = new PDO($dsn, $_SESSION['database']['user'], $_SESSION['database']['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Erro de conexão: " . $e->getMessage()]);
    exit;
}

// Receber dados via JSON
try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        echo json_encode(["success" => false, "error" => "JSON inválido ou não enviado."]);
        exit;
    }

    $nome = $data['nome'] ?? '';
    $senha = $data['senha'] ?? '';

    if (empty($nome) || empty($senha)) {
        echo json_encode(["success" => false, "error" => "Usuário e senha são obrigatórios."]);
        exit;
    }

    // Consulta o usuário no banco
    $sql = "SELECT * FROM usuario WHERE nome = ? LIMIT 1";
    $stmt = $pdo->prepare($sql);   
    $stmt->execute([$nome]);       
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (password_verify($senha, $user['senha'])) {
            $_SESSION['logado'] = true;
            $_SESSION['usuario'] = $nome;

            // JSON de sucesso
            echo json_encode(["success" => true, "message" => "Login bem-sucedido"]);
        } else {
            // Senha incorreta
            echo json_encode(["success" => false, "error" => "Senha incorreta."]);
        }
    } else {
        // Usuário não encontrado
        echo json_encode(["success" => false, "error" => "Usuário não encontrado."]);
    }

} catch (Throwable $e) {
    echo json_encode(["success" => false, "error" => "Exceção no servidor: " . $e->getMessage()]);
}