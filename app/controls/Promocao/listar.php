<?php
header('Content-Type: application/json');

require_once __DIR__.'/../../models/Promocao.php';
require_once __DIR__.'/../../models/PromocaoDAO.php';

try {
    $promocaoDAO = new \app\models\PromocaoDAO();
    $promocoes = $promocaoDAO->getAll();

    $promocoesArray = [];
    foreach ($promocoes as $promocao) {
        $promocoesArray[] = $promocao->toArray();
    }

    echo json_encode($promocoesArray);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Ocorreu um erro no servidor.']);
}