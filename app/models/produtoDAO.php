<?php

namespace app\models;

use core\database\DBConnection;
use core\database\DBQuery;
use core\database\Where;

include_once __DIR__.'/../core/database/DBConnection.php';
include_once __DIR__.'/../core/database/DBQuery.php';
include_once __DIR__.'/../core/database/Where.php';
include_once __DIR__.'/Produto.php';

class ProdutoDAO {
    private $dbQuery;
    private $conn;

    public function __construct(){
        $this->conn = (new DBConnection())->getConn();
        $this->dbQuery = new DBQuery(
            'produtos',
            'id_produto, nome, descricao, preco, marca, categoria, id_promocao, imagem, estoque, disponivel',
            'id_produto'
        );
    }

    public function getAll(){
        $dados = $this->dbQuery->select();

        $produtos = [];
        foreach($dados as $row){
            $produto = new Produto(
                $row['id_produto'],
                $row['nome'],
                $row['descricao'],
                $row['preco'],
                $row['marca'],
                $row['categoria'],
                $row['id_promocao'],
                $row['imagem'],
                $row['estoque'],
                $row['disponivel']
            );
            $produtos[] = $produto;
        }
        return $produtos;
    }

    public function getAllWithDetails(){
        $conn = (new \core\database\DBConnection())->getConn();
        
        $sql = "
            SELECT 
                id_produto as idProduto,
                nome,
                descricao,
                preco,
                marca,
                categoria,
                id_promocao as idPromocao,
                imagem,
                estoque,
                disponivel
            FROM produtos
            ORDER BY nome
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id){
        $where = new Where();
        $where->addCondition('AND', 'id_produto', '=', $id);
        $dados = $this->dbQuery->selectFiltered($where);

        if($dados){
            $row = $dados[0];
            return new Produto(
                $row['id_produto'],
                $row['nome'],
                $row['descricao'],
                $row['preco'],
                $row['marca'],
                $row['categoria'],
                $row['id_promocao'],
                $row['imagem'],
                $row['estoque'],
                $row['disponivel']
            );
        }

        return null;
    }

    public function insert(Produto $produto){
        $dados = [
            null, // IdProduto (auto increment)
            $produto->getNome(),
            $produto->getDescricao(),
            $produto->getPreco(),
            $produto->getMarca(),
            $produto->getCategoria(),
            $produto->getIdPromocao(),
            null,
            $produto->getEstoque(),
            $produto->getDisponivel()
        ];
        return $this->dbQuery->insert($dados);
    }

    public function update(Produto $produto){
        // Preparamos o array de dados com os nomes corretos das colunas (snake_case do DB)
        $dados = [
            'id_produto'      => $produto->getIdProduto(), 
            'nome'            => $produto->getNome(),
            'descricao'       => $produto->getDescricao(),
            'preco'           => $produto->getPreco(),
            'marca'           => $produto->getMarca(),
            'categoria'       => $produto->getCategoria(),
            'id_promocao'     => $produto->getIdPromocao(),
            'imagem'          => null,
            'estoque'         => $produto->getEstoque(),
            'disponivel'      => $produto->getDisponivel()
        ];
        return $this->dbQuery->update($dados);
    }

    public function delete($id){
        try {
            $sql = "DELETE FROM produtos WHERE id_produto = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            throw new \Exception('Erro ao deletar produto: ' . $e->getMessage());
        }
    }
}
