<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

// Carregar .env se necessário
$envPath = __DIR__ . '/../../../.env';
if (file_exists($envPath) && !getenv('MERCADO_PAGO_ACCESS_TOKEN')) {
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
}

// SDK do Mercado Pago
use MercadoPago\MercadoPagoConfig;

// Adicione credenciais a partir da variável de ambiente
$accessToken = getenv('MERCADO_PAGO_ACCESS_TOKEN');
if (!$accessToken) {
	throw new Exception('Access token do Mercado Pago não definido. Configure a variável de ambiente MERCADO_PAGO_ACCESS_TOKEN no arquivo .env');
}
MercadoPagoConfig::setAccessToken($accessToken);
?>
