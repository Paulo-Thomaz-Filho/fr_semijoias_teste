<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

require_once $rootPath . '/app/models/Marca.php';
require_once $rootPath . '/app/models/MarcaDAO.php';

try {
    $marcaDAO = new \app\models\MarcaDAO();
    $marcas = $marcaDAO->getAll();

    $marcasArray = [];
    foreach ($marcas as $marca) {
        $marcasArray[] = $marca->toArray();
    }

    echo json_encode($marcasArray, JSON_UNESCAPED_UNICODE);

} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}