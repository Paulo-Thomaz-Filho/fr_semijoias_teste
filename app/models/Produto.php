<?php

namespace App\Models;

/**
 * Modelo Puro (POPO) para a entidade Produto.
 * Representa os dados de um produto.
 */
class Produto
{
    private ?int $id_produto = null;
    private string $nome;
    private ?string $descricao = null;
    private float $preco;
    private int $quantidade_estoque = 0;
    private ?string $sku = null;
    private int $id_categoria;
    private ?int $id_promocao = null;

    // --- Getters ---
    public function getId(): ?int { return $this->id_produto; }
    public function getNome(): string { return $this->nome; }
    public function getDescricao(): ?string { return $this->descricao; }
    public function getPreco(): float { return $this->preco; }
    public function getQuantidadeEstoque(): int { return $this->quantidade_estoque; }
    public function getSku(): ?string { return $this->sku; }
    public function getIdCategoria(): int { return $this->id_categoria; }
    public function getIdPromocao(): ?int { return $this->id_promocao; }

    // --- Setters ---
    public function setId(int $id): void { $this->id_produto = $id; }
    public function setNome(string $nome): void { $this->nome = trim(strip_tags($nome)); }
    public function setDescricao(?string $descricao): void { $this->descricao = $descricao ? trim(strip_tags($descricao)) : null; }
    public function setPreco(float $preco): void { $this->preco = $preco; }
    public function setQuantidadeEstoque(int $qtd): void { $this->quantidade_estoque = $qtd; }
    public function setSku(?string $sku): void { $this->sku = $sku ? trim(strip_tags($sku)) : null; }
    public function setIdCategoria(int $id_categoria): void { $this->id_categoria = $id_categoria; }
    public function setIdPromocao(?int $id_promocao): void { $this->id_promocao = $id_promocao; }
}
