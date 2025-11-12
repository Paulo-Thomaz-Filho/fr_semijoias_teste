<?php
// public/index.php

// 1. Define o caminho raiz do projeto.
$rootPath = dirname(__DIR__);

// 2. Carrega o autoloader do Composer (PHPMailer e outras dependências)
require_once $rootPath . '/vendor/autoload.php';

// 3. Inclui os arquivos essenciais do framework.
require_once $rootPath . '/app/etc/config.php';
require_once $rootPath . '/app/core/utils/Router.php';
require_once $rootPath . '/app/core/utils/Sanitize.php';

// 3. Pega o "módulo" da URL, que é preenchido pelo .htaccess.
// Se o ?module= não existir (caso da raiz '/'), define 'home' como padrão.
$module = $_GET['module'] ?? 'login/';

// 4. (Opcional, mas recomendado) Remove a barra final da URL, se houver.
if (strlen($module) > 1 && substr($module, -1) === '/') {
    $module = substr($module, 0, -1);
}

// 5. Inicia o roteador e despacha a requisição.
$router = new core\utils\Router();
$router->dispatch($module);
