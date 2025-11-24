<?php

require_once __DIR__ . '/../../../vendor/autoload.php';
// SDK do Mercado Pago
use MercadoPago\MercadoPagoConfig;
// Adicione credenciais a partir da variável de ambiente
$accessToken = getenv('MERCADO_PAGO_ACCESS_TOKEN');
if (!$accessToken) {
	throw new Exception('Access token do Mercado Pago não definido. Configure a variável de ambiente MERCADO_PAGO_ACCESS_TOKEN.');
}
MercadoPagoConfig::setAccessToken($accessToken);
?>
