<?php
// view_log.php - Visualizar o log de debug do payment_preference.php
header('Content-Type: text/html; charset=utf-8');

$logFile = dirname(__DIR__) . '/payment_debug.log';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Debug Log</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 20px; border-radius: 5px; }
        pre { background: #eee; padding: 15px; overflow-x: auto; white-space: pre-wrap; word-wrap: break-word; }
        .actions { margin-bottom: 20px; }
        button { padding: 10px 20px; font-size: 14px; cursor: pointer; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Payment Debug Log</h1>
        
        <div class="actions">
            <button onclick="location.reload()">🔄 Recarregar</button>
            <button onclick="clearLog()">🗑️ Limpar Log</button>
            <button onclick="testPayment()">🧪 Testar Payment</button>
        </div>
        
        <h2>Status do Arquivo de Log:</h2>
        <?php
        if (file_exists($logFile)) {
            $size = filesize($logFile);
            $modified = date('Y-m-d H:i:s', filemtime($logFile));
            echo "<p class='success'>✓ Log existe</p>";
            echo "<p>Tamanho: $size bytes</p>";
            echo "<p>Última modificação: $modified</p>";
        } else {
            echo "<p class='error'>✗ Arquivo de log não encontrado</p>";
            echo "<p>O arquivo será criado automaticamente na primeira requisição.</p>";
            echo "<p>Caminho esperado: <code>$logFile</code></p>";
        }
        ?>
        
        <h2>Conteúdo do Log:</h2>
        <?php
        if (file_exists($logFile)) {
            $content = file_get_contents($logFile);
            if (empty($content)) {
                echo "<p>Log está vazio.</p>";
            } else {
                echo "<pre>" . htmlspecialchars($content) . "</pre>";
            }
        } else {
            echo "<pre>Nenhum log disponível ainda.</pre>";
        }
        ?>
    </div>
    
    <script>
        function clearLog() {
            if (confirm('Tem certeza que deseja limpar o log?')) {
                fetch('view_log.php?action=clear', {
                    method: 'POST'
                })
                .then(() => location.reload());
            }
        }
        
        function testPayment() {
            alert('Enviando requisição para payment_preference.php...');
            
            fetch('/payment_preference.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    title: 'Produto Teste via View Log',
                    quantity: 1,
                    unit_price: 10.00,
                    id_pedido: 999
                })
            })
            .then(response => response.text())
            .then(text => {
                alert('Resposta recebida! Recarregando para ver o log...');
                location.reload();
            })
            .catch(error => {
                alert('Erro: ' + error);
                location.reload();
            });
        }
    </script>
    
    <?php
    // Ação de limpar o log
    if (isset($_GET['action']) && $_GET['action'] === 'clear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if (file_exists($logFile)) {
            unlink($logFile);
        }
        exit('OK');
    }
    ?>
</body>
</html>
