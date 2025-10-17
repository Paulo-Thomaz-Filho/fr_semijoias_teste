<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

require_once $rootPath . '/app/models/Categoria.php';
require_once $rootPath . '/app/models/CategoriaDAO.php';

try {
    $categoriaDAO = new \app\models\CategoriaDAO();
    $categorias = $categoriaDAO->getAll();

    $categoriasArray = [];
    foreach ($categorias as $categoria) {
        $categoriasArray[] = $categoria->toArray();
    }

    echo json_encode($categoriasArray, JSON_UNESCAPED_UNICODE);

} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
