<?php

namespace App\Models;

use PDO;
use PDOException;

/**
 * DAO para a entidade Endereco.
 */
class EnderecoDAO
{
    private PDO $conexao;

    public function __construct(PDO $db)
    {
        $this->conexao = $db;
    }

    public function save(Endereco &$endereco): bool
    {
        $query = $endereco->getId() ?
            "UPDATE Enderecos SET id_usuario = :id_user, rua = :rua, numero = :num, complemento = :comp, cidade = :cid, estado = :est, cep = :cep, nome_destinatario = :dest WHERE id_endereco = :id" :
            "INSERT INTO Enderecos (id_usuario, rua, numero, complemento, cidade, estado, cep, nome_destinatario) VALUES (:id_user, :rua, :num, :comp, :cid, :est, :cep, :dest)";
        
        try {
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':id_user', $endereco->getIdUsuario(), PDO::PARAM_INT);
            $stmt->bindValue(':rua', $endereco->getRua());
            $stmt->bindValue(':num', $endereco->getNumero());
            $stmt->bindValue(':comp', $endereco->getComplemento());
            $stmt->bindValue(':cid', $endereco->getCidade());
            $stmt->bindValue(':est', $endereco->getEstado());
            $stmt->bindValue(':cep', $endereco->getCep());
            $stmt->bindValue(':dest', $endereco->getNomeDestinatario());

            if ($endereco->getId()) {
                $stmt->bindValue(':id', $endereco->getId(), PDO::PARAM_INT);
            }

            $stmt->execute();
            
            if (!$endereco->getId()) {
                $endereco->setId((int)$this->conexao->lastInsertId());
            }
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function findById(int $id): ?Endereco
    {
        $query = "SELECT * FROM Enderecos WHERE id_endereco = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        return $dados ? $this->hydrate($dados) : null;
    }

    public function findAllByUserId(int $id_usuario): array
    {
        $query = "SELECT * FROM Enderecos WHERE id_usuario = :id_user";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':id_user', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        $lista = [];
        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lista[] = $this->hydrate($dados);
        }
        return $lista;
    }

    public function delete(int $id): bool
    {
        $query = "DELETE FROM Enderecos WHERE id_endereco = :id";
        try {
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    private function hydrate(array $dados): Endereco
    {
        $endereco = new Endereco();
        $endereco->setId((int)$dados['id_endereco']);
        $endereco->setIdUsuario((int)$dados['id_usuario']);
        $endereco->setRua($dados['rua']);
        $endereco->setNumero($dados['numero']);
        $endereco->setComplemento($dados['complemento']);
        $endereco->setCidade($dados['cidade']);
        $endereco->setEstado($dados['estado']);
        $endereco->setCep($dados['cep']);
        $endereco->setNomeDestinatario($dados['nome_destinatario']);
        return $endereco;
    }
}
