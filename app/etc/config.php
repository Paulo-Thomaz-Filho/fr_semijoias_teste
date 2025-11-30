<?php

/**
 * config.php
 * @package ZaitTinyFrameworkPHP
 * @author  Msc Cleber Silva de Oliveira, Yossef Zait
 * @version 0.0.0.1
 * @see     https://cleberoliveira.info
 * 
 * Este é o arquivo de configuração principal para a aplicação.
 * Ele define uma série de variáveis de configuração que serão usadas em outras partes do aplicativo.
 *
 * As seguintes operações são realizadas, em ordem:
 *
 * 1. O Index define $rootPath como o diretório pai do diretório atual, que será usado posteriormente para resolver caminhos na aplicação.
 * 2. Define a variável $showErrors que controla se erros de PHP serão exibidos ou não.
 * 3. Se $showErrors for true, ajusta as configurações de exibição de erro do PHP.
 * 4. Define uma array associativa $config com as configurações da aplicação, incluindo:
 *     - Configuração do banco de dados
 *     - Configuração do email
 *     - Caminhos de alias para vários diretórios importantes na aplicação
 * 5. Armazena $config na variável de sessão $_SESSION['config'] para uso em outras partes da aplicação.
 *
 * Requisitos:
 * - PHP 7.4 ou superior.
 * - A extensão json do PHP deve estar habilitada.
 * - A sessão PHP deve estar iniciada antes de utilizar este arquivo.
 */



if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

global $rootPath; // é necessário global pra usar $rootPath de '/index.php'
global $config;   // é necessário global pra usar $config de '/index.php'

// Arquivo de configurações de rotas
$routersFile = $rootPath . '/app/etc/routes.json';
$uriArray    = [];
$dominio     = $_SERVER['HTTP_HOST'];
$uriArray    = array_values( 
                    array_filter( 
                        explode("/", explode("?", $_SERVER['REQUEST_URI'])[0]), 
                        function ($item) { return trim($item) !== "";} 
                    )
               );

$ultimaPastaDir = $rootPath ? basename($rootPath) : '';
$primeiraPastaUri = isset($uriArray[0]) ? $uriArray[0] : '';



$rootPathUrl = $dominio;
if ($ultimaPastaDir === $primeiraPastaUri) {
    $rootPathUrl = ((!empty($_SERVER['HTTPS']))?'https://':'http://') . $dominio."/" . $ultimaPastaDir . "/";
    array_shift($uriArray);
}
   
$showErrors = true;

if ($showErrors){
    ini_set('display_errors',1);
    ini_set('display_startup_erros',1);
    error_reporting(E_ALL);
}

$envPath = $rootPath . '/.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        
        $value = trim($value, '"\'');
        
        putenv("$key=$value");
    }
}

$config['database']['host']   = getenv('DB_HOST') ?: 'localhost';
$config['database']['schema'] = getenv('DB_NAME') ?: 'fr_semijoias';
$config['database']['user']   = getenv('DB_USER') ?: 'root';
$config['database']['pass']   = getenv('DB_PASS') ?: '';
$config['database']['port']   = getenv('DB_PORT') ?: '3306';

$config['email']['host']	    = 'smtp.mydomain.com';
$config['email']['port']	    =  587;
$config['email']['username']	= 'my_username';
$config['email']['password']	= 'my_password';
    
$config['path']['routes']	  = $routersFile;
$config['path']['root']	      = $rootPath;
$config['path']['css']	      = $rootPath .'public/assets/css/';
$config['path']['js']	      = $rootPath .'public/assets/js/';
$config['path']['font']	      = $rootPath .'public/assets/fonts/';
$config['path']['images']     = $rootPath .'public/assets/images/';
$config['path']['uploads']    = $rootPath .'/app/uploads/';
$config['path']['pdoErrors']  = $rootPath .'/app/etc/pdoErrors.php';
$config['path']['controllers']= $rootPath .'app/controllers';
$config['path']['views']	  = $rootPath .'app/views';
$config['path']['models']     = $rootPath .'app/models';

$config['url']['root']	     = $rootPathUrl;
$config['url']['domain']     = $_SERVER['SERVER_NAME'];
$config['url']['css']	     = $rootPathUrl .'public/assets/css/';
$config['url']['js']	     = $rootPathUrl .'public/assets/js/';
$config['url']['fonts']	     = $rootPathUrl .'public/assets/fonts/';
$config['url']['images']     = $rootPathUrl .'public/assets/images/';

$config['url']['options']    = $rootPathUrl.'public/options_loader.php';

$head  = "\n\t";

$head .= 
$head .= "\n\t".'<script src="'.$rootPathUrl .'public/assets/css/bootstrap.min.css" rel="stylesheet">'.
$head .= "\n\t".'<script src="'.$rootPathUrl .'public/assets/js/bootstrap.bundle.min.js"></script>'.
$head .= "\n\t".'<script src="'.$rootPathUrl .'public/assets/js/jquery.min.js"></script>';
$head .= "\n\t".'<script src="'.$rootPathUrl .'public/assets/js/' .'jquery.maskedinput.js" type="text/javascript" ></script>';
$head .= "\n\t".'<script src="'.$rootPathUrl .'public/assets/js/' .'script.js"></script>';
$head .= "\n\t".'<link  href="'.$rootPathUrl .'public/assets/css/'.'style.css" rel="stylesheet">';

if(!isset( $_SESSION['login'])){
    
}


$config['head']['defaults']	    =  $head;

// Limpar a $_SESSION
$_SESSION['database'] = [];
$_SESSION['email'] = [];
$_SESSION['path'] = [];
$_SESSION['url'] = [];
$_SESSION['head'] = [];
$_SESSION['login'] = [];

// Copiar os valores de $config para $_SESSION
$_SESSION['database']['host']   = $config['database']['host'];
$_SESSION['database']['schema'] = $config['database']['schema'];
$_SESSION['database']['user']   = $config['database']['user'];
$_SESSION['database']['pass']   = $config['database']['pass'];
$_SESSION['database']['port']   = $config['database']['port'];

$_SESSION['email']['host']      = $config['email']['host'];
$_SESSION['email']['port']      = $config['email']['port'];
$_SESSION['email']['username']  = $config['email']['username'];
$_SESSION['email']['password']  = $config['email']['password'];

$_SESSION['path']['routes']     = $config['path']['routes'];
$_SESSION['path']['root']       = $config['path']['root'];
$_SESSION['path']['css']        = $config['path']['css'];
$_SESSION['path']['js']         = $config['path']['js'];
$_SESSION['path']['font']       = $config['path']['font'];
$_SESSION['path']['images']     = $config['path']['images'];
$_SESSION['path']['uploads']    = $config['path']['uploads'];
$_SESSION['path']['pdoErrors']  = $config['path']['pdoErrors'];

$_SESSION['url']['root']        = $config['url']['root'];
$_SESSION['url']['domain']      = $config['url']['domain'];
$_SESSION['url']['css']         = $config['url']['css'];
$_SESSION['url']['js']          = $config['url']['js'];
$_SESSION['url']['fonts']       = $config['url']['fonts'];
$_SESSION['url']['images']      = $config['url']['images'];
$_SESSION['url']['options']     = $config['url']['options'];

$_SESSION['head']['defaults']   = $config['head']['defaults'];

/* 
$_SESSION['login']['email']     = $config['login']['email'];
$_SESSION['login']['nome']      = $config['login']['nome'];
$_SESSION['login']['idUsuario'] = $config['login']['idUsuario'];
$_SESSION['login']['idNivel']   = $config['login']['idNivel'];
 */

?>
