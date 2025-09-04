<?php
/**
 * Ponto de extremidade (endpoint) para autenticação de usuário.
 * Este script se integra com o 'config.php' do ZaitTinyFrameworkPHP.
 */

// Este script é um endpoint de API, então não precisa de saídas HTML.
// Oculta erros que não sejam fatais para garantir uma saída JSON limpa.
error_reporting(0);
ini_set('display_errors', 0);

// Define o cabeçalho da resposta como JSON
header('Content-Type: application/json');

// Inclui o arquivo de configuração para inicializar a sessão e as variáveis.
// O caminho '../etc/config.php' é relativo à localização de 'login.php' (app/php/).
try {
    // A variável $rootPath é necessária pelo seu config.php
    global $rootPath;
    $rootPath = dirname(__DIR__, 2); // Sobe dois níveis de app/php/ para a raiz do projeto

    require_once $rootPath . '/app/etc/config.php';
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Falha ao carregar o arquivo de configuração.']);
    exit;
}

// Bloco de Conexão com o Banco de Dados
try {
    // Usa as credenciais do banco que foram carregadas na sessão pelo config.php
    $dsn = "mysql:host=".$_SESSION['database']['host'].
           ";dbname=".$_SESSION['database']['schema'].
           ";port=".$_SESSION['database']['port'];
           
    $pdo = new PDO($dsn, $_SESSION['database']['user'], $_SESSION['database']['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    $pdo->exec("SET NAMES utf8mb4");

} catch(Throwable $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Erro ao conectar ao banco de dados."]);
    exit;
}

// Aceitar apenas requisições do tipo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Método inválido. Use POST."]);
    exit;
}

// Lógica Principal de Login
try {
    // Lê o corpo da requisição (que deve ser o JSON enviado pelo JavaScript)
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Verifica se o JSON é válido
    if (!$data) {
        http_response_code(400); // Bad Request
        echo json_encode(["success" => false, "error" => "JSON inválido ou não enviado."]);
        exit;
    }

    $email = $data['email'] ?? '';
    $senha = $data['senha'] ?? '';

    // Verifica se os campos essenciais foram enviados
    if (empty($email) || empty($senha)) {
        http_response_code(400); // Bad Request
        echo json_encode(["success" => false, "error" => "Usuário e senha são obrigatórios."]);
        exit;
    }

    // Consulta o usuário de forma segura usando prepared statements
    // Adicionamos "AND ativo = 1" como uma boa prática de segurança
    $sql = "SELECT * FROM usuario WHERE email = ? AND ativo = 1 LIMIT 1";
    
    $stmt = $pdo->prepare($sql);   
    $stmt->execute([$email]);       
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o usuário existe e se a senha (hash) corresponde
    if ($user && password_verify($senha, $user['senha_hash'])) {
        
        // Login bem-sucedido: regenera o ID da sessão para segurança
        session_regenerate_id(true); 
        
        // Define as variáveis de sessão para o usuário logado
        $_SESSION['logado'] = true;
        $_SESSION['login']['email'] = $user['email'];
        $_SESSION['login']['nome'] = $user['nome'];
        $_SESSION['login']['idUsuario'] = $user['usuario_id'];
        
        // Retorna uma resposta de sucesso
        echo json_encode(["success" => true, "message" => "Login bem-sucedido"]);

    } else {
        // Falha no login: usuário não encontrado, inativo, ou senha incorreta
        http_response_code(401); // Unauthorized
        echo json_encode(["success" => false, "error" => "Usuário ou senha inválidos."]);
    }

} catch (Throwable $e) {
    // Captura qualquer outra exceção que possa ocorrer
    http_response_code(500); // Internal Server Error
    error_log($e->getMessage()); // É uma boa prática logar o erro no servidor
    echo json_encode(["success" => false, "error" => "Ocorreu um erro inesperado no servidor."]);
}