<?php

namespace core\utils;

// Não se esqueça de incluir as classes que serão usadas
use App\Models\Usuario;
use App\Models\UsuarioDAO;
// Adicione outros DAOs e Models aqui conforme necessário

class Router {
    
    private array $routes = [];
    private string $rootPath;

    public function __construct() {
        global $rootPath;
        global $config;
        $this->rootPath = $rootPath;
        
        $json = file_get_contents($config['path']['routes']);
        $routes = json_decode($json, true);
        
        foreach ($routes as $path => $route) {
            $this->addRoute($path, $route);
        }
    }
    
    public function addRoute(string $requiredPath, array $routeConfig): void {
        // Resolve o caminho físico se ele existir
        if (isset($routeConfig['path'])) {
            $routeConfig['path'] = $this->rootPath . "/" . $routeConfig['path'];
        }
        $this->routes[$requiredPath] = $routeConfig;
    }
    
    public function dispatch(string $module = null): void {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (str_starts_with($module, "public/")) {
            $this->servePublicFile($module);
            return;
        }
        
        if (isset($this->routes[$module])) {
            $route = $this->routes[$module];
            
            if (!$this->isAccessGranted($route)) {
                $this->redirectToErrorPage($route);
                return;
            }

            // Sanitização (opcional, mantida do seu código original)
            if ($route['sanitize']['requestVars'] ?? false) {
                (new Sanitize(true, false, false))->getCleanRequestVars();
            }

            // ** AQUI ESTÁ A NOVA LÓGICA **
            // Se a rota define um DAO e um método, entra no modo API
            if (isset($route['dao']) && isset($route['method'])) {
                $this->handleDaoRoute($route);
            } 
            // Senão, usa o modo antigo de incluir o arquivo
            elseif (isset($route['path']) && file_exists($route['path'])) {
                require $route['path'];
            } 
            // Erro se a rota está mal configurada ou o arquivo não existe
            else {
                $this->resourceNotFound($module);
            }
        } else {
            $this->routeNotFound();
        }
    }

    /**
     * Verifica se o usuário tem permissão para acessar a rota com base na sessão.
     */
    private function isAccessGranted(array $route): bool {
        if (!isset($route['sessionKey'])) {
            return true; // Rota pública
        }
        
        foreach ($route['sessionKey'] as $sessionRequirement) {
            foreach ($sessionRequirement as $key => $value) {
                if ($value === true) {
                    if (!isset($_SESSION[$key])) return false;
                } elseif (is_array($value)) {
                    if (!isset($_SESSION[$key]) || !in_array($_SESSION[$key], $value)) {
                        return false;
                    }
                } elseif (isset($_SESSION[$key]) && $_SESSION[$key] !== $value) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Manipula a rota no modo DAO/API.
     */
    private function handleDaoRoute(array $route): void {
        $daoClass = $route['dao'];
        $methodName = $route['method'];

        if (!class_exists($daoClass) || !method_exists($daoClass, $methodName)) {
            $this->sendJsonResponse(['error' => 'Configuração de rota inválida: classe ou método não encontrado.'], 500);
            return;
        }

        try {
            $daoInstance = new $daoClass();
            $reflectionMethod = new \ReflectionMethod($daoClass, $methodName);
            $args = $this->resolveMethodArguments($reflectionMethod, $route);
            
            $result = $reflectionMethod->invokeArgs($daoInstance, $args);

            $this->sendJsonResponse($result);

        } catch (\Exception $e) {
            $this->sendJsonResponse(['error' => 'Erro ao executar a operação.', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Usa Reflection para montar os argumentos para o método do DAO.
     */
    private function resolveMethodArguments(\ReflectionMethod $method, array $route): array {
        $dataSource = [];
        $paramSource = $route['paramSource'] ?? null;
        if ($paramSource === 'GET') {
            $dataSource = $_GET;
        } elseif ($paramSource === 'POST') {
            $dataSource = $_POST;
        }

        // Caso especial para o método 'save' que recebe um objeto Model
        if (isset($route['model']) && $method->getNumberOfParameters() === 1) {
            $modelClass = $route['model'];
            $reflectionParam = $method->getParameters()[0];
            $paramType = $reflectionParam->getType();

            if ($paramType && !$paramType->isBuiltin() && $paramType->getName() === $modelClass) {
                $modelInstance = new $modelClass();
                // Preenche o modelo com os dados do POST/PUT
                foreach ($dataSource as $key => $value) {
                    $setter = 'set' . ucfirst(str_replace('_', '', ucwords($key, '_')));
                    if (method_exists($modelInstance, $setter)) {
                        $modelInstance->$setter($value);
                    }
                }
                // Se o ID estiver no GET (para updates), define também
                if (isset($_GET['id_usuario'])) {
                    $modelInstance->setId((int)$_GET['id_usuario']);
                }

                return [&$modelInstance]; // Passa por referência
            }
        }

        // Lógica geral para outros métodos
        $args = [];
        foreach ($method->getParameters() as $param) {
            $paramName = $param->getName();
            if (isset($dataSource[$paramName])) {
                $args[] = $dataSource[$paramName];
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            } else {
                throw new \Exception("Parâmetro obrigatório '{$paramName}' não encontrado na requisição.");
            }
        }
        return $args;
    }

    /**
     * Envia uma resposta em formato JSON.
     */
    private function sendJsonResponse($data, int $httpCode = 200): void {
        http_response_code($httpCode);
        header('Content-Type: application/json; charset=utf-8');
        // Para DAOs que retornam booleano (update, delete), retorna um status
        if (is_bool($data)) {
            echo json_encode(['success' => $data]);
        } 
        // Para DAOs que retornam objetos ou arrays, serializa eles
        else {
            echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
    }
    
    private function servePublicFile(string $module): void {
        $fileExtension = strtolower(pathinfo($module, PATHINFO_EXTENSION));
        if (in_array($fileExtension, ['php', 'html'])) {
            require $module;
            return;
        }
        $this->headerMimeTypes($fileExtension);
        if (file_exists($module)) {
            echo file_get_contents($module);
        } else {
            http_response_code(404);
        }
    }

    private function redirectToErrorPage(array $route): void {
        if (isset($route['errorPath']) && file_exists($route['errorPath'])) {
            require $route['errorPath'];
        } else {
            $this->routeNotFound();
        }
    }
    
    public function routeNotFound(): void {
        http_response_code(404);
        $this->sendJsonResponse(['error' => '404 - Rota requerida não encontrada!', 'details' => $_GET]);
    }
    
    private function resourceNotFound(string $module): void {
        http_response_code(404);
        $this->sendJsonResponse(['error' => "404 - Rota '{$module}' existe, mas o recurso (arquivo ou DAO) não foi encontrado ou está mal configurado!"]);
    }
    
    // Seu método headerMimeTypes permanece o mesmo...
    private function headerMimeTypes($extension) {
        // ... (código original sem alterações)
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