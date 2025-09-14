<?php

// --- 1. CONFIGURAÇÃO INICIAL DO AMBIENTE ---

// Habilita a exibição de todos os erros (essencial durante o desenvolvimento)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define o fuso horário para evitar erros de data/hora
date_default_timezone_set('America/Sao_Paulo');


// --- 2. DEFINIÇÃO DE CAMINHOS E VARIÁVEIS GLOBAIS ---

// Define a variável global $rootPath que aponta para a raiz do projeto.
// `__DIR__` é o diretório atual (`/public`), então `dirname(__DIR__)` sobe um nível.
global $rootPath;
$rootPath = dirname(__DIR__);


// --- 3. AUTOLOAD DE CLASSES (MUITO IMPORTANTE) ---

// A forma PADRÃO e RECOMENDADA é usar o autoloader do Composer.
// Ele carrega automaticamente as classes quando elas são necessárias.
$composerAutoload = $rootPath . '/vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
} else {
    // Se você não usa Composer, a aplicação não funcionará corretamente.
    // O roteador precisa disso para encontrar classes como `App\Models\UsuarioDAO`.
    // Para resolver, navegue até a raiz do seu projeto no terminal e execute `composer install`.
    die("<h1>Autoloader do Composer não encontrado.</h1><p>Por favor, execute o comando <strong><code>composer install</code></strong> na raiz do seu projeto para gerar o arquivo <code>vendor/autoload.php</code>.</p>");
}


// --- 4. CARREGAMENTO DO ARQUIVO DE CONFIGURAÇÃO ---

// O roteador e a conexão com o banco de dados precisam das configurações.
global $config;
$configFile = $rootPath . '/app/etc/config.php';
if (file_exists($configFile)) {
    // Usamos `require` pois a configuração é essencial para a aplicação.
    $config = require $configFile;
} else {
    die("<h1>Erro Crítico</h1><p>Arquivo de configuração não encontrado em <code>{$configFile}</code>.</p>");
}


// --- 5. INICIALIZAÇÃO E EXECUÇÃO DO ROTEADOR ---

// Importa a classe Router para facilitar a leitura do código.
use core\utils\Router;

try {
    // Captura a rota a partir do parâmetro 'module' na URL.
    // Ex: `site.com/index.php?module=api/produtos/listar`
    // Se nenhum módulo for passado, define um padrão (ex: 'home').
    $module = $_GET['module'] ?? 'home';

    // Instancia o roteador que criamos.
    $router = new Router();

    // Entrega o controle para o roteador, que irá despachar a rota correta.
    $router->dispatch($module);

} catch (Exception $e) {
    // Uma captura de erro genérica para qualquer problema inesperado.
    http_response_code(500);
    // Em produção, seria bom logar o erro em vez de exibi-lo.
    echo json_encode([
        'status' => 'error',
        'message' => 'Ocorreu um erro interno no servidor.',
        'error_details' => $e->getMessage() // Apenas para desenvolvimento
    ]);
}