<?php
// O namespace precisa corresponder à estrutura de pastas para o autoloader funcionar.
namespace App\Core\Utils; 

use PDO;
use Exception;

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
            // Recomenda-se mover a lógica de config para fora da sessão no futuro
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

            // 1. Verifica se a rota é para um Controller
            if (isset($route['controller']) && isset($route['method'])) {
                $this->handleControllerRoute($route);
                return; 
            }
            
            // 2. Verifica se a rota é para um DAO
            if (isset($route['dao']) && isset($route['method'])) {
                $this->handleDaoRoute($route);
            } 
            // 3. Senão, serve um arquivo de página estático
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
     * Manipula rotas que apontam para uma classe Controller.
     */
    private function handleControllerRoute(array $route): void {
        $controllerClass = $route['controller'];
        $methodName = $route['method'];

        if (!class_exists($controllerClass) || !method_exists($controllerClass, $methodName)) {
            $this->sendJsonResponse(['error' => 'Configuração de rota inválida: Controller ou método não encontrado.'], 500);
            return;
        }

        try {
            $connection = $this->getDbConnection();
            
            // Instancia o Controller, passando a conexão (caso ele precise)
            $controllerInstance = new $controllerClass($connection);
            
            // Chama o método do controller (ex: 'checkAuthStatus')
            $controllerInstance->$methodName();

        } catch (Exception $e) {
            $this->sendJsonResponse(['error' => 'Erro ao executar a operação.', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Manipula rotas que apontam diretamente para uma classe DAO.
     */
    private function handleDaoRoute(array $route): void {
        $daoClass = $route['dao'];
        $methodName = $route['method'];

        if (!class_exists($daoClass) || !method_exists($daoClass, $methodName)) {
            $this->sendJsonResponse(['error' => 'Configuração de rota inválida: DAO ou método não encontrado.'], 500);
            return;
        }

        try {
            $connection = $this->getDbConnection();
            $daoInstance = new $daoClass($connection);
            
            // Lógica para cada tipo de método
            switch ($methodName) {
                case 'save':
                    $modelClass = $route['model'];
                    if (!class_exists($modelClass)) {
                        throw new Exception("Classe de modelo '{$modelClass}' não encontrada para a rota.");
                    }
                    $modelInstance = new $modelClass();
                    $inputData = json_decode(file_get_contents('php://input'), true) ?: $_POST;

                    // Verifica se um ID foi passado no corpo dos dados para saber se é um UPDATE
                    if (!empty($inputData['id_produto'])) { // Ajuste 'id_produto' para a chave primária genérica se necessário
                        $modelInstance->setId((int)$inputData['id_produto']);
                    }

                    foreach ($inputData as $key => $value) {
                        $setter = 'set' . str_replace('_', '', ucwords($key, '_'));
                        if (method_exists($modelInstance, $setter)) {
                            $modelInstance->$setter($value);
                        }
                    }
                    $success = $daoInstance->save($modelInstance);
                    $this->sendJsonResponse(['success' => $success]);
                    break;

                case 'login':
                    $inputData = json_decode(file_get_contents('php://input'), true) ?: $_POST;
                    $email = $inputData['email'] ?? '';
                    $senha = $inputData['senha'] ?? '';
                    $tipoAcesso = $daoInstance->login($email, $senha);

                    if ($tipoAcesso) {
                        $this->sendJsonResponse(['success' => true, 'isAdmin' => ($tipoAcesso === 'admin')]);
                    } else {
                        $this->sendJsonResponse(['success' => false, 'error' => 'Credenciais inválidas.']);
                    }
                    break;

                default: // Lida com findById, findAll, delete, etc.
                    $args = [];
                    if (($route['paramSource'] ?? null) === 'GET' && isset($_GET['id'])) {
                        $args[] = (int)$_GET['id'];
                    }
                    
                    $result = call_user_func_array([$daoInstance, $methodName], $args);
                    $this->sendJsonResponse($result);
                    break;
            }
        } catch (Exception $e) {
            $this->sendJsonResponse(['error' => 'Erro ao executar a operação.', 'details' => $e->getMessage()], 500);
        }
    }
    
    private function sendJsonResponse($data, int $httpCode = 200): void {
        http_response_code($httpCode);
        header('Content-Type: application/json; charset=utf-8');
        // Adicionado para lidar com o caso de $data ser nulo
        if ($data === null) {
            echo json_encode([]);
        } elseif (is_bool($data)) {
            echo json_encode(['success' => $data]);
        } else {
            echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
        exit();
    }
    
    private function servePublicFile(string $filePath): void {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'svg' => 'image/svg+xml',
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
        ];
        $contentType = $mimeTypes[$extension] ?? 'application/octet-stream';
        header("Content-Type: $contentType");
        readfile($filePath);
        exit();
    }
    
    public function routeNotFound(): void {
        http_response_code(404);
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
