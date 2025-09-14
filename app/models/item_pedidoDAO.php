<?php

namespace App\Models;

use PDO;
use PDOException;

/**
 * DAO para a entidade ItemPedido.
 */
class ItemPedidoDAO
{
    private PDO $conexao;

    public function __construct(PDO $db)
    {
        $this->conexao = $db;
    }

    public function save(ItemPedido $item): bool
    {
        $query = "INSERT INTO Itens_Pedido (id_pedido, id_produto, quantidade, preco_unitario_na_compra) VALUES (:id_ped, :id_prod, :qtd, :preco)";
        
        try {
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':id_ped', $item->getIdPedido(), PDO::PARAM_INT);
            $stmt->bindValue(':id_prod', $item->getIdProduto(), PDO::PARAM_INT);
            $stmt->bindValue(':qtd', $item->getQuantidade(), PDO::PARAM_INT);
            $stmt->bindValue(':preco', $item->getPrecoUnitario());
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function findAllByPedidoId(int $id_pedido): array
    {
        $query = "SELECT i.*, p.nome AS nome_produto
                  FROM Itens_Pedido i
                  JOIN Produtos p ON i.id_produto = p.id_produto
                  WHERE i.id_pedido = :id_ped";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':id_ped', $id_pedido, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retorna array associativo
    }
}
