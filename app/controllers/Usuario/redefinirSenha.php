<?php
header('Content-Type: application/json');


require_once __DIR__ . '/../../core/database/DBConnection.php';
use core\database\DBConnection;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$token = isset($data['token']) ? trim($data['token']) : '';
$newPassword = isset($data['password']) ? $data['password'] : '';

if (strlen($newPassword) < 6) {
    http_response_code(400);
    echo json_encode(['error' => 'A senha deve ter pelo menos 6 caracteres']);
    exit;
}

$dbObj = new DBConnection();
$db = $dbObj->getConn();
$stmt = $db->prepare('SELECT usuario_id, expira FROM redefinicao_senha WHERE token = ? LIMIT 1');
$stmt->execute([$token]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row || strtotime($row['expira']) < time()) {
    http_response_code(400);
    echo json_encode(['error' => 'Token inválido ou expirado']);
    exit;
}

$usuarioId = $row['usuario_id'];
$hash = password_hash($newPassword, PASSWORD_DEFAULT);

// Atualiza senha
$stmt = $db->prepare('UPDATE usuarios SET senha = ? WHERE id_usuario = ?');
$stmt->execute([$hash, $usuarioId]);

// Invalida token
$stmt = $db->prepare('DELETE FROM redefinicao_senha WHERE token = ?');
$stmt->execute([$token]);

echo json_encode(['success' => true]);
