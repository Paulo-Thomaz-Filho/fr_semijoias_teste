<?php
/**
 * Classe Sanitize para limpeza de variáveis de entrada.
 * @package app\core\utils
 */

namespace core\utils;

class Sanitize {
    
    private $cleanCode = false;
    private $cleanSql = false;
    private $cleanRequestVars = false;
    private $input_vars = [];
    
    public function __construct($cleanRequestVars = true, $cleanCode = true, $cleanSql = true) {
        $this->cleanCode        = (bool) $cleanCode;
        $this->cleanSql         = (bool) $cleanSql;
        $this->cleanRequestVars = (bool) $cleanRequestVars;
        if ($this->cleanRequestVars) {
            $this->clearRequestHttp();
        }
    }
    
    public function clearRequestHttp() {
        parse_str(file_get_contents("php://input"), $this->input_vars);
        $_GET       = $this->array_map_recursive([$this, 'clean'], $_GET);
        $_POST      = $this->array_map_recursive([$this, 'clean'], $_POST);
        $_REQUEST   = $this->array_map_recursive([$this, 'clean'], $_REQUEST);
        $this->input_vars = $this->array_map_recursive([$this, 'clean'], $this->input_vars);
    }
    
    public function clean($data) {
        switch (gettype($data)) {
            case "NULL":
            case "boolean":
            case "integer":
            case "double":
                return $data;
            break;
            case"string":
                // Deprecated $data = filter_var($data, FILTER_SANITIZE_STRING);
                if ($this->cleanCode) {
                    $data = strip_tags($data);
                }
                if ($this->cleanSql) {
                    $data = addslashes($data);
                }
                return $data;
            break;
            case "array":
                return $this->array_map_recursive([$this, 'clean'], $data);
            break;
            case"object":
                $properties = get_object_vars($data);
                foreach ($properties as $property => $value) {
                    $data->$property = $this->clean($value);
                }
                return $data;
            break;
            case "resource":
            case "resource (closed)":
            case "callable":
                return $data;
            break;
            default:
                return $data;
            break;
        }
    }
    
    private function array_map_recursive($callback, $array) {
        $result = [];
        foreach ($array as $key => $value) {
            $result[$key] = is_array($value) ? $this->array_map_recursive($callback, $value) : call_user_func($callback, $value);
        }
        return $result;
    }
    
	public function getCleanCode(){
		return $this->cleanCode;
	}

	public function setCleanCode($cleanCode){
		$this->cleanCode = $cleanCode;
	}

	public function getCleanSql(){
		return $this->cleanSql;
	}

	public function setCleanSql($cleanSql){
		$this->cleanSql = $cleanSql;
	}

	public function getCleanRequestVars(){
		return $this->cleanRequestVars;
	}

	public function setCleanRequestVars($cleanRequestVars){
		$this->cleanRequestVars = $cleanRequestVars;
	}

	public function getInput_vars(){
		return $this->input_vars;
	}

	public function setInput_vars($input_vars){
		$this->input_vars = $input_vars;
	}

}

// Exemplo de uso da classe Sanitize
// $sanitize = new Sanitize();
// $data = "<script>alert('Hello World!');</script>";
// $cleanData = $sanitize->clean($data);
// echo $cleanData; // Saída: "alert('Hello World!');"
?>
