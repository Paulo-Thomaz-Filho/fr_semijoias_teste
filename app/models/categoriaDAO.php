<?php

namespace App\Models;

use PDO;
use PDOException;

/**
 * DAO para a entidade Categoria.
 */
class CategoriaDAO
{
    private PDO $conexao;

    public function __construct(PDO $db)
    {
        $this->conexao = $db;
    }

    public function save(Categoria &$categoria): bool
    {
        $query = $categoria->getId() ?
            "UPDATE Categorias SET nome = :nome, descricao = :desc WHERE id_categoria = :id" :
            "INSERT INTO Categorias (nome, descricao) VALUES (:nome, :desc)";

        try {
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':nome', $categoria->getNome());
            $stmt->bindValue(':desc', $categoria->getDescricao());
            
            if ($categoria->getId()) {
                $stmt->bindValue(':id', $categoria->getId(), PDO::PARAM_INT);
            }

            $stmt->execute();

            if (!$categoria->getId()) {
                $categoria->setId((int)$this->conexao->lastInsertId());
            }
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function findById(int $id): ?Categoria
    {
        $query = "SELECT * FROM Categorias WHERE id_categoria = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        return $dados ? $this->hydrate($dados) : null;
    }

    public function findAll(): array
    {
        $query = "SELECT * FROM Categorias ORDER BY nome ASC";
        $stmt = $this->conexao->query($query);
        
        $lista = [];
        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lista[] = $this->hydrate($dados);
        }
        return $lista;
    }

    public function delete(int $id): bool
    {
        $query = "DELETE FROM Categorias WHERE id_categoria = :id";
        try {
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    private function hydrate(array $dados): Categoria
    {
        $categoria = new Categoria();
        $categoria->setId((int)$dados['id_categoria']);
        $categoria->setNome($dados['nome']);
        $categoria->setDescricao($dados['descricao']);
        return $categoria;
    }
}
