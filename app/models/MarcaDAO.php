<?php
namespace app\models;

use core\database\DBQuery;
use core\database\Where;

include_once __DIR__.'/../core/database/DBConnection.php';
include_once __DIR__.'/../core/database/DBQuery.php';
include_once __DIR__.'/../core/database/Where.php';

include_once __DIR__.'/Marca.php';

class MarcaDAO {
    private $dbQuery;
    private $conn;

    public function __construct(){
        $this->dbQuery = new DBQuery(
            'marcas', 
            'id_marca, nome', 
            'id_marca'
        );
        $this->conn = (new \core\database\DBConnection())->getConn();
    }

    public function getAll(){
        $marcas = [];
        $dados = $this->dbQuery->select();

        foreach($dados as $marca){
            $obj = new Marca();
            $obj->load($marca['id_marca'], $marca['nome']);
            $marcas[] = $obj;
        }

        return $marcas;
    }

    public function getById($id){
        $where = new Where();
        $where->addCondition('AND', 'id_marca', '=', $id);
        $dados = $this->dbQuery->selectFiltered($where);

        if($dados){
            return new Marca(...array_values($dados[0]));
        }

        return null;
    }

    public function insert(Marca $marca){
        $dados = [
            null,
            $marca->getNome(),
        ];
        return $this->dbQuery->insert($dados);
    }

    public function update(Marca $marca){
        $dados = [
            'id_marca' => $marca->getIdMarca(),
            'nome'     => $marca->getNome(),
        ];
        return $this->dbQuery->update($dados);
    }

    public function delete($id){
        try {
            $sql = "DELETE FROM marcas WHERE id_marca = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            throw new \Exception('Erro ao deletar marca: ' . $e->getMessage());
        }
    }
}
