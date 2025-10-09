<?php
// Em: app/controls/Endereco/buscarPorUsuario.php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__.'/../../models/EnderecoDAO.php';

// 1. Pega o ID do usuário enviado pela URL (ex: ?id=123)
$usuarioId = $_GET['id'] ?? null;

if (!$usuarioId) {
    http_response_code(400); // Bad Request
    echo json_encode(['erro' => 'O ID do usuário é obrigatório.']);
    exit;
}

try {
    // 2. Instancia o DAO
    $enderecoDAO = new \app\models\EnderecoDAO();
    
    // 3. Chama o método para buscar os endereços pelo ID do usuário
    $enderecos = $enderecoDAO->getByUsuarioId($usuarioId);

    // 4. Retorna os dados encontrados em formato JSON
    echo json_encode($enderecos);

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['erro' => 'Ocorreu um erro no servidor ao buscar endereços.', 'details' => $e->getMessage()]);
}