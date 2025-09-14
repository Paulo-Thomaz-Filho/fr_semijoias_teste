<?php

namespace App\Models;

use PDO;
use PDOException;

/**
 * DAO para a entidade Produto.
 */
class ProdutoDAO
{
    private PDO $conexao;

    public function __construct(PDO $db)
    {
        $this->conexao = $db;
    }

    public function save(Produto &$produto): bool
    {
        $query = $produto->getId() ?
            "UPDATE Produtos SET nome = :nome, descricao = :desc, preco = :preco, quantidade_estoque = :qtd, sku = :sku, id_categoria = :cat, id_promocao = :promo WHERE id_produto = :id" :
            "INSERT INTO Produtos (nome, descricao, preco, quantidade_estoque, sku, id_categoria, id_promocao) VALUES (:nome, :desc, :preco, :qtd, :sku, :cat, :promo)";

        try {
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':nome', $produto->getNome());
            $stmt->bindValue(':desc', $produto->getDescricao());
            $stmt->bindValue(':preco', $produto->getPreco());
            $stmt->bindValue(':qtd', $produto->getQuantidadeEstoque(), PDO::PARAM_INT);
            $stmt->bindValue(':sku', $produto->getSku());
            $stmt->bindValue(':cat', $produto->getIdCategoria(), PDO::PARAM_INT);
            // Lida com o caso de não haver promoção (ID nulo)
            $stmt->bindValue(':promo', $produto->getIdPromocao(), $produto->getIdPromocao() ? PDO::PARAM_INT : PDO::PARAM_NULL);

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
     * Busca todos os produtos com informações de categoria e promoção.
     * Retorna um array associativo para facilitar a listagem, sendo mais performático.
     */
    public function findAll(): array
    {
        $query = "SELECT p.*, c.nome AS nome_categoria, pr.nome AS nome_promocao
                  FROM Produtos p
                  JOIN Categorias c ON p.id_categoria = c.id_categoria
                  LEFT JOIN Promocoes pr ON p.id_promocao = pr.id_promocao
                  ORDER BY p.nome ASC";
        $stmt = $this->conexao->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        $produto->setSku($dados['sku']);
        $produto->setIdCategoria((int)$dados['id_categoria']);
        if (!empty($dados['id_promocao'])) {
            $produto->setIdPromocao((int)$dados['id_promocao']);
        }
        return $produto;
    }
}

