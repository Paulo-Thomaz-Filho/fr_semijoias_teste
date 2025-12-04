<?php

require_once __DIR__ . '/../../core/database/DBConnection.php';

use core\database\DBConnection;

header('Content-Type: application/json');

try {
    $conn = (new DBConnection())->getConn();
    
    // Busca todas as categorias únicas dos produtos
    $sql = "SELECT DISTINCT categoria FROM produtos WHERE categoria IS NOT NULL AND categoria != '' ORDER BY categoria";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    $categorias = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo json_encode($categorias);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao buscar categorias: ' . $e->getMessage()]);
}
