<?php

namespace App\Models;

use PDO;
use PDOException;

class ProdutoDAO
{
    private PDO $conexao;

    public function __construct(PDO $db)
    {
        $this->conexao = $db;
    }
    
    // O método save já lida com INSERT e UPDATE, então não precisa de alteração.
    public function save(Produto &$produto): bool
    {
        $query = $produto->getId() ?
            "UPDATE Produtos SET nome = :nome, descricao = :desc, preco = :preco, quantidade_estoque = :qtd, id_categoria = :cat, id_marca = :marca WHERE id_produto = :id" :
            "INSERT INTO Produtos (nome, descricao, preco, quantidade_estoque, id_categoria, id_marca) VALUES (:nome, :desc, :preco, :qtd, :cat, :marca,)";

        try {
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':nome', $produto->getNome());
            $stmt->bindValue(':desc', $produto->getDescricao());
            $stmt->bindValue(':preco', $produto->getPreco());
            $stmt->bindValue(':qtd', $produto->getQuantidadeEstoque(), PDO::PARAM_INT);
            $stmt->bindValue(':cat', $produto->getIdCategoria(), PDO::PARAM_INT);
            $stmt->bindValue(':marca', $produto->getIdMarca(), PDO::PARAM_INT);

            if ($produto->getId()) {
                $stmt->bindValue(':id', $produto->getId(), PDO::PARAM_INT);
            }
            
            $stmt->execute();

            if (!$produto->getId()) {
                $produto->setId((int)$this->conexao->lastInsertId());
            }
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function findById(int $id): ?Produto
    {
        $query = "SELECT * FROM Produtos WHERE id_produto = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        return $dados ? $this->hydrate($dados) : null;
    }

    /**
     * ATUALIZADO: Busca todos os produtos com JOIN para nomes de categoria e marca.
     */
    public function findAll(): array
    {
        $query = "SELECT 
                    p.*, 
                    c.nome AS nome_categoria, 
                    m.nome AS nome_marca
                  FROM Produtos p
                  LEFT JOIN Categorias c ON p.id_categoria = c.id_categoria
                  LEFT JOIN Marcas m ON p.id_marca = m.id_marca
                  ORDER BY p.id_produto ASC";
        
        try {
            $stmt = $this->conexao->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Em vez de retornar um array vazio, lança a exceção para que o Router a capture
            // e retorne uma mensagem de erro JSON clara para o front-end.
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        $query = "DELETE FROM Produtos WHERE id_produto = :id";
        try {
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    private function hydrate(array $dados): Produto
    {
        $produto = new Produto();
        $produto->setId((int)$dados['id_produto']);
        $produto->setNome($dados['nome']);
        $produto->setDescricao($dados['descricao']);
        $produto->setPreco((float)$dados['preco']);
        $produto->setQuantidadeEstoque((int)$dados['quantidade_estoque']);
        $produto->setIdCategoria((int)$dados['id_categoria']);
        if (!empty($dados['id_marca'])) {
            $produto->setIdMarca((int)$dados['id_marca']);
        }

        return $produto;
    }
}