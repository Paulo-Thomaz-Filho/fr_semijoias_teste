<?php
// view_simple_log.php - Ver o log do test_simple.php
$logFile = dirname(__DIR__) . '/test_simple.log';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Simple Test Log</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        pre { background: white; padding: 15px; border-radius: 5px; }
        button { padding: 10px; margin: 5px; }
    </style>
</head>
<body>
    <h1>📄 Simple Test Log</h1>
    
    <button onclick="location.reload()">🔄 Recarregar</button>
    <button onclick="window.location='test_simple.php'">🧪 Executar Teste Novamente</button>
    
    <h2>Conteúdo:</h2>
    <?php
    if (file_exists($logFile)) {
        echo "<pre>" . htmlspecialchars(file_get_contents($logFile)) . "</pre>";
    } else {
        echo "<p style='color: red;'>❌ Log não encontrado em: $logFile</p>";
        echo "<p>Execute o teste primeiro: <a href='test_simple.php'>test_simple.php</a></p>";
    }
    ?>
</body>
</html>
