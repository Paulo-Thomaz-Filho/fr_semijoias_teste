<?php

namespace app\models;

class Promocao {
    private $idPromocao;
    private $nome;
    private $dataInicio;
    private $dataFim;
    private $tipo;
    private $valor;
    private $status;

    // --- Getters ---
    public function getIdPromocao() { return $this->idPromocao; }
    public function getNome()       { return $this->nome; }
    public function getDataInicio() { return $this->dataInicio; }
    public function getDataFim()    { return $this->dataFim; }
    public function getTipo()       { return $this->tipo; }
    public function getValor()      { return $this->valor; }
    public function getStatus()     { return $this->status; }

    // --- Setters ---
    public function setIdPromocao($idPromocao) { $this->idPromocao = $idPromocao; }
    public function setNome($nome)             { $this->nome = $nome; }
    public function setDataInicio($dataInicio) { $this->dataInicio = $dataInicio; }
    public function setDataFim($dataFim)       { $this->dataFim = $dataFim; }
    public function setTipo($tipo)             { $this->tipo = $tipo; }
    public function setValor($valor)           { $this->valor = $valor; }
    public function setStatus($status)         { $this->status = $status; }

    // --- Métodos Especiais ---

    // O método load PRECISA ser atualizado para receber o status do banco de dados
    public function load($idPromocao, $nome, $dataInicio, $dataFim, $tipo, $valor, $status) {
        $this->setIdPromocao($idPromocao);
        $this->setNome($nome);
        $this->setDataInicio($dataInicio);
        $this->setDataFim($dataFim);
        $this->setTipo($tipo);
        $this->setValor($valor);
        $this->setStatus($status);
    }

    public function toArray() {
        return [
            'idPromocao' => $this->idPromocao,
            'nome'       => $this->nome,
            'dataInicio' => $this->dataInicio,
            'dataFim'    => $this->dataFim,
            'tipo'       => $this->tipo,
            'valor'      => $this->valor,
            'status'     => $this->status
        ];
    }
}
