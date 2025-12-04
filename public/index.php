<?php

// 1. Define o caminho raiz do projeto.
$rootPath = dirname(__DIR__);

// 2. Carrega o autoloader do Composer e Arquivos Essenciais
require_once $rootPath . '/app/etc/config.php';
require_once $rootPath . '/app/core/utils/Router.php';
require_once $rootPath . '/app/core/utils/Sanitize.php';

// 3. Pega o "módulo" da URL.
$module = $_GET['module'] ?? 'inicio';

// ---------------------------------------------------
// ZONA DE LIMPEZA DE URL
// ---------------------------------------------------

// 1. Remove espaços em branco
$module = trim($module);

// 2. Remove barras do início e do fim
$module = trim($module, '/');

// 3. Remove o prefixo "public/" se ele existir na string
// Isso transforma "public/api/usuario/login" em "api/usuario/login"
if (strpos($module, 'public/') === 0) {
    $module = substr($module, 7); // Remove os 7 caracteres de "public/"
}

// Se após limpar ficar vazio, define o padrão
if ($module === '') {
    $module = 'inicio'; 
}

// 4. Inicia o roteador...
$router = new core\utils\Router();
$router->dispatch($module);