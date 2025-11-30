<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../core/utils/MercadoPago.php';

use MercadoPago\Client\Preference\PreferenceClient;

try {
    // Recebe dados do produto via POST (exemplo)
    $data = json_decode(file_get_contents('php://input'), true);
    $title = $data['title'] ?? 'Produto';
    $quantity = $data['quantity'] ?? 1;
    $unit_price = $data['unit_price'] ?? 10.00;
    $id_pedido = $data['id_pedido'] ?? null; // Receba o id do pedido do seu sistema

    $client = new PreferenceClient();
    $preference = $client->create([
        "items" => [
            [
                "title" => $title,
                "quantity" => (int)$quantity,
                "unit_price" => (float)$unit_price
            ]
        ],
        "back_urls" => [
            "success" => "https://frsemijoias.ifhost.gru.br/sucesso",
            "failure" => "https://frsemijoias.ifhost.gru.br/erro",
            "pending" => "https://frsemijoias.ifhost.gru.br/pendente"
        ],
        "auto_return" => "approved",
        "external_reference" => $id_pedido
    ]);

    echo json_encode([
        'id' => $preference->id,
        'init_point' => $preference->init_point
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
