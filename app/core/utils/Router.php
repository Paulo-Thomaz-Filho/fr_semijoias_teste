<?php
// O namespace precisa corresponder à estrutura de pastas para o autoloader funcionar.
namespace App\Core\Utils; 

use PDO;
use Exception;
use ReflectionMethod;

class Router {
    
    private array $routes = [];
    private string $rootPath;
    private ?PDO $pdo = null; // Propriedade para armazenar a conexão e reutilizá-la

    public function __construct() {
        global $rootPath;
        global $config;
        $this->rootPath = $rootPath;
        
        $json = file_get_contents($config['path']['routes']);
        $this->routes = json_decode($json, true);
    }

    /**
     * Cria a conexão com o banco de dados de forma "preguiçosa" (só quando for usada).
     * @return PDO A instância da conexão.
     */
    private function getDbConnection(): PDO {
        if ($this->pdo === null) {
            $dbConfig = $_SESSION['database'];
            $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['schema']};charset=utf8mb4";
            $this->pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }
        return $this->pdo;
    }
    
    /**
     * O método principal que direciona a requisição.
     */
    public function dispatch(string $module = null): void {
        if (isset($this->routes[$module])) {
            $route = $this->routes[$module];

            // A lógica de API (DAO/método) tem prioridade
            if (isset($route['dao']) && isset($route['method'])) {
                $this->handleDaoRoute($route);
            } 
            // Senão, serve um arquivo de página estático
            elseif (isset($route['path'])) {
                $filePath = $this->rootPath . '/' . $route['path'];
                if (file_exists($filePath)) {
                    require $filePath;
                } else {
                    $this->resourceNotFound($module);
                }
            }
            else {
                $this->routeNotFound();
            }
        } else {
            // Se a rota não está no JSON, verifica se é um arquivo público (CSS, JS)
            $publicFilePath = $this->rootPath . '/public/' . $module;
            if(file_exists($publicFilePath) && !is_dir($publicFilePath)) {
                 $this->servePublicFile($publicFilePath);
            } else {
                 $this->routeNotFound();
            }
        }
    }

    /**
     * Manipula as rotas definidas como "API" no routes.json.
     */
    private function handleDaoRoute(array $route): void {
        $daoClass = $route['dao'];
        $methodName = $route['method'];

        if (!class_exists($daoClass) || !method_exists($daoClass, $methodName)) {
            $this->sendJsonResponse(['error' => 'Configuração de rota inválida: DAO ou método não encontrado.'], 500);
            return;
        }

        try {
            // **A CORREÇÃO PRINCIPAL ESTÁ AQUI**
            // 1. Obtém a conexão com o banco.
            $connection = $this->getDbConnection();
            
            // 2. Instancia o DAO, injetando a conexão. Funciona para TODOS os seus DAOs.
            $daoInstance = new $daoClass($connection);
            
            // Lógica para resolver os argumentos do método (simplificada para focar na correção)
            $args = [];
            if (($route['paramSource'] ?? null) === 'POST') {
                $args[] = $_POST; // Assumindo que o método espera um array
            } elseif (($route['paramSource'] ?? null) === 'GET') {
                 $args[] = (int)($_GET['id'] ?? 0); // Exemplo para findById
            }

            // Para o método 'save', precisamos montar o objeto do Modelo
            if (isset($route['model']) && $methodName === 'save') {
                $modelClass = $route['model'];
                $modelInstance = new $modelClass();
                foreach($_POST as $key => $value) {
                    $setter = 'set' . str_replace('_', '', ucwords($key, '_'));
                    if(method_exists($modelInstance, $setter)) {
                        $modelInstance->$setter($value);
                    }
                }
                $result = $daoInstance->save($modelInstance);
            } else {
                // Para outros métodos (findById, delete, findAll)
                $result = call_user_func_array([$daoInstance, $methodName], $args);
            }

            $this->sendJsonResponse($result);

        } catch (Exception $e) {
            $this->sendJsonResponse(['error' => 'Erro ao executar a operação.', 'details' => $e->getMessage()], 500);
        }
    }
    
    // ... Os seus outros métodos (isAccessGranted, sendJsonResponse, etc.) podem ser colados aqui ...
    
    private function sendJsonResponse($data, int $httpCode = 200): void {
        http_response_code($httpCode);
        header('Content-Type: application/json; charset=utf-8');
        if (is_bool($data)) {
            echo json_encode(['success' => $data]);
        } else {
            echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
        exit();
    }
    
    private function servePublicFile(string $filePath): void {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        // ... (Cole aqui seu método headerMimeTypes) ...
        readfile($filePath);
        exit();
    }
    
    public function routeNotFound(): void {
        http_response_code(404);
        // Se houver uma rota 404 definida, mostre-a.
        if (isset($this->routes['404']['path'])) {
            require $this->rootPath . '/' . $this->routes['404']['path'];
        } else {
            $this->sendJsonResponse(['error' => '404 - Rota não encontrada!']);
        }
        exit();
    }
    
    private function resourceNotFound(string $module): void {
        http_response_code(404);
        $this->sendJsonResponse(['error' => "404 - Recurso para a rota '{$module}' não foi encontrado!"]);
        exit();
    }
}