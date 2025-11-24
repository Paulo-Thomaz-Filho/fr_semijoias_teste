<?php

namespace app\models;

class Promocao {
    private $idPromocao;
    private $nome;
    private $dataInicio;
    private $dataFim;
    private $descricao;
    private $desconto;
    private $tipoDesconto;
    private $status;

    public function __construct($idPromocao = null, $nome = null, $dataInicio = null, $dataFim = null, $descricao = null, $desconto = null, $tipoDesconto = 'percentual', $status = null) {
        $this->idPromocao = $idPromocao;
        $this->nome = $nome;
        $this->dataInicio = $dataInicio;
        $this->dataFim = $dataFim;
        $this->descricao = $descricao;
        $this->desconto = $desconto;
        $this->tipoDesconto = $tipoDesconto;
        $this->status = $status;
    }

    // --- Getters ---
    public function getIdPromocao()   { return $this->idPromocao; }
    public function getNome()         { return $this->nome; }
    public function getDataInicio()   { return $this->dataInicio; }
    public function getDataFim()      { return $this->dataFim; }
    public function getDescricao()    { return $this->descricao; }
    public function getDesconto()     { return $this->desconto; }
    public function getTipoDesconto() { return $this->tipoDesconto; }
    public function getStatus()       { return $this->status; }

    // --- Setters ---
    public function setIdPromocao($idPromocao)     { $this->idPromocao = $idPromocao; }
    public function setNome($nome)                 { $this->nome = $nome; }
    public function setDataInicio($dataInicio)     { $this->dataInicio = $dataInicio; }
    public function setDataFim($dataFim)           { $this->dataFim = $dataFim; }
    public function setDescricao($descricao)       { $this->descricao = $descricao; }
    public function setDesconto($desconto)         { $this->desconto = $desconto; }
    public function setTipoDesconto($tipoDesconto) { $this->tipoDesconto = $tipoDesconto; }
    public function setStatus($status)             { $this->status = $status; }

    public function toArray() {
        return [
            'idPromocao'    => $this->idPromocao,
            'nome'          => $this->nome,
            'dataInicio'    => $this->dataInicio,
            'dataFim'       => $this->dataFim,
            'descricao'     => $this->descricao,
            'desconto'      => $this->desconto,
            'tipo_desconto' => $this->tipoDesconto,
            'status'        => $this->status
        ];
    }
}
