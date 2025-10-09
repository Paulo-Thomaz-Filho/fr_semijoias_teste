<?php

namespace  core\utils;

class Router {
    
    private $routes = [];

    public function __construct() {
        global $rootPath;
        global $config;
        $json = file_get_contents($config['path']['routes']);
        $routes = json_decode($json, true);
        foreach ($routes as $path => $route) {
            $this->addRoute($path, $rootPath . "/" . $route['path'], $route['sanitize']?? null, $route['sessionKey']?? null, $route['errorPath'] ?? null);
        }
        //print_r(json_decode($config['path']['routes']));
    }
    
    public function addRoute($requiredPath, $physicalPath, $sanitizeConfig, $sessionKey, $errorPath) {
        $this->routes[$requiredPath] = [
            'path' => $physicalPath,
            'sanitize' => $sanitizeConfig,
            'sessionKey' => $sessionKey,
            'errorPath' => $errorPath
        ];
    }
    
    public function dispatch($module = null) {
        // Inicia a sessão se ainda não foi iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $accessGranted = false;
        
        if (str_starts_with($module, "public/")) {
            $fileExtension = strtolower(pathinfo($module, PATHINFO_EXTENSION));
            switch ($fileExtension) {
            case 'php':
                require $module; return;
            case 'html':
                require $module; return;
            default:
                $this->headerMimeTypes($fileExtension);
                echo \file_get_contents($module); return;
            }
        }
        
        if (isset($this->routes[$module])) {
            $route = $this->routes[$module];
            if (file_exists($route['path'])) {
                $accessGranted = true;
                if (isset($route['sessionKey'])) {
                    foreach ($route['sessionKey'] as $sessionRequirement) {
                        foreach ($sessionRequirement as $key => $value) {
                            if ($value === true) {
                                if (!isset($_SESSION[$key])) {
                                    $accessGranted = false;
                                    break 2;
                                }
                            } elseif (is_array($value)) {
                                if (!isset($_SESSION[$key]) || !in_array($_SESSION[$key], $value)) {
                                    $accessGranted = false;
                                    break 2;
                                }
                            }
                        }
                    }
                }
                
                if ($accessGranted) {
                    if ($route['sanitize']['requestVars'] ?? false) {
                        (new Sanitize(true, false, false))->getCleanRequestVars();
                    }
                    require $route['path'];
                } else {
                    $this->redirectToErrorPage($route);
                }
            } else {
                // Se o arquivo físico da rota não existir, redireciona para a função notFound
                http_response_code(404);
                header('Content-Type: application/json');
                header('Content-Type: text/html; charset=utf-8');
                header('HTTP/1.0 404 Not Found');
                $response = [
                    'error' => '404 - Rota requerida existe, mas o recurso requerido não encontrado!',
                    'errorno' => 404,
                ];
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
            }
        } else {
            // Se a rota não estiver definida, redireciona para a função notFound
            http_response_code(404);
            header('Content-Type: application/json');
            header('Content-Type: text/html; charset=utf-8');
            header('HTTP/1.0 404 Route Module Not Found');
            $response = [
                'error' => '404 - Rota requerida não encontrada!',
                'errorno' => 404,
            ];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        }
    }
    
    private function redirectToErrorPage($route) {
        if (isset($route['errorPath']) && file_exists($route['errorPath'])) {
            require $route['errorPath'];
        } else {
            $this->notFound();
        }
    }
    
    public function notFound() {
        if (isset($this->routes['404']) && file_exists($this->routes['404']['path'])) {
            require $this->routes['404']['path'];
        } else {
            header('Content-Type: application/json'); 
            header('Content-Type: text/html; charset=utf-8');
            header('HTTP/1.0 404 Not Found');
            $response = [
                'error' => '404 - Page not found.',
                'errorno' => 404,
                'GET' => $_GET,
                'POST' => $_POST,
                'REQUEST' => $_REQUEST
            ];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        }
    }
    
    function headerMimeTypes($extension) {
        $jsonMimeTypes = '[
            {"extension": "jpg",  "mimetype": "image/jpeg"},
            {"extension": "jpeg", "mimetype": "image/jpeg"},
            {"extension": "png",  "mimetype": "image/png"},
            {"extension": "gif",  "mimetype": "image/gif"},
            {"extension": "svg",  "mimetype": "image/svg+xml"},
            {"extension": "wav",  "mimetype": "audio/wav"},
            {"extension": "mp3",  "mimetype": "audio/mpeg"},
            {"extension": "pdf",  "mimetype": "application/pdf"},
            {"extension": "css",  "mimetype": "text/css"},
            {"extension": "js",   "mimetype": "text/javascript"},
            {"extension": "json", "mimetype": "application/json"},
            {"extension": "html", "mimetype": "text/html"},
            {"extension": "txt",  "mimetype": "text/plain"},
            {"extension": "xml",  "mimetype": "application/xml"},
            {"extension": "doc",  "mimetype": "application/msword"},
            {"extension": "docx", "mimetype": "application/vnd.openxmlformats-officedocument.wordprocessingml.document"},
            {"extension": "xls",  "mimetype": "application/vnd.ms-excel"},
            {"extension": "xlsx", "mimetype": "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"},
            {"extension": "ppt",  "mimetype": "application/vnd.ms-powerpoint"},
            {"extension": "pptx", "mimetype": "application/vnd.openxmlformats-officedocument.presentationml.presentation"},
            {"extension": "mp4",  "mimetype": "video/mp4"},
            {"extension": "avi",  "mimetype": "video/x-msvideo"},
            {"extension": "mov",  "mimetype": "video/quicktime"},
            {"extension": "flv",  "mimetype": "video/x-flv"},
            {"extension": "webm", "mimetype": "video/webm"},
            {"extension": "mkv",  "mimetype": "video/x-matroska"},
            {"extension": "zip",  "mimetype": "application/zip"},
            {"extension": "rar",  "mimetype": "application/x-rar-compressed"},
            {"extension": "tar",  "mimetype": "application/x-tar"},
            {"extension": "gz",   "mimetype": "application/gzip"},
            {"extension": "bz2",  "mimetype": "application/x-bzip2"},
            {"extension": "7z",   "mimetype": "application/x-7z-compressed"},
            {"extension": "ico",  "mimetype": "image/x-icon"},
            {"extension": "tiff", "mimetype": "image/tiff"},
            {"extension": "bmp",  "mimetype": "image/bmp"},
            {"extension": "psd",  "mimetype": "image/vnd.adobe.photoshop"},
            {"extension": "eps",  "mimetype": "application/postscript"},
            {"extension": "ai",   "mimetype": "application/postscript"},
            {"extension": "otf",  "mimetype": "font/otf"},
            {"extension": "ttf",  "mimetype": "font/ttf"},
            {"extension": "woff", "mimetype": "font/woff"},
            {"extension": "woff2","mimetype": "font/woff2"}
        ]';
        
        $mimeType = 'application/octet-stream';
        $mimeTypes = json_decode($jsonMimeTypes, true);
        foreach ($mimeTypes as $type) {
            if ($type['extension'] === $extension) {
                $mimeType = $type['mimetype'];
                break;
            }
        }
        if (str_starts_with($mimeType, 'text/') || $mimeType === 'application/json' || $mimeType === 'application/javascript') {
            header('Content-Type: ' . $mimeType . '; charset=utf-8');
        } else {
            header('Content-Type: ' . $mimeType);
        }
    }
}

?>
