<?php
/**
 * Namespace para a classe DBConnection
 */
namespace core\database;

use PDOException;
use RuntimeException;


/**
 * Classe DBConnection para estabelecer uma conexão com o banco de dados.
 */

class DBConnection {
    
    /**
     * Propriedade para armazenar a conexão PDO.
     *
     * @var \PDO
     */
    private $conn;
    
    /**
     * Construtor da classe DBConnection.
     *
     * @throws \InvalidArgumentException se a configuração do banco de dados estiver incompleta.
     * @throws \RuntimeException se a conexão com o banco de dados falhar.
     */
    function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset( $_SESSION['database']['host'], $_SESSION['database']['user'], $_SESSION['database']['pass'], $_SESSION['database']['schema'])) {
            throw new \InvalidArgumentException("Configuração de banco de dados incompleta.");
        }
        
        try {
            $dsn = "mysql:host={$_SESSION['database']['host']};dbname={$_SESSION['database']['schema']}";
            $this->conn = new \PDO($dsn, $_SESSION['database']['user'], $_SESSION['database']['pass']);
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch( PDOException $e) {
            throw new RuntimeException("Connection failed: " . $e->getMessage());
        }
    }
    
    /**
     * Função para realizar uma query SQL no banco de dados.
     *
     * @param string $sqlCommand Comando SQL para ser executado.
     * 
     * @throws \RuntimeException se a query falhar.
     * 
     * @return array Retornará os resultados da query.
     */
    public function query($sqlCommand) {
        try {
            $stmt = $this->conn->query($sqlCommand);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $error) {
            throw new \RuntimeException("Erro: " . $this->getErroMensage("mysql",$error->getCode()) . "\n". $error->getMessage());
        }
    }
    
    public function prepareQuery($sqlCommand, $values) {
        try {
            $stmt = $this->conn->prepare($sqlCommand);
            $stmt->execute($values);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $error) {
            throw new \RuntimeException("Erro: " . $this->getErroMensage("mysql",$error->getCode()) . "\n". $error->getMessage());
        }
    }
    
    public function getErroMensage($sgbd, $errorNumber) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        global $mensagensDeErro;
        require $_SESSION['path']['pdoErrors'];
        return ( $mensagensDeErro[$sgbd][$errorNumber] );
    }
    
    function getConn() {
        return ($this->conn);
    }
}

?>
