<?php
namespace app\models;

use core\database\DBQuery;
use core\database\Where;

include_once __DIR__.'/../core/database/DBConnection.php';
include_once __DIR__.'/../core/database/DBQuery.php';
include_once __DIR__.'/../core/database/Where.php';

include_once __DIR__.'/Categoria.php';

class CategoriaDAO {
    private $dbQuery;

    public function __construct(){
        $this->dbQuery = new DBQuery(
            'categoria', 
            'IdCategoria, Nome', 
            'IdCategoria'
        );
    }

    public function getAll(){
        $categorias = [];
        $dados = $this->dbQuery->select();

        foreach($dados as $categoria){
            $categorias[] = new Categoria(...array_values($categoria));
        }

        return $categorias;
    }

    public function getById($id){
        $where = new Where();
        $where->addCondition('AND', 'IdCategoria', '=', $id);
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
            'IdCategoria' => $categoria->getIdCategoria(),
            'Nome'        => $categoria->getNome()
        ];
        return $this->dbQuery->update($dados);
    }

    public function delete($id){
        return $this->dbQuery->delete(['IdCategoria' => $id]);
    }
}
