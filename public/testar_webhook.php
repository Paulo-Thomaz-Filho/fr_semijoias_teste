<?php
// Teste manual do webhook - simula uma notificação do Mercado Pago
header('Content-Type: text/plain; charset=utf-8');

echo "=== TESTE DE WEBHOOK ===\n\n";

// Carregar .env
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

// Verificar se a secret está carregada
$secret = getenv('MERCADO_PAGO_WEBHOOK_SECRET');
$accessToken = getenv('MERCADO_PAGO_ACCESS_TOKEN');

echo "1. Verificando variáveis de ambiente:\n";
echo "   - ACCESS_TOKEN: " . ($accessToken ? "✅ Configurado" : "❌ NÃO configurado") . "\n";
echo "   - WEBHOOK_SECRET: " . ($secret ? "✅ Configurado" : "❌ NÃO configurado") . "\n\n";

if ($secret) {
    echo "2. Secret encontrada:\n";
    echo "   " . substr($secret, 0, 20) . "...\n\n";
}

// Testar escrita no log
$timestamp = date('Y-m-d H:i:s');
$logPath = __DIR__ . '/notificacao_log.txt';
$testMessage = "[$timestamp] ✅ TESTE: Sistema funcionando corretamente!\n";

if (file_put_contents($logPath, $testMessage, FILE_APPEND)) {
    echo "3. Teste de escrita no log:\n";
    echo "   ✅ Log gravado com sucesso\n\n";
} else {
    echo "3. Teste de escrita no log:\n";
    echo "   ❌ ERRO ao gravar log (permissões?)\n\n";
}

echo "4. Conteúdo atual do .env:\n";
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    // Ocultar senhas
    $envContent = preg_replace('/(PASSWORD|PASS|SECRET|TOKEN)=(.+)/i', '$1=***', $envContent);
    echo "   " . str_replace("\n", "\n   ", trim($envContent)) . "\n\n";
} else {
    echo "   ❌ Arquivo .env não encontrado!\n\n";
}

echo "=== FIM DO TESTE ===\n";
