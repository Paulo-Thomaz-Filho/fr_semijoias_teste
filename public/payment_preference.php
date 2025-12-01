<?php
// Endpoint público para criar preferência de pagamento no Mercado Pago
header('Content-Type: application/json; charset=utf-8');

// Função para carregar o .env
function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, '"\'');
        
        if (!getenv($key)) {
            putenv("$key=$value");
        }
    }
    return true;
}

try {
    // 1. Carregar variáveis de ambiente
    $envPath = dirname(__DIR__) . '/.env';
    loadEnv($envPath);
    
    // 2. Verificar e carregar autoload do Composer
    $autoloadPath = dirname(__DIR__) . '/vendor/autoload.php';
    if (!file_exists($autoloadPath)) {
        throw new Exception('Vendor autoload não encontrado. Execute: php composer.phar install');
    }
    require_once $autoloadPath;
    
    // 3. Verificar access token do Mercado Pago
    $accessToken = getenv('MERCADO_PAGO_ACCESS_TOKEN');
    if (!$accessToken) {
        throw new Exception('MERCADO_PAGO_ACCESS_TOKEN não configurado no .env');
    }
    
    // 4. Configurar SDK do Mercado Pago
    use MercadoPago\MercadoPagoConfig;
    use MercadoPago\Client\Preference\PreferenceClient;
    
    MercadoPagoConfig::setAccessToken($accessToken);
    
    // 5. Receber dados do POST
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        throw new Exception('Dados inválidos enviados na requisição');
    }
    
    $title = $data['title'] ?? 'Produto';
    $quantity = $data['quantity'] ?? 1;
    $unit_price = $data['unit_price'] ?? 10.00;
    $id_pedido = $data['id_pedido'] ?? null;
    
    // 6. Criar preferência de pagamento
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
    
    // 7. Retornar resposta de sucesso
    echo json_encode([
        'id' => $preference->id,
        'init_point' => $preference->init_point
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>
