<?php
// test_simple.php - Teste extremamente simples
error_reporting(E_ALL);
ini_set('display_errors', 1);

$logFile = dirname(__DIR__) . '/test_simple.log';

// Teste 1: Pode escrever arquivo?
file_put_contents($logFile, "[TEST 1] Script iniciado\n");

// Teste 2: Autoload existe?
$autoloadPath = dirname(__DIR__) . '/vendor/autoload.php';
file_put_contents($logFile, "[TEST 2] Autoload path: $autoloadPath\n", FILE_APPEND);
file_put_contents($logFile, "[TEST 2] Autoload existe: " . (file_exists($autoloadPath) ? "SIM" : "NAO") . "\n", FILE_APPEND);

try {
    // Teste 3: Carregar autoload
    if (file_exists($autoloadPath)) {
        require_once $autoloadPath;
        file_put_contents($logFile, "[TEST 3] Autoload carregado com sucesso\n", FILE_APPEND);
    } else {
        throw new Exception("Autoload nao encontrado");
    }
    
    // Teste 4: .env existe?
    $envPath = dirname(__DIR__) . '/.env';
    file_put_contents($logFile, "[TEST 4] .env path: $envPath\n", FILE_APPEND);
    file_put_contents($logFile, "[TEST 4] .env existe: " . (file_exists($envPath) ? "SIM" : "NAO") . "\n", FILE_APPEND);
    
    // Teste 5: Ler .env
    if (file_exists($envPath)) {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) continue;
            if (strpos($line, '=') === false) continue;
            
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, '"\'');
            putenv("$key=$value");
        }
        file_put_contents($logFile, "[TEST 5] .env processado\n", FILE_APPEND);
    }
    
    // Teste 6: Token configurado?
    $token = getenv('MERCADO_PAGO_ACCESS_TOKEN');
    file_put_contents($logFile, "[TEST 6] Token existe: " . ($token ? "SIM" : "NAO") . "\n", FILE_APPEND);
    if ($token) {
        file_put_contents($logFile, "[TEST 6] Token (20 chars): " . substr($token, 0, 20) . "...\n", FILE_APPEND);
    }
    
    // Teste 7: Classes do Mercado Pago existem?
    $mpConfig = class_exists('MercadoPago\\MercadoPagoConfig');
    $mpClient = class_exists('MercadoPago\\Client\\Preference\\PreferenceClient');
    file_put_contents($logFile, "[TEST 7] MercadoPagoConfig existe: " . ($mpConfig ? "SIM" : "NAO") . "\n", FILE_APPEND);
    file_put_contents($logFile, "[TEST 7] PreferenceClient existe: " . ($mpClient ? "SIM" : "NAO") . "\n", FILE_APPEND);
    
    // Teste 8: Configurar Mercado Pago
    if ($mpConfig && $token) {
        use MercadoPago\MercadoPagoConfig;
        MercadoPagoConfig::setAccessToken($token);
        file_put_contents($logFile, "[TEST 8] MercadoPago configurado\n", FILE_APPEND);
    }
    
    // Teste 9: Criar cliente
    if ($mpClient) {
        use MercadoPago\Client\Preference\PreferenceClient;
        $client = new PreferenceClient();
        file_put_contents($logFile, "[TEST 9] Cliente criado\n", FILE_APPEND);
    }
    
    file_put_contents($logFile, "[SUCCESS] Todos os testes passaram!\n", FILE_APPEND);
    
    echo "<!DOCTYPE html><html><body>";
    echo "<h1>Testes Concluidos!</h1>";
    echo "<p>Verifique o arquivo: <code>test_simple.log</code></p>";
    echo "<p><a href='view_simple_log.php'>Ver Log</a></p>";
    echo "</body></html>";
    
} catch (Exception $e) {
    file_put_contents($logFile, "[ERROR] " . $e->getMessage() . "\n", FILE_APPEND);
    file_put_contents($logFile, "[ERROR] Arquivo: " . $e->getFile() . "\n", FILE_APPEND);
    file_put_contents($logFile, "[ERROR] Linha: " . $e->getLine() . "\n", FILE_APPEND);
    
    echo "<!DOCTYPE html><html><body>";
    echo "<h1>ERRO!</h1>";
    echo "<p>Mensagem: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Arquivo: " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p>Linha: " . $e->getLine() . "</p>";
    echo "<p><a href='view_simple_log.php'>Ver Log Completo</a></p>";
    echo "</body></html>";
}
