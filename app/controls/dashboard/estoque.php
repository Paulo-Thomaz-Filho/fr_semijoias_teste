<?php
// app/controls/Dashboard/estoque.php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

require_once $rootPath . '/app/core/database/DBConnection.php';

try {
    $conn = (new \core\database\DBConnection())->getConn();
    
    // Query para buscar estatísticas de estoque por categoria
    $sql = "
        SELECT 
            c.nome as categoria,
            SUM(p.unidade_estoque) as total
        FROM 
            produtos p
        LEFT JOIN
            categorias c ON p.id_categoria = c.id_categoria
        GROUP BY 
            c.id_categoria, c.nome
        ORDER BY 
            total DESC
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $resultados = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
    // Se não houver dados, retornar array vazio
    if (empty($resultados)) {
        $resultados = [];
    }
    
    echo json_encode($resultados, JSON_UNESCAPED_UNICODE);

} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
