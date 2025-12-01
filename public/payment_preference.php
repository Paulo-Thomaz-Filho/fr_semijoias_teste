<?php
// Endpoint público para criar preferência de pagamento no Mercado Pago
header('Content-Type: application/json; charset=utf-8');

try {
    // 1. Carregar autoload do Composer
    $autoloadPath = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoloadPath)) {
        throw new Exception('Vendor autoload não encontrado');
    }
    require_once $autoloadPath;
    
    // 2. Carregar variáveis de ambiente do .env
    $envPath = __DIR__ . '/../.env';
    if (file_exists($envPath)) {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || $line[0] === '#') continue;
            if (strpos($line, '=') === false) continue;
            
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value, '"\''));
        }
    }
    
    // 3. Verificar access token
    $accessToken = getenv('MERCADO_PAGO_ACCESS_TOKEN');
    if (!$accessToken) {
        throw new Exception('MERCADO_PAGO_ACCESS_TOKEN não configurado no .env');
    }
    
    // 4. Configurar SDK do Mercado Pago
    MercadoPago\MercadoPagoConfig::setAccessToken($accessToken);
    
    // 5. Receber dados do POST
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data || !is_array($data)) {
        throw new Exception('Dados inválidos enviados na requisição');
    }
    
    // Processar items do carrinho
    $items = [];
    
    // Se recebeu array de items (carrinho completo)
    if (isset($data['items']) && is_array($data['items'])) {
        foreach ($data['items'] as $item) {
            $items[] = [
                "title" => $item['title'] ?? 'Produto',
                "quantity" => (int)($item['quantity'] ?? 1),
                "unit_price" => (float)($item['unit_price'] ?? 0)
            ];
        }
    } 
    // Se recebeu um único produto (compatibilidade com código antigo)
    else {
        $items[] = [
            "title" => $data['title'] ?? 'Produto',
            "quantity" => (int)($data['quantity'] ?? 1),
            "unit_price" => (float)($data['unit_price'] ?? 10.00)
        ];
    }
    
    if (empty($items)) {
        throw new Exception('Nenhum item para processar');
    }
    
    $id_pedido = $data['id_pedido'] ?? null;
    
    // 6. Criar preferência de pagamento
    $client = new MercadoPago\Client\Preference\PreferenceClient();
    $preference = $client->create([
        "items" => $items,
        "back_urls" => [
            "success" => "https://frsemijoias.ifhost.gru.br/sucesso",
            "failure" => "https://frsemijoias.ifhost.gru.br/erro",
            "pending" => "https://frsemijoias.ifhost.gru.br/pendente"
        ],
        "auto_return" => "approved",
        "external_reference" => $id_pedido
    ]);
    
    // 7. Retornar resposta de sucesso
    echo json_encode([
        'id' => $preference->id,
        'init_point' => $preference->init_point
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'file' => basename($e->getFile()),
        'line' => $e->getLine()
    ]);
}
