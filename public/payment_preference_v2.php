<?php
// payment_preference_v2.php - Versão simplificada e robusta
header('Content-Type: application/json; charset=utf-8');

// Capturar TODOS os erros
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

try {
    // 1. Carregar autoload
    $autoloadPath = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoloadPath)) {
        throw new Exception('Vendor autoload nao encontrado');
    }
    require_once $autoloadPath;
    
    // 2. Carregar .env
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
    
    // 3. Verificar token
    $token = getenv('MERCADO_PAGO_ACCESS_TOKEN');
    if (!$token) {
        throw new Exception('MERCADO_PAGO_ACCESS_TOKEN nao configurado');
    }
    
    // 4. Configurar SDK
    MercadoPago\MercadoPagoConfig::setAccessToken($token);
    
    // 5. Receber dados
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        throw new Exception('Dados invalidos');
    }
    
    // 6. Criar preferência
    $client = new MercadoPago\Client\Preference\PreferenceClient();
    $preference = $client->create([
        "items" => [[
            "title" => $data['title'] ?? 'Produto',
            "quantity" => (int)($data['quantity'] ?? 1),
            "unit_price" => (float)($data['unit_price'] ?? 10.00)
        ]],
        "back_urls" => [
            "success" => "https://frsemijoias.ifhost.gru.br/sucesso",
            "failure" => "https://frsemijoias.ifhost.gru.br/erro",
            "pending" => "https://frsemijoias.ifhost.gru.br/pendente"
        ],
        "auto_return" => "approved",
        "external_reference" => $data['id_pedido'] ?? null
    ]);
    
    // 7. Retornar sucesso
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
