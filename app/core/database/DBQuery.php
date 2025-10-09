<?php

namespace core\database;

use Exception;
use InvalidArgumentException;
use PDOException;

/**
 * Classe DBQuery
 *
 * Esta classe representa uma consulta ao banco de dados.
 *
 * @package app\core\database
 */
class DBQuery {
    
    /**
     * @var object $conn A conexão com o banco de dados
     */
    private $conn;
    
    /**
     * @var string $tableName O nome da tabela
     */
    private $tableName;
    
    /**
     * @var array $fieldsName Os nomes dos campos da tabela
     */
    private $fieldsName;
    
    /**
     * @var array $primaryKeys As chaves primárias da tabela
     */
    private $primaryKeys = [];
    
    /**
     * @var array $foreignKeys As chaves estrangeiras da tabela
     */
    private $foreignKeys = [];
    
    /**
     * @var array $uniqueKeys As chaves únicas da tabela
     */
    private $uniqueKeys = [];
    
    /**
     * @var array $joins As cláusulas JOIN da consulta
     */
    private $joins = [];
    
    /**
     * @var array $outerJoins As cláusulas OUTER JOIN da consulta
     */
    private $outerJoins = [];
    
    /**
     * Construtor da classe DBQuery
     *
     * @param string $tableName O nome da tabela
     * @param string $fieldsNames Os nomes dos campos da tabela separados por vírgula
     * @param mixed $primaryKeys As chaves primárias da tabela (pode ser uma string ou um array)
     */
    public function __construct($tableName, $fieldsNames, $primaryKeys) {
        $this->tableName = $tableName;
        $this->fieldsName = explode(',', $fieldsNames);
        for ($i = 0; $i < count($this->fieldsName); $i++) {
            $this->fieldsName[$i] = trim($this->fieldsName[$i]);
        }
        $this->primaryKeys = is_array($primaryKeys) ? $primaryKeys : [$primaryKeys];
        $this->conn = (new DBConnection())->getConn();
    }
    
    /**
     * Adiciona uma cláusula JOIN à consulta
     *
     * @param string $type O tipo de junção (INNER, LEFT, RIGHT, FULL, CROSS)
     * @param string $fieldname O nome do campo da tabela atual usado na junção
     * @param DBQuery $dbQueryJoined A consulta à tabela que será unida
     * @param string $fieldNameJoined O nome do campo da tabela unida usado na junção
     * @throws InvalidArgumentException Se o tipo de junção for inválido
     * @return void
     */
    public function addJoin($type, $fieldname, DBQuery $dbQueryJoined, $fieldNameJoined) {
        if (!in_array($type, ["INNER","LEFT","RIGHT","FULL","CROSS"])) {
            throw new InvalidArgumentException("Tipo de junção inválido: $type");
        }
        $this->joins[] = $type . " JOIN " . $dbQueryJoined->getTableName() . " ON " . $this->tableName . "." . $fieldname . " = " . $dbQueryJoined->getTableName() . "." . $fieldNameJoined;
    }
    
    /**
     * Adiciona uma cláusula OUTER JOIN à consulta
     *
     * @param string $type O tipo de junção externa (LEFT, RIGHT, FULL)
     * @param string $fieldname O nome do campo da tabela atual usado na junção
     * @param DBQuery $dbQueryJoined A consulta à tabela que será unida
     * @param string $fieldNameJoined O nome do campo da tabela unida usado na junção
     * @throws InvalidArgumentException Se o tipo de junção externa for inválido
     * @return void
     */
    public function addOuterJoin($type, $fieldname, DBQuery $dbQueryJoined, $fieldNameJoined) {
        if (!in_array($type, ["LEFT","RIGHT","FULL"])) {
            throw new InvalidArgumentException("Tipo de junção externa inválido: $type");
        }
        $this->outerJoins[] = $type . " OUTER JOIN " . $dbQueryJoined->getTableName() . " ON " . $this->tableName . "." . $fieldname . " = " . $dbQueryJoined->getTableName() . "." . $fieldNameJoined;
    }
    
    /**
     * Adiciona chaves únicas à tabela
     *
     * @param array $uniqueKeys As chaves únicas a serem adicionadas
     * @return void
     */
    public function addUniqueKeys($uniqueKeys) {
        $this->uniqueKeys[] = $uniqueKeys;
    }
    
    /**
     * Adiciona chaves estrangeiras à tabela
     *
     * @param string $field O campo da tabela atual usado como chave estrangeira
     * @param string $referenceTable A tabela de referência para a chave estrangeira
     * @param string $referenceField O campo de referência para a chave estrangeira
     * @return void
     */
    public function addForeignKeys($field, $referenceTable, $referenceField) {
        $this->foreignKeys[] = ['field' => $field, 'referenceTable' => $referenceTable, 'referenceField' => $referenceField];
    }
    
    /**
     * Executa uma consulta SELECT sem filtros
     *
     * @return mixed O resultado da consulta
     */
    public function select() {
        $fields = implode(', ', $this->fieldsName);
        $sql = "SELECT {$fields} FROM {$this->tableName}";
        return ($this->conn->query($sql))->fetchAll(\PDO::FETCH_ASSOC);
    }
  
    public function selectWhere($where) {
        $fields = implode(', ', $this->fieldsName);
        $sql = "SELECT {$fields} FROM {$this->tableName}" . (($where!="")?$where->build():"");
        return ($this->conn->query($sql))->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Executa uma consulta SELECT com filtros
     *
     * @param Where $where O objeto Where contendo as condições de filtro
     * @return mixed O resultado da consulta
     */
    public function selectFiltered(Where $where) {
        $fields = implode(', ', $this->fieldsName);
        $sql = "SELECT {$fields} FROM {$this->tableName}";
        foreach ($this->joins as $join) {
            $sql .= " " . $join;
        }
        foreach ($this->outerJoins as $outerJoin) {
            $sql .= " " . $outerJoin;
        }
        $sql .= $where->build();
        return ($this->conn->query($sql))->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Executa uma consulta de inserção de dados na tabela
     *
     * @param array $values Os valores a serem inseridos na tabela
     * @throws InvalidArgumentException Se o número de valores não for equivalente aos campos da tabela
     * @throws Exception Se ocorrer uma violação de chave única ou estrangeira
     * @return mixed O resultado da consulta
     */
    public function insert($values) {
        $values = array_map(function($value) {
            return is_null($value) ? null : addslashes($value);
        }, $values);
        
        if (count($values) !== count($this->fieldsName)) {
            throw new InvalidArgumentException("O número de valores informados não é equivalente aos campos da tabela!");
        }
        
        $sql = "INSERT INTO {$this->tableName} (" . implode(', ', $this->fieldsName) . ")";
        $sql .= " VALUES ('" . implode("', '", $values) . "')";
        try {
            // return ($this->conn->query($sql))->fetchAll(\PDO::FETCH_ASSOC);
            $this->conn->query($sql);
            return $this->conn->lastInsertId(); 
        } catch ( PDOException $error) {
            if ($error->getCode() == 23000) {
                throw new \Exception('Violação de chave única ou estrangeira.');
            }
            throw $error;
        }
    }
    
    /**
     * Executa uma consulta de atualização de dados na tabela
     *
     * @param array $values Os novos valores dos campos a serem atualizados
     * @throws InvalidArgumentException Se a quantidade de campos for diferente da quantidade de valores
     * @throws Exception Se ocorrer uma violação de chave única ou estrangeira
     * @return mixed O resultado da consulta
     */
    public function update($values) {
        $values = array_map('addslashes', $values);
        
        if (count($values) !== count($this->fieldsName)) {
            throw new InvalidArgumentException("A quantidade de campos é diferente da quantidade de valores!");
        }
        
        $sql = "UPDATE {$this->tableName} SET ";
        
        for ($i = 0; $i < count($this->fieldsName); $i++) {
            if (!array_key_exists($this->fieldsName[$i], $values)) {
                throw new InvalidArgumentException("Chave não encontrada no array de valores: {$this->fieldsName[$i]}");
            }
            $sql .= "{$this->fieldsName[$i]} = '{$values[$this->fieldsName[$i]]}'";
            if ($i !== count($this->fieldsName) - 1) {
                $sql .= ", ";
            }
        }
        
        $where = new Where();
            
        foreach ($this->primaryKeys as $primaryKey) {
            $position = array_search($primaryKey, $this->fieldsName);
            if ($position !== false && isset($values[$this->fieldsName[$position]])) {
                $where->addCondition("AND", $primaryKey, "=", $values[$this->fieldsName[$position]]);
            } else {
                throw new InvalidArgumentException("Valor da chave primária não encontrado: $primaryKey");
            }
        }
        $sql .= $where->build();
        
        try {
            $stmt = $this->conn->query($sql);
            return $stmt->rowCount();
        } catch ( PDOException $error) {
            if ($error->getCode() == 23000) {
                throw new Exception('Violação de chave única ou estrangeira.');
            }
            throw $error;
        }
    }
    
    /**
     * Executa uma consulta de exclusão de dados na tabela
     *
     * @throws InvalidArgumentException Se ocorrer uma violação de chave única ou estrangeira
     * @return mixed O resultado da consulta
     */
    public function delete($values) {
        $values = array_map('addslashes', $values);
        
        if (count($values) !== count($this->fieldsName)) {
            throw new InvalidArgumentException("A quantidade de campos é diferente da quantidade de valores!");
        }
        
        $sql = "DELETE FROM {$this->tableName} ";
        $where = new Where();
        
        foreach ($this->primaryKeys as $primaryKey) {
            $position = array_search($primaryKey, $this->fieldsName);
            if ($position !== false && isset($values[$this->fieldsName[$position]])) {
                $where->addCondition("AND", $primaryKey, "=", $values[$this->fieldsName[$position]]);
            } else {
                throw new InvalidArgumentException("Valor da chave primária não encontrado: $primaryKey");
            }
        }
        $sql .= $where->build();
        
        try {
            $stmt = $this->conn->query($sql);
            return $stmt->rowCount();
        } catch ( PDOException $error) {
            if ($error->getCode() == 23000) {
                throw new Exception('Violação de chave única ou estrangeira.');
            }
            throw $error;
        }
    }
    
    /**
     * Obtém a conexão com o banco de dados
     *
     * @return object A conexão com o banco de dados
     */
    public function getConn() {
        return $this->conn;
    }
    
    /**
     * Define a conexão com o banco de dados
     *
     * @param object $conn A conexão com o banco de dados
     * @return void
     */
    public function setConn($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Obtém o nome da tabela
     *
     * @return string O nome da tabela
     */
    public function getTableName() {
        return $this->tableName;
    }
    
    /**
     * Define o nome da tabela
     *
     * @param string $tableName O nome da tabela
     * @return void
     */
    public function setTableName($tableName) {
        $this->tableName = $tableName;
    }
    
    /**
     * Obtém os nomes dos campos da tabela
     *
     * @return array Os nomes dos campos da tabela
     */
    public function getFieldsName() {
        return $this->fieldsName;
    }
    
    /**
     * Define os nomes dos campos da tabela
     *
     * @param string $fieldsName Os nomes dos campos da tabela separados por vírgula
     * @return void
     */
    public function setFieldsName($fieldsName) {
        $this->fieldsName = explode(',', $fieldsName);
        for ($i = 0; $i < count($this->fieldsName); $i++) {
            $this->fieldsName[$i] = trim($this->fieldsName[$i]);
        }
    }
    
    /**
     * Obtém as chaves primárias da tabela
     *
     * @return array As chaves primárias da tabela
     */
    public function getPrimaryKeys() {
        return $this->primaryKeys;
    }
    
    /**
     * Define as chaves primárias da tabela
     *
     * @param array $primaryKeys As chaves primárias da tabela
     * @return void
     */
    public function setPrimaryKeys($primaryKeys) {
        $this->primaryKeys = $primaryKeys;
    }
    
    /**
     * Obtém as chaves estrangeiras da tabela
     *
     * @return array As chaves estrangeiras da tabela
     */
    public function getForeignKeys() {
        return $this->foreignKeys;
    }
    
    /**
     * Define as chaves estrangeiras da tabela
     *
     * @param array $foreignKeys As chaves estrangeiras da tabela
     * @return void
     */
    public function setForeignKeys($foreignKeys) {
        $this->foreignKeys = $foreignKeys;
    }
    
    /**
     * Obtém as chaves únicas da tabela
     *
     * @return array As chaves únicas da tabela
     */
    public function getUniqueKeys() {
        return $this->uniqueKeys;
    }
    
    /**
     * Define as chaves únicas da tabela
     *
     * @param array $uniqueKeys As chaves únicas da tabela
     * @return void
     */
    public function setUniqueKeys($uniqueKeys) {
        $this->uniqueKeys = $uniqueKeys;
    }
}
?>
