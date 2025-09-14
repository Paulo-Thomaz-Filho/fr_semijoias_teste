<?php

namespace App\Models;

/**
 * Modelo Puro (POPO) para a entidade Promocao.
 * Sua única responsabilidade é representar os dados de uma promoção.
 * Não possui conhecimento sobre o banco de dados.
 */
class Promocao
{
    // Propriedades
    private ?int $id_promocao = null;
    private string $nome;
    private string $tipo_desconto;
    private float $valor_desconto;
    private bool $ativo = true;
    private ?string $data_inicio = null;
    private ?string $data_fim = null;

    // --- Getters ---
    public function getId(): ?int
    {
        return $this->id_promocao;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getTipoDesconto(): string
    {
        return $this->tipo_desconto;
    }

    public function getValorDesconto(): float
    {
        return $this->valor_desconto;
    }

    public function isAtivo(): bool
    {
        return $this->ativo;
    }

    public function getDataInicio(): ?string
    {
        return $this->data_inicio;
    }

    public function getDataFim(): ?string
    {
        return $this->data_fim;
    }

    // --- Setters ---
    // O ID é definido internamente pelo DAO, então não há setter público para ele.
    public function setId(int $id): void
    {
        $this->id_promocao = $id;
    }
    
    public function setNome(string $nome): void
    {
        $this->nome = trim(strip_tags($nome));
    }

    public function setTipoDesconto(string $tipo): void
    {
        // Validação para garantir que o tipo seja um dos valores esperados
        if (in_array($tipo, ['percentual', 'fixo'])) {
            $this->tipo_desconto = $tipo;
        }
    }

    public function setValorDesconto(float $valor): void
    {
        $this->valor_desconto = $valor;
    }

    public function setAtivo(bool $ativo): void
    {
        $this->ativo = $ativo;
    }

    public function setDataInicio(?string $data): void
    {
        $this->data_inicio = $data;
    }

    public function setDataFim(?string $data): void
    {
        $this->data_fim = $data;
    }
}
