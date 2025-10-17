<?php

namespace app\models;

use core\database\DBConnection;
use core\database\DBQuery;
use core\database\Where;
use PDO;

include_once __DIR__.'/../core/database/DBConnection.php';
include_once __DIR__.'/../core/database/DBQuery.php';
include_once __DIR__.'/../core/database/Where.php';
include_once __DIR__.'/Categoria.php';

class CategoriaDAO {
    private $dbQuery;
    private $conn;

    public function __construct(){
        $this->dbQuery = new DBQuery(
            'categorias', 
            'id_categoria, nome', 
            'id_categoria'
        );
        $this->conn = (new \core\database\DBConnection())->getConn();
    }

    public function getAll(){
        $categorias = [];
        $dados = $this->dbQuery->select();

        foreach($dados as $categoria){
            $obj = new Categoria();
            $obj->load($categoria['id_categoria'], $categoria['nome']);
            $categorias[] = $obj;
        }

        return $categorias;
    }

    public function getById($id){
        $where = new Where();
        $where->addCondition('AND', 'id_categoria', '=', $id);
        $dados = $this->dbQuery->selectFiltered($where);

        if($dados){
            return new Categoria(...array_values($dados[0]));
        }

        return null;
    }

    public function insert(Categoria $categoria){
        $dados = [
            null,
            $categoria->getNome()
        ];
        return $this->dbQuery->insert($dados);
    }

    public function update(Categoria $categoria){
        $dados = [
            'id_categoria' => $categoria->getIdCategoria(),
            'nome'         => $categoria->getNome()
        ];
        return $this->dbQuery->update($dados);
    }

    public function delete($id){
        try {
            $sql = "DELETE FROM categorias WHERE id_categoria = :id";
                $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            throw new \Exception('Erro ao deletar categoria: ' . $e->getMessage());
        }
    }
}
