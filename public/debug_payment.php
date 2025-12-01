<?php
// debug_payment.php - Captura a resposta exata do payment_preference.php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Payment Preference</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; }
        pre { background: #eee; padding: 10px; overflow-x: auto; white-space: pre-wrap; word-wrap: break-word; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h1>🔍 Debug do payment_preference.php</h1>
    
    <div class="box">
        <h2>Teste 1: Requisição POST com dados</h2>
        <button onclick="testarComDados()">Testar com Dados</button>
        <div id="result1"></div>
    </div>

    <div class="box">
        <h2>Teste 2: Acessar diretamente (sem POST)</h2>
        <button onclick="testarSemDados()">Testar sem Dados</button>
        <div id="result2"></div>
    </div>

    <div class="box">
        <h2>Teste 3: Ver resposta RAW (exatamente o que volta)</h2>
        <button onclick="testarRaw()">Ver Resposta RAW</button>
        <div id="result3"></div>
    </div>

    <script>
        function testarComDados() {
            const resultDiv = document.getElementById('result1');
            resultDiv.innerHTML = '<p>Enviando requisição...</p>';
            
            fetch('/payment_preference.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    title: 'Produto Teste Debug',
                    quantity: 1,
                    unit_price: 10.00,
                    id_pedido: 999
                })
            })
            .then(response => {
                console.log('Status:', response.status);
                console.log('Headers:', response.headers);
                return response.text();
            })
            .then(text => {
                console.log('Response:', text);
                
                let html = '<h3>Status da Resposta:</h3>';
                html += '<pre>Texto recebido (length: ' + text.length + ' bytes):\n' + escapeHtml(text) + '</pre>';
                
                // Tentar parsear como JSON
                try {
                    const json = JSON.parse(text);
                    html += '<p class="success">✓ É JSON válido!</p>';
                    html += '<pre>' + JSON.stringify(json, null, 2) + '</pre>';
                    
                    if (json.id) {
                        html += '<p class="success">✓ Preferência criada: ' + json.id + '</p>';
                        html += '<p>Init Point: <a href="' + json.init_point + '" target="_blank">' + json.init_point + '</a></p>';
                    }
                } catch (e) {
                    html += '<p class="error">✗ NÃO é JSON válido!</p>';
                    html += '<p class="error">Erro: ' + e.message + '</p>';
                    
                    // Mostrar bytes iniciais em hex
                    const bytes = text.substring(0, 100);
                    html += '<p>Primeiros caracteres (pode ter espaços/quebras ocultas):</p>';
                    html += '<pre>';
                    for (let i = 0; i < Math.min(bytes.length, 50); i++) {
                        const char = bytes[i];
                        const code = char.charCodeAt(0);
                        html += 'char[' + i + ']: "' + (code < 32 ? '\\x' + code.toString(16) : char) + '" (code: ' + code + ')\n';
                    }
                    html += '</pre>';
                }
                
                resultDiv.innerHTML = html;
            })
            .catch(error => {
                resultDiv.innerHTML = '<p class="error">✗ Erro na requisição: ' + error + '</p>';
            });
        }

        function testarSemDados() {
            const resultDiv = document.getElementById('result2');
            resultDiv.innerHTML = '<p>Acessando sem dados POST...</p>';
            
            fetch('/payment_preference.php', {
                method: 'GET'
            })
            .then(response => response.text())
            .then(text => {
                let html = '<h3>Resposta sem POST:</h3>';
                html += '<pre>' + escapeHtml(text) + '</pre>';
                resultDiv.innerHTML = html;
            })
            .catch(error => {
                resultDiv.innerHTML = '<p class="error">✗ Erro: ' + error + '</p>';
            });
        }

        function testarRaw() {
            const resultDiv = document.getElementById('result3');
            resultDiv.innerHTML = '<p>Fazendo requisição RAW...</p>';
            
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/payment_preference.php', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    let html = '<h3>Resposta RAW Completa:</h3>';
                    html += '<p>Status: ' + xhr.status + ' ' + xhr.statusText + '</p>';
                    html += '<p>Response Headers:</p><pre>' + escapeHtml(xhr.getAllResponseHeaders()) + '</pre>';
                    html += '<p>Response Text (length: ' + xhr.responseText.length + '):</p>';
                    html += '<pre>' + escapeHtml(xhr.responseText) + '</pre>';
                    
                    // Mostrar em hexadecimal os primeiros bytes
                    html += '<p>Primeiros 20 bytes em HEX:</p><pre>';
                    for (let i = 0; i < Math.min(20, xhr.responseText.length); i++) {
                        html += xhr.responseText.charCodeAt(i).toString(16).padStart(2, '0') + ' ';
                    }
                    html += '</pre>';
                    
                    resultDiv.innerHTML = html;
                }
            };
            
            xhr.send(JSON.stringify({
                title: 'Produto Teste',
                quantity: 1,
                unit_price: 10.00,
                id_pedido: 999
            }));
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>
