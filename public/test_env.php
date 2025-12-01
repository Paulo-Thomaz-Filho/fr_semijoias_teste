<?php
// Arquivo de diagnóstico - test_env.php
// Este arquivo mostra todos os detalhes do ambiente no cPanel

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Diagnóstico do Ambiente</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .section { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; }
        h2 { border-bottom: 2px solid #333; padding-bottom: 5px; }
        pre { background: #eee; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 Diagnóstico do Ambiente - cPanel</h1>

    <div class="section">
        <h2>1. Informações do PHP</h2>
        <p>Versão do PHP: <strong><?php echo phpversion(); ?></strong></p>
        <p>Diretório atual: <strong><?php echo __DIR__; ?></strong></p>
        <p>Diretório raiz: <strong><?php echo dirname(__DIR__); ?></strong></p>
    </div>

    <div class="section">
        <h2>2. Verificação de Arquivos Críticos</h2>
        <?php
        $files = [
            'vendor/autoload.php' => dirname(__DIR__) . '/vendor/autoload.php',
            'composer.json' => dirname(__DIR__) . '/composer.json',
            'composer.lock' => dirname(__DIR__) . '/composer.lock',
            'composer.phar' => dirname(__DIR__) . '/composer.phar',
            '.env' => dirname(__DIR__) . '/.env',
        ];
        
        foreach ($files as $name => $path) {
            $exists = file_exists($path);
            $class = $exists ? 'success' : 'error';
            $status = $exists ? '✓ EXISTE' : '✗ NÃO EXISTE';
            echo "<p class='$class'>$name: $status<br><small>$path</small></p>";
        }
        ?>
    </div>

    <div class="section">
        <h2>3. Teste de Carregamento do Autoload</h2>
        <?php
        $autoloadPath = dirname(__DIR__) . '/vendor/autoload.php';
        
        if (file_exists($autoloadPath)) {
            try {
                require_once $autoloadPath;
                echo "<p class='success'>✓ Autoload carregado com sucesso!</p>";
            } catch (Exception $e) {
                echo "<p class='error'>✗ Erro ao carregar autoload:</p>";
                echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
                echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            }
        } else {
            echo "<p class='error'>✗ Arquivo autoload.php não encontrado!</p>";
            echo "<p>Execute no terminal do cPanel: <code>cd " . dirname(__DIR__) . " && php composer.phar install</code></p>";
        }
        ?>
    </div>

    <div class="section">
        <h2>4. Verificação das Classes do Mercado Pago</h2>
        <?php
        $classes = [
            'MercadoPago\MercadoPagoConfig',
            'MercadoPago\Client\Preference\PreferenceClient',
        ];
        
        foreach ($classes as $class) {
            $exists = class_exists($class);
            $status = $exists ? '✓ EXISTE' : '✗ NÃO EXISTE';
            $cssClass = $exists ? 'success' : 'error';
            echo "<p class='$cssClass'>$class: $status</p>";
        }
        ?>
    </div>

    <div class="section">
        <h2>5. Leitura do arquivo .env</h2>
        <?php
        $envPath = dirname(__DIR__) . '/.env';
        
        if (file_exists($envPath)) {
            echo "<p class='success'>✓ Arquivo .env encontrado!</p>";
            
            // Carregar .env
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $envVars = [];
            
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                if (strpos($line, '=') === false) continue;
                
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                $value = trim($value, '"\'');
                
                putenv("$key=$value");
                
                // Ocultar valores sensíveis
                if (strpos($key, 'PASSWORD') !== false || strpos($key, 'TOKEN') !== false || strpos($key, 'PASS') !== false) {
                    $displayValue = substr($value, 0, 10) . '...';
                } else {
                    $displayValue = $value;
                }
                
                $envVars[$key] = $displayValue;
            }
            
            echo "<pre>";
            foreach ($envVars as $key => $value) {
                echo htmlspecialchars("$key = $value") . "\n";
            }
            echo "</pre>";
            
            // Verificar variáveis críticas
            $criticalVars = [
                'MERCADO_PAGO_ACCESS_TOKEN',
                'DB_HOST',
                'DB_NAME',
                'DB_USER',
            ];
            
            echo "<h3>Variáveis Críticas:</h3>";
            foreach ($criticalVars as $var) {
                $value = getenv($var);
                $exists = !empty($value);
                $status = $exists ? '✓ CONFIGURADO' : '✗ NÃO CONFIGURADO';
                $cssClass = $exists ? 'success' : 'error';
                echo "<p class='$cssClass'>$var: $status</p>";
            }
            
        } else {
            echo "<p class='error'>✗ Arquivo .env não encontrado!</p>";
        }
        ?>
    </div>

    <div class="section">
        <h2>6. Teste de Criação de Preferência (SIMULAÇÃO)</h2>
        <?php
        if (class_exists('MercadoPago\MercadoPagoConfig') && getenv('MERCADO_PAGO_ACCESS_TOKEN')) {
            try {
                $accessToken = getenv('MERCADO_PAGO_ACCESS_TOKEN');
                
                MercadoPago\MercadoPagoConfig::setAccessToken($accessToken);
                
                echo "<p class='success'>✓ MercadoPago configurado com sucesso!</p>";
                echo "<p>Token configurado: " . substr($accessToken, 0, 20) . "...</p>";
                
                // Tentar criar um cliente (sem fazer requisição real)
                $client = new MercadoPago\Client\Preference\PreferenceClient();
                echo "<p class='success'>✓ PreferenceClient instanciado com sucesso!</p>";
                
                echo "<p><strong>Tudo pronto para criar preferências de pagamento!</strong></p>";
                
            } catch (Exception $e) {
                echo "<p class='error'>✗ Erro ao configurar Mercado Pago:</p>";
                echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
            }
        } else {
            echo "<p class='error'>✗ Não foi possível testar: classes não carregadas ou token não configurado</p>";
        }
        ?>
    </div>

    <div class="section">
        <h2>7. Teste do payment_preference.php</h2>
        <p>Para testar o endpoint de pagamento, clique no botão abaixo:</p>
        <button onclick="testarPayment()">Testar Payment Preference</button>
        <div id="result" style="margin-top: 10px;"></div>
        
        <script>
        function testarPayment() {
            const resultDiv = document.getElementById('result');
            resultDiv.innerHTML = '<p>Enviando requisição...</p>';
            
            fetch('/payment_preference.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    title: 'Produto Teste',
                    quantity: 1,
                    unit_price: 10.00,
                    id_pedido: 999
                })
            })
            .then(response => {
                console.log('Status:', response.status);
                return response.text();
            })
            .then(text => {
                resultDiv.innerHTML = '<h3>Resposta do Servidor:</h3><pre>' + text + '</pre>';
                try {
                    const json = JSON.parse(text);
                    if (json.id) {
                        resultDiv.innerHTML += '<p class="success">✓ Preferência criada com sucesso!</p>';
                    } else if (json.error) {
                        resultDiv.innerHTML += '<p class="error">✗ Erro: ' + json.error + '</p>';
                    }
                } catch (e) {
                    resultDiv.innerHTML += '<p class="error">✗ Resposta não é JSON válido</p>';
                }
            })
            .catch(error => {
                resultDiv.innerHTML = '<p class="error">✗ Erro na requisição: ' + error + '</p>';
            });
        }
        </script>
    </div>

    <div class="section">
        <h2>8. Estrutura de Diretórios</h2>
        <pre><?php
        $baseDir = dirname(__DIR__);
        echo "Raiz do projeto: $baseDir\n\n";
        
        function listDir($dir, $prefix = '', $maxDepth = 2, $currentDepth = 0) {
            if ($currentDepth >= $maxDepth) return;
            
            $items = @scandir($dir);
            if (!$items) return;
            
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') continue;
                
                $path = $dir . '/' . $item;
                $isDir = is_dir($path);
                
                echo $prefix . ($isDir ? '📁 ' : '📄 ') . $item . "\n";
                
                if ($isDir && !in_array($item, ['vendor', 'node_modules', '.git'])) {
                    listDir($path, $prefix . '  ', $maxDepth, $currentDepth + 1);
                }
            }
        }
        
        listDir($baseDir);
        ?></pre>
    </div>

</body>
</html>
