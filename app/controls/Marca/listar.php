<?php
header('Content-Type: application/json');

require_once '../../models/Marca.php';
require_once '../../models/MarcaDAO.php';

try {
    $marcaDAO = new \app\models\MarcaDAO();
    $marcas = $marcaDAO->getAll();

    $marcasArray = [];
    foreach ($marcas as $marca) {
        $marcasArray[] = $marca->toArray();
    }

    echo json_encode($marcasArray);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Ocorreu um erro no servidor.']);
}