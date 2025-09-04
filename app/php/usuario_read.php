<?php
// Conexão com o banco de dados
require_once "../etc/config.php"; // Carrega sessão e configurações do banco

try {
    // Monta DSN para PDO usando os dados da sessão
    $dsn = "mysql:host=" . $_SESSION['database']['host'] .
           ";dbname=" . $_SESSION['database']['schema'] .
           ";port=" . $_SESSION['database']['port'];

    // Cria a conexão PDO
    $pdo = new PDO($dsn, $_SESSION['database']['user'], $_SESSION['database']['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Erro de conexão: " . $e->getMessage()]);
    exit;
}

// Redirecionar se não estiver logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: " . $_SESSION['url']['root'] . "login.html");
    exit;
}

// Buscar dados de usuarios
$sql = "SELECT * FROM usuario";
$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC); 

$usuarios = [];
foreach ($rows as $row) {
    $usuario = new usuario(); 
    $usuario->load(         
        $row['usuario_id'],
        $row['email'],
        $row['senha'],
        $row['idNivelUsuario'],
        $row['nome'],
        $row['cpf'],
        $row['endereco'],
        $row['bairro'],
        $row['cidade'],
        $row['uf'],
        $row['cep'],
        $row['telefone'],
        $row['foto'],
        $row['ativo'],
    );
    $usuarios[] = $usuario->toArray(); 
}

// Retorna os usuarios em JSON
header("Content-Type: application/json; charset=utf-8");
echo json_encode($usuarios);
