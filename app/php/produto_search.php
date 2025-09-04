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

// 3. Recebe o nome do produto via GET ou POST
require_once "../core/utils/Sanitize.php";
$sanitizer = new \core\utils\Sanitize(true, true, true);
$data = $sanitizer->getCleanRequestVars();

$nome = isset($data['nome']) ? (int)$data['nome'] : null;

if (!$nome) {
    echo json_encode(["success" => false, "error" => "nome do produto inválido."]);
    exit;
}

// 4. Buscar produto específico
try {
    $sql = "SELECT * FROM produto WHERE nome = :nome LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome, PDO::PARAM_INT);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // Sanitização dos campos do produto
        $produto = [
            'produto_id'      => (int) $row['produto_id'],
            'nome'            => $sanitizer->sanitizeString($row['nome']),
            'valor'           => (float) $row['valor'],
            'marca_id'        => (int) $row['marca_id'],
            'categoria_id'    => (int) $row['categoria_id'],
            'ativo'           => (int) $row['ativo'],
            'imagem_url'      => $sanitizer->sanitizeString($row['imagem_url']),
            'unidade_estoque' => (int) $row['unidade_estoque'],
            'promocao_id'     => isset($row['promocao_id']) ? (int)$row['promocao_id'] : null,
            'descricao'       => $sanitizer->sanitizeString($row['descricao'])
        ];

        echo json_encode(["success" => true, "produto" => $produto]);
    } else {
        echo json_encode(["success" => false, "error" => "Produto não encontrado."]);
    }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Erro interno no banco de dados."]);
}
