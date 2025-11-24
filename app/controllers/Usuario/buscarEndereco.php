<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente (ajuste o caminho se necessário)
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';
require_once $rootPath . '/app/models/Usuario.php';
require_once $rootPath . '/app/models/UsuarioDAO.php';

// O método deve ser GET para buscar dados
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido. Utilize GET.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Obter o id do usuario através da sessão
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$idUsuario = $_SESSION['usuario_id'] ?? null;

if (!$idUsuario) {
    http_response_code(401); // Não autorizado
    echo json_encode(['erro' => 'Usuário não autenticado ou sessão expirada.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // 1. Instanciar o DAO
    $usuarioDao = new \app\models\UsuarioDAO();

    // 2. Obter o usuário pelo ID. Assumindo que getById está implementado no UsuarioDAO.
    $usuario = $usuarioDao->getById($idUsuario);

    if (!$usuario) {
        http_response_code(404); // Não Encontrado
        echo json_encode(['erro' => 'Dados do usuário não encontrados.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 3. Obter o endereço
    $enderecoCompleto = $usuario->getEndereco();

    // 4. Responder com o endereço
    http_response_code(200);
    echo json_encode(['sucesso' => true, 'endereco' => $enderecoCompleto], JSON_UNESCAPED_UNICODE);
    
} catch (\Exception $e) {
    http_response_code(500); // Erro Interno do Servidor
    error_log("Erro ao buscar endereço: " . $e->getMessage());
    echo json_encode(['erro' => 'Erro interno ao buscar o endereço.'], JSON_UNESCAPED_UNICODE);
}
?>