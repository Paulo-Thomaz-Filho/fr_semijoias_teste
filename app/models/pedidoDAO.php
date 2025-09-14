<?php

namespace App\Models;

use PDO;
use PDOException;

/**
 * DAO para a entidade Pedido.
 */
class PedidoDAO
{
    private PDO $conexao;

    public function __construct(PDO $db)
    {
        $this->conexao = $db;
    }

    public function save(Pedido &$pedido): bool
    {
        $query = $pedido->getId() ?
            "UPDATE Pedidos SET id_usuario = :id_user, id_endereco_entrega = :id_end, valor_total = :total, status = :status WHERE id_pedido = :id" :
            "INSERT INTO Pedidos (id_usuario, id_endereco_entrega, valor_total, status) VALUES (:id_user, :id_end, :total, :status)";

        try {
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':id_user', $pedido->getIdUsuario(), PDO::PARAM_INT);
            $stmt->bindValue(':id_end', $pedido->getIdEnderecoEntrega(), PDO::PARAM_INT);
            $stmt->bindValue(':total', $pedido->getValorTotal());
            $stmt->bindValue(':status', $pedido->getStatus());

            if ($pedido->getId()) {
                $stmt->bindValue(':id', $pedido->getId(), PDO::PARAM_INT);
            }
            
            $stmt->execute();

            if (!$pedido->getId()) {
                $pedido->setId((int)$this->conexao->lastInsertId());
            }
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function findById(int $id): ?Pedido
    {
        $query = "SELECT * FROM Pedidos WHERE id_pedido = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        return $dados ? $this->hydrate($dados) : null;
    }

    public function findAllByUserId(int $id_usuario): array
    {
        $query = "SELECT * FROM Pedidos WHERE id_usuario = :id_user ORDER BY data_pedido DESC";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':id_user', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        $listaPedidos = [];
        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $listaPedidos[] = $this->hydrate($dados);
        }
        return $listaPedidos;
    }

    private function hydrate(array $dados): Pedido
    {
        $pedido = new Pedido();
        $pedido->setId((int)$dados['id_pedido']);
        $pedido->setIdUsuario((int)$dados['id_usuario']);
        $pedido->setIdEnderecoEntrega((int)$dados['id_endereco_entrega']);
        $pedido->setValorTotal((float)$dados['valor_total']);
        $pedido->setStatus($dados['status']);
        $pedido->setDataPedido($dados['data_pedido']);
        return $pedido;
    }
}
