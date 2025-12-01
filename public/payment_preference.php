<?php
// Endpoint público para criar preferência de pagamento no Mercado Pago

// Iniciar buffer de saída para capturar qualquer output indesejado
ob_start();

// Função para registrar logs de debug
function logDebug($message, $data = null) {
    $logFile = dirname(__DIR__) . '/payment_debug.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message";
    if ($data !== null) {
        $logMessage .= "\n" . print_r($data, true);
    }
    $logMessage .= "\n---\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Função para carregar o .env
function loadEnv($path) {
    if (!file_exists($path)) {
        logDebug("ERROR: .env não encontrado em: $path");
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
    logDebug(".env carregado com sucesso");
    return true;
}

// Limpar log anterior
$logFile = dirname(__DIR__) . '/payment_debug.log';
if (file_exists($logFile)) {
    unlink($logFile);
}

logDebug("=== INÍCIO DA REQUISIÇÃO ===");
logDebug("Method: " . $_SERVER['REQUEST_METHOD']);
logDebug("URI: " . $_SERVER['REQUEST_URI']);

try {
    // 1. Carregar variáveis de ambiente
    logDebug("Passo 1: Carregando .env");
    $envPath = dirname(__DIR__) . '/.env';
    loadEnv($envPath);
    
    // 2. Verificar e carregar autoload do Composer
    logDebug("Passo 2: Carregando autoload");
    $autoloadPath = dirname(__DIR__) . '/vendor/autoload.php';
    if (!file_exists($autoloadPath)) {
        throw new Exception('Vendor autoload não encontrado. Execute: php composer.phar install');
    }
    require_once $autoloadPath;
    logDebug("Autoload carregado com sucesso");
    
    // 3. Verificar access token do Mercado Pago
    logDebug("Passo 3: Verificando access token");
    $accessToken = getenv('MERCADO_PAGO_ACCESS_TOKEN');
    if (!$accessToken) {
        throw new Exception('MERCADO_PAGO_ACCESS_TOKEN não configurado no .env');
    }
    logDebug("Access token encontrado: " . substr($accessToken, 0, 20) . "...");
    
    // 4. Configurar SDK do Mercado Pago
    logDebug("Passo 4: Configurando SDK do Mercado Pago");
    use MercadoPago\MercadoPagoConfig;
    use MercadoPago\Client\Preference\PreferenceClient;
    
    MercadoPagoConfig::setAccessToken($accessToken);
    logDebug("SDK configurado com sucesso");
    
    // 5. Receber dados do POST
    logDebug("Passo 5: Recebendo dados do POST");
    $input = file_get_contents('php://input');
    logDebug("Input recebido: $input");
    $data = json_decode($input, true);
    
    if (!$data) {
        throw new Exception('Dados inválidos enviados na requisição');
    }
    
    $title = $data['title'] ?? 'Produto';
    $quantity = $data['quantity'] ?? 1;
    $unit_price = $data['unit_price'] ?? 10.00;
    $id_pedido = $data['id_pedido'] ?? null;
    
    logDebug("Dados processados:", [
        'title' => $title,
        'quantity' => $quantity,
        'unit_price' => $unit_price,
        'id_pedido' => $id_pedido
    ]);
    
    // 6. Criar preferência de pagamento
    logDebug("Passo 6: Criando preferência de pagamento");
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
    
    logDebug("Preferência criada com sucesso! ID: " . $preference->id);
    
    // 7. Limpar buffer e retornar resposta de sucesso
    $buffer = ob_get_clean();
    if (!empty($buffer)) {
        logDebug("AVISO: Buffer continha dados: " . substr($buffer, 0, 200));
    }
    
    header('Content-Type: application/json; charset=utf-8');
    $response = json_encode([
        'id' => $preference->id,
        'init_point' => $preference->init_point
    ]);
    logDebug("Resposta enviada: $response");
    echo $response;
    
} catch (Exception $e) {
    logDebug("ERRO CAPTURADO!", [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
    
    $buffer = ob_get_clean();
    if (!empty($buffer)) {
        logDebug("Buffer no erro: " . substr($buffer, 0, 200));
    }
    
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
