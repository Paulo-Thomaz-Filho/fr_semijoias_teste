<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

require_once $rootPath . '/app/models/Promocao.php';
require_once $rootPath . '/app/models/PromocaoDAO.php';

try {
    $promocaoDAO = new \app\models\PromocaoDAO();
    $promocoes = $promocaoDAO->getAll();

    $promocoesArray = [];
    foreach ($promocoes as $promocao) {
        $promocoesArray[] = $promocao->toArray();
    }

    echo json_encode($promocoesArray, JSON_UNESCAPED_UNICODE);

} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}