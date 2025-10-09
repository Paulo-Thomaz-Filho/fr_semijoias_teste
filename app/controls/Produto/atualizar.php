<?php
// Em: app/controls/Produto/atualizar.php

header('Content-Type: application/json; charset=utf-8');

// 1. Inclusão dos modelos necessários
require_once __DIR__.'/../../models/Produto.php';
require_once __DIR__.'/../../models/ProdutoDAO.php';

// 2. Leitura dos dados do formulário (enviados via POST pelo JavaScript)
$id = $_POST['IdProduto'] ?? null;
$nome = $_POST['nome'] ?? null;
$descricao = $_POST['descricao'] ?? ''; // Descrição é opcional
$categoria = $_POST['categoria'] ?? null;
$marca = $_POST['marca'] ?? null;
$valor = $_POST['valor'] ?? null;
$idPromocao = $_POST['idPromocao'] ?? null;

// 3. Validação dos dados obrigatórios
if (!$id) {
    http_response_code(400); // Bad Request
    echo json_encode(['erro' => 'O IdProduto é obrigatório para atualização.']);
    exit;
}

if (!$nome || !$categoria || !$marca || !$valor) {
    http_response_code(400); // Bad Request
    echo json_encode(['erro' => 'Dados incompletos. Nome, Categoria, Marca e Valor são obrigatórios.']);
    exit;
}

try {
    // 4. Instancia o DAO
    $produtoDAO = new \app\models\ProdutoDAO();

    // 5. Verifica se o produto que está sendo atualizado realmente existe
    $produtoExistente = $produtoDAO->getById($id);
    if (!$produtoExistente) {
        http_response_code(404); // Not Found
        echo json_encode(['erro' => 'Produto não encontrado para atualização.']);
        exit;
    }

    // 6. Cria um novo objeto Produto com os dados atualizados
    $produtoAtualizado = new \app\models\Produto(
        $id,
        $nome,
        $descricao,
        $valor,
        $marca,
        $categoria,
        empty($idPromocao) ? null : $idPromocao // Garante que o valor seja null se o select estiver vazio
    );

    // 7. Chama o método de atualização no DAO
    if ($produtoDAO->update($produtoAtualizado)) {
        // Se a atualização for bem-sucedida, retorna o objeto atualizado
        echo json_encode($produtoAtualizado->toArray());
    } else {
        // Se a atualização falhar por um motivo interno do DAO
        http_response_code(500); // Internal Server Error
        echo json_encode(['erro' => 'Ocorreu um erro ao atualizar o produto.']);
    }

} catch (Exception $e) {
    // Captura qualquer outra exceção que possa ocorrer (ex: falha de conexão com o banco)
    http_response_code(500); // Internal Server Error
    echo json_encode(['erro' => 'Ocorreu um erro no servidor.', 'details' => $e->getMessage()]);
}