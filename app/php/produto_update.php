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

// 3. Somente POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "error" => "Método inválido. Use POST."]);
    exit;
}

// 4. Sanitização automática
require_once "../core/utils/Sanitize.php";
$sanitizer = new \core\utils\Sanitize(true, true, true); // requestVars, code, sql
$data = $sanitizer->getCleanRequestVars();

// 5. Receber dados do produto
$produto_id      = $data['produto_id'] ?? null;
$nome            = $data['nome'] ?? '';
$valor           = $data['valor'] ?? null;
$marca_id        = $data['marca_id'] ?? null;
$categoria_id    = $data['categoria_id'] ?? null;
$disponivel      = $data['disponivel'] ?? null;
$imagem_url      = $data['imagem_url'] ?? '';
$unidade_estoque = $data['unidade_estoque'] ?? null;
$promocao_id     = $data['promocao_id'] ?? null;
$descricao       = $data['descricao'] ?? '';

// 6. Validações básicas
if (!$produto_id || !$nome || !$valor || !$marca_id || !$categoria_id || $disponivel === null || !$unidade_estoque) {
    echo json_encode(["success" => false, "error" => "Todos os campos obrigatórios devem ser preenchidos."]);
    exit;
}

// 7. Update no banco
try {
    $sql = "UPDATE produto 
               SET nome = :nome, valor = :valor, marca_id = :marca_id, categoria_id = :categoria_id, 
                   disponivel = :disponivel, imagem_url = :imagem_url, unidade_estoque = :unidade_estoque, 
                   promocao_id = :promocao_id, descricao = :descricao
             WHERE id = :produto_id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':produto_id', $produto_id, PDO::PARAM_INT);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':valor', $valor);
    $stmt->bindParam(':marca_id', $marca_id, PDO::PARAM_INT);
    $stmt->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
    $stmt->bindParam(':disponivel', $disponivel, PDO::PARAM_INT);
    $stmt->bindParam(':imagem_url', $imagem_url);
    $stmt->bindParam(':unidade_estoque', $unidade_estoque, PDO::PARAM_INT);
    $stmt->bindParam(':promocao_id', $promocao_id, PDO::PARAM_INT);
    $stmt->bindParam(':descricao', $descricao);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Produto atualizado com sucesso."]);
    } else {
        echo json_encode(["success" => false, "error" => "Erro ao atualizar produto."]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Erro interno no banco de dados."]);
}
