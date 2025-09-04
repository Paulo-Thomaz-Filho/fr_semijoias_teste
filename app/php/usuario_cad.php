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

// 3. Verificação do método HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "error" => "Método inválido. Use POST."]);
    exit;
}

// 4. Sanitização automática
require_once "../core/utils/Sanitize.php";
$sanitizer = new \core\utils\Sanitize(true, true, true); // requestVars, code, sql
$data = $sanitizer->getCleanRequestVars();

// 5. Receber dados já sanitizados
$usuario_id     = $data['usuario_id'] ?? null; // se for auto_increment, pode remover
$email          = $data['email'] ?? null;
$senha          = $data['senha'] ?? null;
$idNivelUsuario = $data['idNivelUsuario'] ?? null;
$nome           = $data['nome'] ?? null;
$cpf            = $data['cpf'] ?? null;
$endereco       = $data['endereco'] ?? null;
$bairro         = $data['bairro'] ?? null;
$cidade         = $data['cidade'] ?? null;
$uf             = $data['uf'] ?? null;
$cep            = $data['cep'] ?? null;
$telefone       = $data['telefone'] ?? null;
$foto           = $data['foto'] ?? null;
$ativo          = $data['ativo'] ?? 1;

// 6. Validação básica
if (!$email || !$senha || !$nome || !$idNivelUsuario) {
    echo json_encode(["success" => false, "error" => "Campos obrigatórios não preenchidos."]);
    exit;
}

// 7. Inserção no banco de dados
try {
    $sql = "INSERT INTO usuario 
            (email, senha, idNivelUsuario, nome, cpf, endereco, bairro, cidade, uf, cep, telefone, foto, ativo)
            VALUES 
            (:email, :senha, :idNivelUsuario, :nome, :cpf, :endereco, :bairro, :cidade, :uf, :cep, :telefone, :foto, :ativo)";

    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':senha', $senha); // ideal: hash com password_hash()
    $stmt->bindParam(':idNivelUsuario', $idNivelUsuario, PDO::PARAM_INT);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':cpf', $cpf);
    $stmt->bindParam(':endereco', $endereco);
    $stmt->bindParam(':bairro', $bairro);
    $stmt->bindParam(':cidade', $cidade);
    $stmt->bindParam(':uf', $uf);
    $stmt->bindParam(':cep', $cep);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':foto', $foto);
    $stmt->bindParam(':ativo', $ativo, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Usuário cadastrado com sucesso."]);
    } else {
        echo json_encode(["success" => false, "error" => "Erro ao cadastrar usuário."]);
    }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Erro interno no banco de dados."]);
}
