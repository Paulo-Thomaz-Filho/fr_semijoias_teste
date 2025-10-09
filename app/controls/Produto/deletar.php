<?php
// Em: app/controls/Produto/deletar.php
header('Content-Type: application/json');

require_once __DIR__.'/../../models/ProdutoDAO.php';

// Usamos $_POST pois o JavaScript envia os dados assim
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

$id = $_POST['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['erro' => 'O ID do produto é obrigatório para desativar.']);
    exit;
}

try {
    $produtoDAO = new \app\models\ProdutoDAO();

    // ALTERADO: Chamando o novo método 'desativar'
    if ($produtoDAO->desativar($id)) {
        echo json_encode(['sucesso' => 'Produto desativado com sucesso.']);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Ocorreu um erro ao desativar o produto.']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Ocorreu um erro no servidor.', 'details' => $e->getMessage()]);
}