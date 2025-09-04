<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


// Rotas aceitas
$routes = [
    "login"      => "app/views/admin_login.html",
    "inicio"     => "app/views/admin_inicio.html",
    "pedidos"    => "app/views/admin_pedidos.html",
    "produtos"   => "app/views/admin_produtos.html",
    "usuarios"   => "app/views/admin_usuarios.html",
];

// Captura a URI
$uri = $_GET["_uri_"] ?? "";


$METHOD = $_SERVER['REQUEST_METHOD'];
$PARAMETERS = [];

switch(  strtolower($METHOD)){
    case 'get'  :  $PARAMETERS = $_GET??[];   break;
    case 'post' :  $PARAMETERS = $_POST??[];  break;
    case 'put':
    case 'delete': 
        $input = file_get_contents("php://input");
        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';
        if (stripos($contentType, 'application/json') !== false) {
            $PARAMETERS = json_decode($input, true) ?? [];
        } else {
           parse_str($input, $PARAMETERS);
        }
    break; 
    default: $PARAMETERS = [];  break;
}

foreach ($PARAMETERS as $key => $value) {
    $PARAMETERS[$key] = addslashes($value);
}


// usando preg_replace (permitido{letras maiusculas, minusculas, traço e underline}) ao invez de addslash (menos seguro)
// $uri = preg_replace('/[^a-zA-Z0-9_-]/','',$uri);

// Permite acesso à pasta public/
if (str_starts_with($uri, "public/")) {
    $fileExtension = strtolower(pathinfo($uri, PATHINFO_EXTENSION));

    if (!file_exists($uri) || is_dir($uri)) {
        http_response_code(404);
        die("404 - Arquivo não encontrado.");
    }

    ob_clean(); // limpa buffer antes do output
    headerMimeTypes($fileExtension);
    readfile($uri);
    exit;
}

//verifica se a rota existe
if (!array_key_exists($uri, $routes)) {
    http_response_code(404);
    echo "404 - Página não encontrada a.";
    exit;
}

// Proteção contra acessos diretos não permitidos
if (str_starts_with($uri, "app/")) {
    http_response_code(403);
    echo "403 - Acesso proibido.";
    exit;
}

// Verifica se a rota existe
if (!isset($routes[$uri])) {
    http_response_code(404);
    echo "404 - Página não encontrada b.";
    exit;
}

$filePath = $routes[$uri];
if (file_exists($filePath)) {
    $ext = pathinfo($filePath, PATHINFO_EXTENSION);
    headerMimeTypes($ext);
    readfile($filePath);
} else {
    http_response_code(404);
    echo "404 - Arquivo não encontrado c.";
}

function headerMimeTypes($extension) {
    $mimeTypes = [
 // adicione mais conforme necessário
        "html" => "text/html",
        "css"  => "text/css",
        "js"   => "text/javascript",
        "png"  => "image/png",
        "jpg"  => "image/jpeg",
        "jpeg" => "image/jpeg",
        "gif"  => "image/gif",
        "svg"  => "image/svg+xml",
        "pdf"  => "application/pdf",
        "json" => "application/json",
        "txt"  => "text/plain",
        "mp3"  => "audio/mpeg",
        "mp4"  => "video/mp4",
        "woff" => "font/woff",
        "woff2"=> "font/woff2",
        "ttf"  => "font/ttf",
        "otf"  => "font/otf",
    ];
    header("Content-Type: " . ($mimeTypes[$extension] ?? "application/octet-stream"));
}
