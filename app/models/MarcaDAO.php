<?php

namespace App\Models;

use PDO;
use PDOException;

class MarcaDAO 
{
    private PDO $conexao;

    public function __construct(PDO $db) 
    {
        $this->conexao = $db;
    }

    public function save(Marca &$marca): bool 
    {
        $query = $marca->getMarcaId() ?
            "UPDATE marcas SET nome = :nome WHERE marca_id = :id" :
            "INSERT INTO marcas (nome) VALUES (:nome)";

        try {
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':nome', $marca->getNome());

            if ($marca->getMarcaId()) {
                $stmt->bindValue(':id', $marca->getMarcaId(), PDO::PARAM_INT);
            }
            
            $stmt->execute();

            if (!$marca->getMarcaId()) {
                $marca->setMarcaId((int)$this->conexao->lastInsertId());
            }

            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function findById(int $id): ?Marca 
    {
        $query = "SELECT * FROM marcas WHERE marca_id = :id";
        
        try {
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $dados = $stmt->fetch(PDO::FETCH_ASSOC);
            return $dados ? $this->hydrate($dados) : null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function findAll(): array 
    {
        $query = "SELECT * FROM marcas ORDER BY nome ASC";
        
        try {
            $stmt = $this->conexao->query($query);
            $listaMarcas = [];
            
            while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $listaMarcas[] = $this->hydrate($dados);
            }
            
            return $listaMarcas;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function delete(int $id): bool
    {
        $query = "DELETE FROM marcas WHERE marca_id = :id";
        try {
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    private function hydrate(array $dados): Marca 
    {
        $marca = new Marca();
        $marca->setMarcaId((int)$dados['marca_id']);
        $marca->setNome($dados['nome']);
        return $marca;
    }
}