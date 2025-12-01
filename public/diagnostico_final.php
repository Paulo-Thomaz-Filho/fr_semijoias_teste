<?php
/**
 * DIAGNÓSTICO COMPLETO - Suba este arquivo UMA VEZ e veja o erro
 * Acesse: https://frsemijoias.ifhost.gru.br/diagnostico_final.php
 */

// Desabilitar output buffering do servidor
while (ob_get_level()) ob_end_clean();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Diagnóstico Final</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .box { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        pre { background: #eee; padding: 10px; overflow-x: auto; font-size: 12px; }
        h2 { margin-top: 0; border-bottom: 2px solid #333; padding-bottom: 5px; }
        button { background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin: 5px; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>🔍 Diagnóstico Final - Mercado Pago</h1>

    <?php
    // Teste 1: Autoload
    echo '<div class="box"><h2>1. Autoload do Composer</h2>';
    $autoloadPath = __DIR__ . '/../vendor/autoload.php';
    echo "<p>Caminho: <code>$autoloadPath</code></p>";
    
    if (file_exists($autoloadPath)) {
        echo '<p class="success">✓ Arquivo existe</p>';
        try {
            require_once $autoloadPath;
            echo '<p class="success">✓ Carregado com sucesso</p>';
        } catch (Exception $e) {
            echo '<p class="error">✗ Erro ao carregar: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
    } else {
        echo '<p class="error">✗ Arquivo não encontrado</p>';
    }
    echo '</div>';

    // Teste 2: .env
    echo '<div class="box"><h2>2. Arquivo .env</h2>';
    $envPath = __DIR__ . '/../.env';
    echo "<p>Caminho: <code>$envPath</code></p>";
    
    if (file_exists($envPath)) {
        echo '<p class="success">✓ Arquivo existe</p>';
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || $line[0] === '#') continue;
            if (strpos($line, '=') === false) continue;
            
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value, '"\''));
        }
        echo '<p class="success">✓ Processado</p>';
        
        $token = getenv('MERCADO_PAGO_ACCESS_TOKEN');
        if ($token) {
            echo '<p class="success">✓ Token encontrado: ' . substr($token, 0, 20) . '...</p>';
        } else {
            echo '<p class="error">✗ Token não encontrado</p>';
        }
    } else {
        echo '<p class="error">✗ Arquivo não encontrado</p>';
    }
    echo '</div>';

    // Teste 3: Classes do Mercado Pago
    echo '<div class="box"><h2>3. SDK do Mercado Pago</h2>';
    if (class_exists('MercadoPago\\MercadoPagoConfig')) {
        echo '<p class="success">✓ MercadoPagoConfig existe</p>';
    } else {
        echo '<p class="error">✗ MercadoPagoConfig não encontrada</p>';
    }
    
    if (class_exists('MercadoPago\\Client\\Preference\\PreferenceClient')) {
        echo '<p class="success">✓ PreferenceClient existe</p>';
    } else {
        echo '<p class="error">✗ PreferenceClient não encontrada</p>';
    }
    echo '</div>';

    // Teste 4: Criar preferência REAL
    echo '<div class="box"><h2>4. Teste REAL de Criação de Preferência</h2>';
    echo '<button onclick="testarReal()">▶️ EXECUTAR TESTE REAL</button>';
    echo '<div id="resultado"></div>';
    echo '</div>';
    ?>

    <script>
    function testarReal() {
        const div = document.getElementById('resultado');
        div.innerHTML = '<p>⏳ Testando...</p>';
        
        // Fazer requisição AJAX inline (sem usar outro arquivo)
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'diagnostico_final.php?action=test', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        
        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        div.innerHTML = '<p class="success">✓ SUCESSO! Preferência criada!</p>' +
                                      '<pre>' + JSON.stringify(response.data, null, 2) + '</pre>';
                    } else {
                        div.innerHTML = '<p class="error">✗ ERRO:</p>' +
                                      '<pre>' + JSON.stringify(response, null, 2) + '</pre>';
                    }
                } catch (e) {
                    div.innerHTML = '<p class="error">✗ Resposta inválida:</p><pre>' + xhr.responseText + '</pre>';
                }
            } else {
                div.innerHTML = '<p class="error">✗ Erro HTTP ' + xhr.status + '</p><pre>' + xhr.responseText + '</pre>';
            }
        };
        
        xhr.onerror = function() {
            div.innerHTML = '<p class="error">✗ Erro de rede</p>';
        };
        
        xhr.send(JSON.stringify({
            title: 'Produto Teste',
            quantity: 1,
            unit_price: 10.00
        }));
    }
    </script>

</body>
</html>

<?php
// Se for uma requisição de teste
if (isset($_GET['action']) && $_GET['action'] === 'test') {
    // Limpar qualquer output anterior
    while (ob_get_level()) ob_end_clean();
    
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        // Carregar tudo novamente
        require_once __DIR__ . '/../vendor/autoload.php';
        
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
        
        $token = getenv('MERCADO_PAGO_ACCESS_TOKEN');
        if (!$token) {
            throw new Exception('Token não configurado');
        }
        
        MercadoPago\MercadoPagoConfig::setAccessToken($token);
        
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
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
            "auto_return" => "approved"
        ]);
        
        echo json_encode([
            'success' => true,
            'data' => [
                'id' => $preference->id,
                'init_point' => $preference->init_point
            ]
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => explode("\n", $e->getTraceAsString())
        ]);
    }
    exit;
}
?>
