<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';
require_once $rootPath . '/app/core/database/DBConnection.php';

try {
    $conn = (new \core\database\DBConnection())->getConn();

    // 1. Total de Ganhos (soma de todos os pedidos)
    $sqlGanhos = "SELECT COALESCE(SUM(preco * quantidade), 0) as total_ganhos FROM pedidos";
    $stmtGanhos = $conn->prepare($sqlGanhos);
    $stmtGanhos->execute();
    $totalGanhos = $stmtGanhos->fetch(\PDO::FETCH_ASSOC)['total_ganhos'];

    // 2. Total de Usuários Cadastrados
    $sqlUsuarios = "SELECT COUNT(*) as total_usuarios FROM usuarios";
    $stmtUsuarios = $conn->prepare($sqlUsuarios);
    $stmtUsuarios->execute();
    $totalUsuarios = $stmtUsuarios->fetch(\PDO::FETCH_ASSOC)['total_usuarios'];

    // 3. Vendas do Mês Atual (últimos 30 dias)
    $sqlVendasMes = "SELECT COUNT(*) as vendas_mes FROM pedidos WHERE data_pedido >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    $stmtVendasMes = $conn->prepare($sqlVendasMes);
    $stmtVendasMes->execute();
    $vendasMes = $stmtVendasMes->fetch(\PDO::FETCH_ASSOC)['vendas_mes'];

    // 4. Produto Mais Pedido
    $sqlMaisPedido = "
        SELECT produto_nome, SUM(quantidade) as total_pedido
        FROM pedidos
        GROUP BY produto_nome
        ORDER BY total_pedido DESC
        LIMIT 1
    ";
    $stmtMaisPedido = $conn->prepare($sqlMaisPedido);
    $stmtMaisPedido->execute();
    $maisPedido = $stmtMaisPedido->fetch(\PDO::FETCH_ASSOC);
    $produtoMaisPedido = $maisPedido ? $maisPedido['produto_nome'] : 'N/A';

    // Monta o objeto de resposta
    $resposta = [
        'total_ganhos' => (float) $totalGanhos,
        'total_usuarios' => (int) $totalUsuarios,
        'vendas_mes' => (int) $vendasMes,
        'produto_mais_pedido' => $produtoMaisPedido
    ];

    echo json_encode($resposta, JSON_UNESCAPED_UNICODE);

} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
