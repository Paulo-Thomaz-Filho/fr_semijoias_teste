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
        $colunas = 'id_produto, nome, descricao, preco, marca, categoria, id_promocao, caminho_imagem, estoque, disponivel';
        
        $this->dbQuery = new DBQuery(
            'produtos',
            $colunas,
            'id_produto'
        );
    }

    public function getAll(){
        $dados = $this->dbQuery->select();
        $produtos = [];
        foreach($dados as $row){
            // Criando o objeto com setters para maior clareza
            $produto = new Produto();
            $produto->setIdProduto($row['id_produto']);
            $produto->setNome($row['nome']);
            $produto->setDescricao($row['descricao']);
            $produto->setPreco($row['preco']);
            $produto->setMarca($row['marca']);
            $produto->setCategoria($row['categoria']);
            $produto->setIdPromocao($row['id_promocao']);
            $produto->setCaminhoImagem($row['caminho_imagem']);
            $produto->setEstoque($row['estoque']);
            $produto->setDisponivel($row['disponivel']);
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
            $produto = new Produto();
            $produto->setIdProduto($row['id_produto']);
            $produto->setNome($row['nome']);
            $produto->setDescricao($row['descricao']);
            $produto->setPreco($row['preco']);
            $produto->setMarca($row['marca']);
            $produto->setCategoria($row['categoria']);
            $produto->setIdPromocao($row['id_promocao']);
            $produto->setCaminhoImagem($row['caminho_imagem']);
            $produto->setEstoque($row['estoque']);
            $produto->setDisponivel($row['disponivel']);
            return $produto;
        }
        return null;
    }

    public function insert(Produto $produto){
        // A classe DBQuery->insert espera um array numérico na ordem das colunas do construtor
        $dados = [
            null, // id_produto
            $produto->getNome(),
            $produto->getDescricao(),
            $produto->getPreco(),
            $produto->getMarca(),
            $produto->getCategoria(),
            $produto->getIdPromocao(),
            $produto->getCaminhoImagem(),
            $produto->getEstoque(),
            $produto->getDisponivel()
        ];
        return $this->dbQuery->insert($dados);
    }

    public function update(Produto $produto){
        // A classe DBQuery->update espera um array associativo
        $dados = [
            'id_produto'      => $produto->getIdProduto(), 
            'nome'            => $produto->getNome(),
            'descricao'       => $produto->getDescricao(),
            'preco'           => $produto->getPreco(),
            'marca'           => $produto->getMarca(),
            'categoria'       => $produto->getCategoria(),
            'id_promocao'     => $produto->getIdPromocao(),
            'caminho_imagem'  => $produto->getCaminhoImagem(), 
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
