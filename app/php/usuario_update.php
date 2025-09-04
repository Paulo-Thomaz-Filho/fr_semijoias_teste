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
    echo json_encode(["success" => false, "error" => "Erro de conexão com o banco de dados."]);
    exit;
}

// 2. Verificação de login
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: " . $_SESSION['url']['root'] . "login.html");
    exit;
}

// 3. Apenas POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "error" => "Método inválido. Use POST."]);
    exit;
}

// 4. Sanitização automática
require_once "../core/utils/Sanitize.php";
$sanitizer = new \core\utils\Sanitize(true, true, true); 
$data = $sanitizer->getCleanRequestVars();

// 5. Receber dados já sanitizados
$usuario_id     = $data['usuario_id'] ?? null;
$nome           = $data['nome'] ?? '';
$email          = $data['email'] ?? '';
$senha          = $data['senha'] ?? ''; 
$idNivelUsuario = $data['idNivelUsuario'] ?? null;
$cpf            = $data['cpf'] ?? '';
$endereco       = $data['endereco'] ?? '';
$bairro         = $data['bairro'] ?? '';
$cidade         = $data['cidade'] ?? '';
$uf             = $data['uf'] ?? '';
$cep            = $data['cep'] ?? '';
$telefone       = $data['telefone'] ?? '';
$foto           = $data['foto'] ?? '';
$ativo          = isset($data['ativo']) ? (int)$data['ativo'] : 1;

// 6. Validação mínima
if (!$usuario_id || !$nome || !$email || !$idNivelUsuario) {
    echo json_encode(["success" => false, "error" => "Campos obrigatórios não preenchidos."]);
    exit;
}

// 7. Monta SQL dinâmico para senha (opcional)
$sql = "UPDATE usuario SET 
            nome = :nome,
            email = :email,
            idNivelUsuario = :idNivelUsuario,
            cpf = :cpf,
            endereco = :endereco,
            bairro = :bairro,
            cidade = :cidade,
            uf = :uf,
            cep = :cep,
            telefone = :telefone,
            foto = :foto,
            ativo = :ativo";

if (!empty($senha)) {
    $sql .= ", senha = :senha";
}

$sql .= " WHERE usuario_id = :usuario_id";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':idNivelUsuario', $idNivelUsuario, PDO::PARAM_INT);
    $stmt->bindParam(':cpf', $cpf);
    $stmt->bindParam(':endereco', $endereco);
    $stmt->bindParam(':bairro', $bairro);
    $stmt->bindParam(':cidade', $cidade);
    $stmt->bindParam(':uf', $uf);
    $stmt->bindParam(':cep', $cep);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':foto', $foto);
    $stmt->bindParam(':ativo', $ativo, PDO::PARAM_INT);

    if (!empty($senha)) {
        $hashedPassword = password_hash($senha, PASSWORD_DEFAULT);
        $stmt->bindParam(':senha', $hashedPassword);
    }

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Usuário atualizado com sucesso."]);
    } else {
        echo json_encode(["success" => false, "error" => "Erro ao atualizar usuário."]);
    }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Erro interno no banco de dados."]);
}
