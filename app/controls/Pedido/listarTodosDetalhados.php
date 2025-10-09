<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__.'/../../models/PedidoDAO.php';
require_once __DIR__.'/../../models/UsuarioDAO.php'; 

try {
    $pedidoDAO   = new \app\models\PedidoDAO();
    $usuarioDAO  = new \app\models\UsuarioDAO();
    // $produtoDAO = new \app\models\ProdutoDAO();

    $pedidosBasicos = $pedidoDAO->getAll(); // Busca TODOS os pedidos

    $pedidosDetalhados = [];

    foreach ($pedidosBasicos as $pedido) {
        $usuario = $usuarioDAO->getById($pedido->getUsuarioId());
        // $produto = $produtoDAO->getById($pedido->getProdutoId()); // Futuramente

        $dadosCombinados = [
            'idPedido'          => $pedido->getIdPedido(),
            'valorTotal'        => $pedido->getValorTotal(),
            'status'            => $pedido->getStatus(),
            'dataPedido'        => $pedido->getDataPedido(),
            'quantidade'        => $pedido->getQuantidade(),
            'descricao'         => $pedido->getDescricao(),
            'nomeCliente'       => $usuario ? $usuario->getNome() : 'Não encontrado',
            'nomeProduto'       => 'Produto Fixo Exemplo' // Substituir pela busca real
        ];
        $pedidosDetalhados[] = $dadosCombinados;
    }

    echo json_encode($pedidosDetalhados, JSON_UNESCAPED_UNICODE);

} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno.', 'details' => $e->getMessage()]);
}