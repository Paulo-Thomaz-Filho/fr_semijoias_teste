<?php
// Endpoint público para receber notificações do Mercado Pago
// Exemplo de URL: https://seudominio.com/api/pagamento/notificacao

// Caminho absoluto até o controller real
$rootPath = dirname(__DIR__, 3); // Sobe até a raiz do projeto
require_once $rootPath . '/app/controllers/Pagamento/Notificacao.php';
