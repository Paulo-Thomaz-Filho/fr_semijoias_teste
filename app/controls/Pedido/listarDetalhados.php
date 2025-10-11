<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__.'/../../core/database/DBConnection.php';

try {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // 1. Obtém a conexão com o banco de dados
    $conn = (new \core\database\DBConnection())->getConn();

    // 2. Cria a consulta SQL única e otimizada com JOINs
    $sql = "
        SELECT 
            p.idPedido,
            p.valor_total       AS valorTotal,
            p.status,
            p.data_pedido       AS dataPedido,
            u.nome              AS nomeCliente,
            e.logradouro,
            e.numero,
            e.cidade
        FROM 
            pedidos p
        JOIN 
            usuarios u ON p.usuario_id = u.id
        JOIN 
            enderecos e ON p.endereco_id = e.idEnderecos
        WHERE 
            p.status = ?
        ORDER BY
            p.data_pedido DESC
    ";

    // 3. Prepara e executa a consulta de forma segura
    $stmt = $conn->prepare($sql);
    $stmt->execute(['Pendente']);

    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $pedidosDetalhados = [];
    foreach ($resultados as $row) {
        // 4. Monta o array final para cada pedido com o formato esperado pelo JavaScript
        $pedidosDetalhados[] = [
            'idPedido'          => $row['idPedido'],
            'valorTotal'        => $row['valorTotal'],
            'status'            => $row['status'],
            'dataPedido'        => $row['dataPedido'],
            'nomeCliente'       => $row['nomeCliente'],
            'enderecoCompleto'  => $row['logradouro'] . ', ' . $row['numero'] . ' - ' . $row['cidade']
        ];
    }

    // 5. Envia a resposta JSON para o front-end
    echo json_encode($pedidosDetalhados, JSON_UNESCAPED_UNICODE);

} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Ocorreu um erro interno no servidor ao processar os pedidos.', 
        'details' => $e->getMessage(), 
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}