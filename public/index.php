<?php
// --- 1. CONFIGURAÇÃO INICIAL DO AMBIENTE ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Sao_Paulo');

// --- 2. DEFINIÇÃO DE CAMINHOS E AUTOLOADER ---
global $rootPath;
$rootPath = dirname(__DIR__);

// Carrega o autoloader (Composer ou manual)
$composerAutoload = $rootPath . '/vendor/autoload.php';
$manualAutoload = $rootPath . '/autoloader.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
} elseif (file_exists($manualAutoload)) {
    require_once $manualAutoload;
} else {
    die("<h1>Erro Crítico: Autoloader não encontrado.</h1><p>Execute 'composer install' ou crie o arquivo 'autoloader.php' na raiz do projeto.</p>");
}

// --- 3. CARREGAMENTO DA CONFIGURAÇÃO ---
global $config;
$configFile = $rootPath . '/app/etc/config.php';
if (file_exists($configFile)) {
    require $configFile; // O config.php irá popular a $_SESSION
} else {
    die("<h1>Erro Crítico: Arquivo de configuração não encontrado.</h1>");
}

// --- 4. INICIALIZAÇÃO E EXECUÇÃO DO ROTEADOR ---
use App\Core\Utils\Router; // Namespace corrigido

try {
    // Captura a rota a partir do .htaccess
    $module = $_GET['module'] ?? 'login'; // A rota padrão para quem acessa a raiz é 'login'

    $router = new Router();
    $router->dispatch($module);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erro interno no servidor.', 'error_details' => $e->getMessage()]);
}