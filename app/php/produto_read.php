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

// 3. Sanitização (mesmo que aqui não venha input externo, boa prática)
$sanitizer = new \core\utils\Sanitize(true, true, true);

// 4. Buscar dados de produtos com prepared statement
try {
    $stmt = $pdo->prepare("SELECT * FROM produto");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $produtos = [];
    foreach ($rows as $row) {
        $produto = new Produto();
        $produto->load(
            $row['produto_id'],
            $row['nome'],
            $row['valor'],
            $row['avaliacao'] ?? null,
            $row['marca_id'],
            $row['categoria_id'],
            $row['disponivel'],
            $row['imagem_url'],
            $row['unidade_estoque'],
            $row['promocao_id'] ?? null
        );
        $produtos[] = $produto->toArray();
    }

    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($produtos);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Erro interno no banco de dados."]);
}
