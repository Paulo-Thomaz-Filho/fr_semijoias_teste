<?php

namespace app\models;

use core\database\DBConnection;
use core\database\DBQuery;
use core\database\Where;
use PDO;

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
            'id_produto, nome, descricao, preco, id_marca, id_categoria, id_promocao, imagem, estoque, disponivel',
            'id_produto'
        );
    }

    public function getAll(){
        $dados = $this->dbQuery->select();

        $produtos = [];
        foreach($dados as $row){
            $produto = new Produto();
            $produto->setIdProduto($row['id_produto']);
            $produto->setNome($row['nome']);
            $produto->setDescricao($row['descricao']);
            $produto->setPreco($row['preco']);
            $produto->setMarca($row['id_marca']);
            $produto->setCategoria($row['id_categoria']);
            $produto->setIdPromocao($row['id_promocao']);
            $produto->setImagem($row['imagem']);
            $produto->setEstoque($row['estoque']);
            $produto->setDisponivel($row['disponivel']);
            $produtos[] = $produto;
        }
        return $produtos;
    }

    public function getAllWithDetails(){
        $conn = (new \core\database\DBConnection())->getConn();
        
        // Query com JOIN para buscar nomes de marca e categoria
        // snake_case do DB → camelCase para o PHP/JSON
        $sql = "
            SELECT 
                p.id_produto as idProduto,
                p.nome as nome,
                p.descricao as descricao,
                p.preco as preco,
                m.nome as marca,
                c.nome as categoria,
                p.id_promocao as idPromocao,
                p.imagem as imagem,
                p.estoque as estoque,
                p.disponivel as disponivel
            FROM 
                produtos p
            LEFT JOIN
                marcas m ON p.id_marca = m.id_marca
            LEFT JOIN
                categorias c ON p.id_categoria = c.id_categoria
            ORDER BY
                p.nome
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
            $produto = new Produto();
            $produto->setIdProduto($row['id_produto']);
            $produto->setNome($row['nome']);
            $produto->setDescricao($row['descricao']);
            $produto->setPreco($row['preco']);
            $produto->setMarca($row['id_marca']);
            $produto->setCategoria($row['id_categoria']);
            $produto->setIdPromocao($row['id_promocao']);
            $produto->setImagem($row['imagem']);
            $produto->setEstoque($row['estoque']);
            $produto->setDisponivel($row['disponivel']);
            return $produto;
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
            'id_marca'        => $produto->getMarca(),
            'id_categoria'    => $produto->getCategoria(),
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
