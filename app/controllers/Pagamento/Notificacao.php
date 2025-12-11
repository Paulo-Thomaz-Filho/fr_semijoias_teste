<?php
// Endpoint para receber notificações de pagamento do Mercado Pago
http_response_code(200);

// Carregar variáveis de ambiente
$envPath = __DIR__ . '/../../../.env';
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

$body = file_get_contents('php://input');
$timestamp = date('Y-m-d H:i:s');
$logPath = __DIR__ . '/notificacao_log.txt';
file_put_contents($logPath, "[$timestamp] Body: $body\n", FILE_APPEND);

// Validação de assinatura HMAC do Mercado Pago
$xSignature = $_SERVER['HTTP_X_SIGNATURE'] ?? null;
$xRequestId = $_SERVER['HTTP_X_REQUEST_ID'] ?? null;

// Extrair data.id do body JSON (não da query string)
$dataID = '';
if (!empty($body)) {
    $jsonData = json_decode($body, true);
    if ($jsonData && isset($jsonData['data']['id'])) {
        $dataID = $jsonData['data']['id'];
    }
}

file_put_contents($logPath, "[$timestamp] data.id: $dataID | X-Signature: $xSignature | X-Request-Id: $xRequestId\n", FILE_APPEND);

$parts = explode(',', (string)$xSignature);
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

// Pegar secret do .env
$secret = getenv('MERCADO_PAGO_WEBHOOK_SECRET');
if (!$secret) {
    file_put_contents($logPath, "[$timestamp] ERRO: MERCADO_PAGO_WEBHOOK_SECRET não configurado\n", FILE_APPEND);
    exit;
}
$manifest = "id:$dataID;request-id:$xRequestId;ts:$ts;";
$sha = hash_hmac('sha256', $manifest, $secret);

file_put_contents($logPath, "[$timestamp] Manifest: $manifest\n", FILE_APPEND);
file_put_contents($logPath, "[$timestamp] SHA calculado: $sha | Hash recebido: $hash\n", FILE_APPEND);

if ($sha === $hash) {
	file_put_contents($logPath, "[$timestamp] ✅ HMAC OK - Processando pagamento\n", FILE_APPEND);
	// Notificação válida
	try {
		require_once __DIR__ . '/../../core/utils/WebhookHandler.php';
		\app\core\utils\WebhookHandler::atualizarPedidoPorPagamento($dataID);
		file_put_contents($logPath, "[$timestamp] ✅ Pedido atualizado para pagamento $dataID\n", FILE_APPEND);
	} catch (\Throwable $e) {
		file_put_contents($logPath, "[$timestamp] ❌ Erro ao atualizar pedido: " . $e->getMessage() . "\n", FILE_APPEND);
		file_put_contents($logPath, "[$timestamp] Stack trace: " . $e->getTraceAsString() . "\n", FILE_APPEND);
	}
} else {
	file_put_contents($logPath, "[$timestamp] ❌ HMAC FALHOU - Notificação inválida\n", FILE_APPEND);
	// Notificação inválida (possível tentativa de fraude)
}

file_put_contents($logPath, "[$timestamp] ========================================\n\n", FILE_APPEND);

// Pronto! O Mercado Pago espera só o 200 OK.
?>
