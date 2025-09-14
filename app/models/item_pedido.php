<?php

namespace App\Models;

/**
 * Modelo Puro (POPO) para a entidade ItemPedido.
 */
class ItemPedido
{
    private int $id_pedido;
    private int $id_produto;
    private int $quantidade;
    private float $preco_unitario_na_compra;

    // --- Getters ---
    public function getIdPedido(): int { return $this->id_pedido; }
    public function getIdProduto(): int { return $this->id_produto; }
    public function getQuantidade(): int { return $this->quantidade; }
    public function getPrecoUnitario(): float { return $this->preco_unitario_na_compra; }

    // --- Setters ---
    public function setIdPedido(int $id_pedido): void { $this->id_pedido = $id_pedido; }
    public function setIdProduto(int $id_produto): void { $this->id_produto = $id_produto; }
    public function setQuantidade(int $qtd): void { $this->quantidade = $qtd; }
    public function setPrecoUnitario(float $preco): void { $this->preco_unitario_na_compra = $preco; }
}
