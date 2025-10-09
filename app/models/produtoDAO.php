<?php
namespace app\models;

use core\database\DBQuery;
use core\database\Where;

include_once __DIR__.'/../core/database/DBConnection.php';
include_once __DIR__.'/../core/database/DBQuery.php';
include_once __DIR__.'/../core/database/Where.php';

include_once __DIR__.'/Produto.php';

class ProdutoDAO {
    private $dbQuery;

    public function __construct(){
        $colunas = 'IdProduto, nome, descricao, valor, marca, categoria, idPromocao';
        
        $this->dbQuery = new DBQuery('produtos', $colunas, 'IdProduto');
    }

    public function getAll(){
        $colunas_com_id = 'IdProduto, nome, descricao, valor, marca, categoria, idPromocao, status';
        $queryGetAll = new DBQuery('produtos', $colunas_com_id, 'IdProduto');
        
        $where = new Where();
        $where->addCondition('AND', 'status', '=', 'ativo');
        $dados = $queryGetAll->selectFiltered($where);

        $produtos = [];
        foreach($dados as $item){
            $produtos[] = new Produto(...array_values($item));
        }
        return $produtos;
    }


    public function getById($id){
        $where = new Where();
        $where->addCondition('AND', 'IdProduto', '=', $id);
        $dados = $this->dbQuery->selectFiltered($where);

        if($dados){
            return new Produto(...array_values($dados[0]));
        }

        return null;
    }

    public function insert(Produto $produto){
        $dados = [
            
            'nome' => $produto->getNome(),
            'descricao' => $produto->getDescricao(),
            'valor' => $produto->getValor(),
            'marca' => $produto->getMarca(),        
            'categoria' => $produto->getCategoria(),  
            'idPromocao' => $produto->getIdPromocao()  
        ];
        return $this->dbQuery->insert($dados);
    }

    public function update(Produto $produto){
        // Preparamos o array de dados
        $dados = [
            'IdProduto'  => $produto->getIdProduto(), 
            'nome'       => $produto->getNome(),
            'descricao'  => $produto->getDescricao(),
            'valor'      => $produto->getValor(),
            'marca'      => $produto->getMarca(),
            'categoria'  => $produto->getCategoria(),
            'idPromocao' => $produto->getIdPromocao(),
            'status'     => $produto->getStatus()
        ];
        
        // CORREÇÃO: Chamamos o update passando apenas UM argumento ($dados),
        // como a classe DBQuery espera.
        return $this->dbQuery->update($dados);
    }

    public function desativar($id){
        // CORREÇÃO: Ajustamos este método para seguir as regras da DBQuery.
        
        // 1. A DBQuery precisa saber quais colunas estão envolvidas: a que vamos mudar (status)
        //    e a que vamos usar para encontrar o registro (IdProduto).
        $queryDesativar = new DBQuery('produtos', 'IdProduto, status', 'IdProduto');

        // 2. O array de dados DEVE conter o valor da chave primária.
        $dados = [
            'IdProduto' => $id,
            'status' => 'inativo'
        ];

        // 3. Chamamos o update passando o único array com todos os dados necessários.
        return $queryDesativar->update($dados);
    }

}
