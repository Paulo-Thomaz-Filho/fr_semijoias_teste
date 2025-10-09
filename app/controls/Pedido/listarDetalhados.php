<?php
// Define o tipo de conteúdo da resposta como JSON, com suporte para caracteres UTF-8
header('Content-Type: application/json; charset=utf-8');

// Inclui as classes DAO e Modelo necessárias para a operação
// (Num projeto maior, um autoloader faria este trabalho)
require_once __DIR__.'/../../models/PedidoDAO.php';
require_once __DIR__.'/../../models/UsuarioDAO.php';
require_once __DIR__.'/../../models/EnderecoDAO.php';

try {
    // Inicia a sessão se ainda não estiver ativa, para garantir acesso a variáveis globais se necessário
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Instancia os Data Access Objects (DAOs) para cada entidade
    $pedidoDAO   = new \app\models\PedidoDAO();
    $usuarioDAO  = new \app\models\UsuarioDAO();
    $enderecoDAO = new \app\models\EnderecoDAO();

    // 1. Busca no banco de dados APENAS os pedidos que têm o status "Pendente"
    $pedidosBasicos = $pedidoDAO->getAllByStatus('Pendente');

    // Inicializa um array para armazenar os dados combinados
    $pedidosDetalhados = [];

    // 2. Itera sobre cada pedido "Pendente" encontrado
    foreach ($pedidosBasicos as $pedido) {
        
        // 3. Para cada pedido, busca o objeto de usuário completo usando o ID do usuário
        $usuario = $usuarioDAO->getById($pedido->getUsuarioId());
        
        // 4. Para cada pedido, busca o objeto de endereço completo usando o ID do endereço
        $endereco = $enderecoDAO->getById($pedido->getEnderecoId());

        // 5. Monta uma estrutura de dados final e limpa para o front-end
        //    Utiliza operadores ternários para lidar com casos onde o usuário ou endereço podem não ser encontrados
        $dadosCombinados = [
            'idPedido'          => $pedido->getIdPedido(),
            'valorTotal'        => $pedido->getValorTotal(),
            'status'            => $pedido->getStatus(),
            'dataPedido'        => $pedido->getDataPedido(),
            'nomeCliente'       => $usuario ? $usuario->getNome() : 'Usuário não encontrado',
            'enderecoCompleto'  => $endereco ? $endereco->getLogradouro() . ', ' . $endereco->getNumero() . ' - ' . $endereco->getCidade() : 'Endereço não encontrado'
        ];

        // Adiciona os dados combinados do pedido atual ao array final
        $pedidosDetalhados[] = $dadosCombinados;
    }

    // 6. Envia a resposta final em formato JSON, com os caracteres especiais (como acentos) preservados
    echo json_encode($pedidosDetalhados, JSON_UNESCAPED_UNICODE);

} catch (\Throwable $e) {
    // Em caso de qualquer erro ou exceção durante o processo, define o status da resposta HTTP para 500 (Erro Interno do Servidor)
    http_response_code(500);
    
    // Envia uma resposta de erro detalhada em JSON para facilitar a depuração
    echo json_encode([
        'error' => 'Ocorreu um erro interno no servidor ao processar os pedidos.', 
        'details' => $e->getMessage(), 
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}