<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

require_once $rootPath . '/app/core/database/DBConnection.php';

try {
    $conn = (new \core\database\DBConnection())->getConn();
    
    // Query para buscar os produtos mais vendidos apenas com status Concluído ou Enviado
    $sql = "
        SELECT 
            ped.produto_nome as nome,
            COALESCE(p.categoria, 'N/A') as categoria,
            SUM(ped.quantidade) as total_vendido
        FROM 
            pedidos ped
        LEFT JOIN 
            produtos p ON ped.produto_nome = p.nome
        INNER JOIN 
            status s ON ped.id_status = s.id_status
        WHERE 
            s.nome IN ('Concluído', 'Enviado')
        GROUP BY 
            ped.produto_nome, p.categoria
        ORDER BY 
            total_vendido DESC
        LIMIT 6
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
