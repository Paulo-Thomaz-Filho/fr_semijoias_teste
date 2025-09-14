<?php

namespace App\Models;

/**
 * Modelo Puro (POPO) para a entidade Categoria.
 */
class Categoria
{
    private ?int $id_categoria = null;
    private string $nome;
    private ?string $descricao = null;

    // --- Getters ---
    public function getId(): ?int { return $this->id_categoria; }
    public function getNome(): string { return $this->nome; }
    public function getDescricao(): ?string { return $this->descricao; }

    // --- Setters ---
    public function setId(int $id): void { $this->id_categoria = $id; }
    public function setNome(string $nome): void { $this->nome = trim(strip_tags($nome)); }
    public function setDescricao(?string $desc): void { $this->descricao = $desc ? trim(strip_tags($desc)) : null; }
}
