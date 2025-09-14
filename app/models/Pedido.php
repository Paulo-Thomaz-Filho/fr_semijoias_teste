<?php

namespace App\Models;

/**
 * Modelo Puro (POPO) para a entidade Pedido.
 */
class Pedido
{
    private ?int $id_pedido = null;
    private int $id_usuario;
    private int $id_endereco_entrega;
    private float $valor_total;
    private string $status = 'processando';
    private ?string $data_pedido = null;

    // --- Getters ---
    public function getId(): ?int { return $this->id_pedido; }
    public function getIdUsuario(): int { return $this->id_usuario; }
    public function getIdEnderecoEntrega(): int { return $this->id_endereco_entrega; }
    public function getValorTotal(): float { return $this->valor_total; }
    public function getStatus(): string { return $this->status; }
    public function getDataPedido(): ?string { return $this->data_pedido; }

    // --- Setters ---
    public function setId(int $id): void { $this->id_pedido = $id; }
    public function setIdUsuario(int $id_usuario): void { $this->id_usuario = $id_usuario; }
    public function setIdEnderecoEntrega(int $id_endereco): void { $this->id_endereco_entrega = $id_endereco; }
    public function setValorTotal(float $valor): void { $this->valor_total = $valor; }
    public function setStatus(string $status): void { $this->status = $status; }
    public function setDataPedido(string $data): void { $this->data_pedido = $data; }
}
