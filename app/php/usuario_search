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

// 3. Sanitização automática
require_once "../core/utils/Sanitize.php";
$sanitizer = new \core\utils\Sanitize(true, true, true);
$data = $sanitizer->getCleanRequestVars();

// 4. Recebe o ID do usuário via GET ou POST
$nome = isset($data['nome']) ? (int)$data['nome'] : null;

if (!$nome) {
    echo json_encode(["success" => false, "error" => "ID do usuário inválido."]);
    exit;
}

// 5. Buscar usuário específico
try {
    $sql = "SELECT * FROM usuario WHERE nome = :nome LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome, PDO::PARAM_INT);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // Sanitização dos campos do usuário
        $usuario = [
            'usuario_id'     => (int) $row['nome'],
            'nome'           => $sanitizer->sanitizeString($row['nome']),
            'email'          => $sanitizer->sanitizeString($row['email']),
            'cpf'            => $sanitizer->sanitizeString($row['cpf']),
            'endereco'       => $sanitizer->sanitizeString($row['endereco']),
            'bairro'         => $sanitizer->sanitizeString($row['bairro']),
            'cidade'         => $sanitizer->sanitizeString($row['cidade']),
            'uf'             => $sanitizer->sanitizeString($row['uf']),
            'cep'            => $sanitizer->sanitizeString($row['cep']),
            'telefone'       => $sanitizer->sanitizeString($row['telefone']),
            'foto'           => $sanitizer->sanitizeString($row['foto']),
            'idNivelUsuario' => (int) $row['idNivelUsuario'],
            'ativo'          => (int) $row['ativo']
        ];

        echo json_encode(["success" => true, "usuario" => $usuario]);
    } else {
        echo json_encode(["success" => false, "error" => "Usuário não encontrado."]);
    }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Erro interno no banco de dados."]);
}
