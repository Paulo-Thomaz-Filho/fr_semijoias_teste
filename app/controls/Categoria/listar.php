<?php
header('Content-Type: application/json');

require_once '../../models/Categoria.php';
require_once '../../models/CategoriaDAO.php';

try {
    $categoriaDAO = new \app\models\CategoriaDAO();
    $categorias = $categoriaDAO->getAll();

    $categoriasArray = [];
    foreach ($categorias as $categoria) {
        $categoriasArray[] = $categoria->toArray();
    }

    echo json_encode($categoriasArray);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Ocorreu um erro no servidor.']);
}