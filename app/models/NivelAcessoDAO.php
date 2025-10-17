<?php

namespace app\models;

use core\database\DBConnection;
use core\database\DBQuery;
use core\database\Where;
use PDO;

include_once __DIR__.'/../core/database/DBConnection.php';
include_once __DIR__.'/../core/database/DBQuery.php';
include_once __DIR__.'/../core/database/Where.php';
include_once __DIR__.'/NivelAcesso.php';

class NivelAcessoDAO {
    private $conn;
    private $dbQuery;

    public function __construct(){
        $this->dbQuery = new DBQuery(
            'nivel_acesso',
            'id_nivel, tipo',
            'id_nivel'
        );
        $this->conn = (new DBConnection())->getConn();
    }

    public function getAll(){
        $niveis = [];
        $dados = $this->dbQuery->select();
        foreach($dados as $nivel){
            $obj = new NivelAcesso();
            $obj->load($nivel['id_nivel'], $nivel['tipo']);
            $niveis[] = $obj;
        }
        return $niveis;
    }

    public function getById($id){
        $where = new Where();
        $where->addCondition('AND', 'id_nivel', '=', $id);
        $dados = $this->dbQuery->selectFiltered($where);
        if($dados){
            return new NivelAcesso(...array_values($dados[0]));
        }
        return null;
    }

    public function insert(NivelAcesso $nivel){
        $dados = [
            null,
            $nivel->getTipo()
        ];
        return $this->dbQuery->insert($dados);
    }

    public function update(NivelAcesso $nivel){
        $dados = [
            'id_nivel' => $nivel->getIdNivel(),
            'tipo'     => $nivel->getTipo()
        ];
        return $this->dbQuery->update($dados);
    }

    public function delete($id){
        try {
            $sql = "DELETE FROM nivel_acesso WHERE id_nivel = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new \Exception('Erro ao deletar nível de acesso: ' . $e->getMessage());
        }
    }
}
