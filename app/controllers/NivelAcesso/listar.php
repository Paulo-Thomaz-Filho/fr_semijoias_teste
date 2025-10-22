<?php
header('Content-Type: application/json; charset=utf-8');

// Configurar o ambiente
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . '/app/etc/config.php';

require_once $rootPath . '/app/models/NivelAcesso.php';
require_once $rootPath . '/app/models/NivelAcessoDAO.php';

try {
    $nivelDAO = new \app\models\NivelAcessoDAO();
    $niveis = $nivelDAO->getAll();
    $niveisArray = [];
    foreach ($niveis as $nivel) {
        $niveisArray[] = $nivel->toArray();
    }
    echo json_encode($niveisArray, JSON_UNESCAPED_UNICODE);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao buscar níveis: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
