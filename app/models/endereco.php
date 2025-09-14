<?php

namespace App\Models;

/**
 * Modelo Puro (POPO) para a entidade Endereco.
 */
class Endereco
{
    private ?int $id_endereco = null;
    private int $id_usuario;
    private string $rua;
    private string $numero;
    private ?string $complemento = null;
    private string $cidade;
    private string $estado;
    private string $cep;
    private ?string $nome_destinatario = null;

    // --- Getters ---
    public function getId(): ?int { return $this->id_endereco; }
    public function getIdUsuario(): int { return $this->id_usuario; }
    public function getRua(): string { return $this->rua; }
    public function getNumero(): string { return $this->numero; }
    public function getComplemento(): ?string { return $this->complemento; }
    public function getCidade(): string { return $this->cidade; }
    public function getEstado(): string { return $this->estado; }
    public function getCep(): string { return $this->cep; }
    public function getNomeDestinatario(): ?string { return $this->nome_destinatario; }

    // --- Setters ---
    public function setId(int $id): void { $this->id_endereco = $id; }
    public function setIdUsuario(int $id_usuario): void { $this->id_usuario = $id_usuario; }
    public function setRua(string $rua): void { $this->rua = trim(strip_tags($rua)); }
    public function setNumero(string $numero): void { $this->numero = trim(strip_tags($numero)); }
    public function setComplemento(?string $comp): void { $this->complemento = $comp ? trim(strip_tags($comp)) : null; }
    public function setCidade(string $cidade): void { $this->cidade = trim(strip_tags($cidade)); }
    public function setEstado(string $estado): void { $this->estado = trim(strip_tags($estado)); }
    public function setCep(string $cep): void { $this->cep = trim(strip_tags($cep)); }
    public function setNomeDestinatario(?string $nome): void { $this->nome_destinatario = $nome ? trim(strip_tags($nome)) : null; }
}
