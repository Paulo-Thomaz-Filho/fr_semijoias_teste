<?php
// Ficheiro: app/controls/Dashboard/estatisticas.php

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

    // 3. Vendas do Mês Atual
    $sqlVendasMes = "SELECT COUNT(*) as vendas_mes FROM pedidos WHERE MONTH(data_pedido) = MONTH(CURRENT_DATE()) AND YEAR(data_pedido) = YEAR(CURRENT_DATE())";
    $stmtVendasMes = $conn->prepare($sqlVendasMes);
    $stmtVendasMes->execute();
    $vendasMes = $stmtVendasMes->fetch(\PDO::FETCH_ASSOC)['vendas_mes'];

    // 4. Produto Mais Vendido
    $sqlMaisVendido = "
        SELECT produto_nome, SUM(quantidade) as total_vendido
        FROM pedidos
        GROUP BY produto_nome
        ORDER BY total_vendido DESC
        LIMIT 1
    ";
    $stmtMaisVendido = $conn->prepare($sqlMaisVendido);
    $stmtMaisVendido->execute();
    $maisVendido = $stmtMaisVendido->fetch(\PDO::FETCH_ASSOC);
    $produtoMaisVendido = $maisVendido ? $maisVendido['produto_nome'] : 'N/A';

    // Monta o objeto de resposta
    $resposta = [
        'total_ganhos' => (float) $totalGanhos,
        'total_usuarios' => (int) $totalUsuarios,
        'vendas_mes' => (int) $vendasMes,
        'produto_mais_vendido' => $produtoMaisVendido
    ];

    echo json_encode($resposta, JSON_UNESCAPED_UNICODE);

} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}