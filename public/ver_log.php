<?php
// Visualizar log de notificações
header('Content-Type: text/plain; charset=utf-8');

$logPath = __DIR__ . '/notificacao_log.txt';

if (file_exists($logPath)) {
    echo file_get_contents($logPath);
} else {
    echo "Arquivo de log não encontrado.";
}
