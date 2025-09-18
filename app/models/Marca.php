<?php

namespace App\Models;

class Marca 
{
    private ?int $marca_id = null;
    private string $nome;

    public function getMarcaId(): ?int {return $this->marca_id;}

    public function getNome(): string {return $this->nome;}

    public function setMarcaId(int $marca_id): void {$this->marca_id = $marca_id;}

    public function setNome(string $nome): void {$this->nome = $nome;}
}