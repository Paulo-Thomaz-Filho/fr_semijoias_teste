<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';
require_once $rootPath . '/app/models/Produto.php';
require_once $rootPath . '/app/models/ProdutoDAO.php';

try {
    // Remove promoções expiradas dos produtos antes de listar
    require_once $rootPath . '/app/models/PromocaoDAO.php';
    $promocaoDAO = new \app\models\PromocaoDAO();
    $promocaoDAO->removerPromocoesExpiradasDosProdutos();

    $produtoDAO = new \app\models\ProdutoDAO();
    $promocaoDAO = new \app\models\PromocaoDAO();
    $produtos = $produtoDAO->getAll();
    $produtosArray = [];
    foreach ($produtos as $produto) {
        $arr = $produto->toArray();
        // Se o produto tem promoção, inclui detalhes
        if (!empty($arr['idPromocao'])) {
            $promocao = $promocaoDAO->getById($arr['idPromocao']);
            if ($promocao) {
                $tipo = $promocao->getTipoDesconto();
                $valor = $promocao->getDesconto();
                if ($tipo === 'percentual') {
                    $valorFormatado = intval($valor);
                } else {
                    $valorFormatado = round($valor);
                }
                $arr['promocao'] = [
                    'valor' => $valorFormatado,
                    'tipo' => ($tipo === 'percentual' ? 'percent' : 'currency'),
                ];
            }
        }
        $produtosArray[] = $arr;
    }
    echo json_encode($produtosArray, JSON_UNESCAPED_UNICODE);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
