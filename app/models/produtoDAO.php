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
    private $conn;

    public function __construct(){
        $colunas = 'id_produto, nome, descricao, preco, id_marca, id_categoria, id_promocao, imagem, unidade_estoque, disponivel';
        
        $this->dbQuery = new DBQuery('produtos', $colunas, 'id_produto');
        $this->conn = (new \core\database\DBConnection())->getConn();
    }

    public function getAll(){
        $dados = $this->dbQuery->select();

        $produtos = [];
        foreach($dados as $item){
            $produtos[] = new Produto(...array_values($item));
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
                p.unidade_estoque as unidadeEstoque,
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
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getById($id){
        $where = new Where();
        $where->addCondition('AND', 'id_produto', '=', $id);
        $dados = $this->dbQuery->selectFiltered($where);

        if($dados){
            return new Produto(...array_values($dados[0]));
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
            null, // Imagem
            $produto->getUnidadeEstoque(),
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
            'imagem'          => null, // Mantém imagem atual
            'unidade_estoque' => $produto->getUnidadeEstoque(),
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
