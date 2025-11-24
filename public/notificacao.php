<?php
// Endpoint para receber notificações de pagamento do Mercado Pago
// Salve este arquivo como public/notificacao.php

// Sempre responda rapidamente com 200 OK
http_response_code(200);


$body = file_get_contents('php://input');
file_put_contents(__DIR__ . '/notificacao_log.txt', $body . PHP_EOL, FILE_APPEND);

// Validação de assinatura HMAC do Mercado Pago
$xSignature = $_SERVER['HTTP_X_SIGNATURE'] ?? null;
$xRequestId = $_SERVER['HTTP_X_REQUEST_ID'] ?? null;
$queryParams = $_GET;
$dataID = isset($queryParams['data.id']) ? $queryParams['data.id'] : '';
$parts = explode(',', $xSignature);
$ts = null;
$hash = null;
foreach ($parts as $part) {
	$keyValue = explode('=', $part, 2);
	if (count($keyValue) == 2) {
		$key = trim($keyValue[0]);
		$value = trim($keyValue[1]);
		if ($key === "ts") {
			$ts = $value;
		} elseif ($key === "v1") {
			$hash = $value;
		}
	}
}
// Coloque sua chave secreta do Mercado Pago abaixo:
$secret = "SUA_CHAVE_SECRETA_AQUI";
$manifest = "id:$dataID;request-id:$xRequestId;ts:$ts;";
$sha = hash_hmac('sha256', $manifest, $secret);
if ($sha === $hash) {
	file_put_contents(__DIR__ . '/notificacao_log.txt', "HMAC OK\n", FILE_APPEND);
	// Notificação válida
} else {
	file_put_contents(__DIR__ . '/notificacao_log.txt', "HMAC FALHOU\n", FILE_APPEND);
	// Notificação inválida
}

// Pronto! O Mercado Pago espera só o 200 OK.
?>
