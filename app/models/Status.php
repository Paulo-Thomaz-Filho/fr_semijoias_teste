<?php

namespace app\models;

class Status {
    private $idStatus;
    private $nome;

    public function __construct($idStatus = null, $nome = null) {
        $this->idStatus = $idStatus;
        $this->nome = $nome;
    }

    // Getters
    public function getIdStatus() { return $this->idStatus; }
    public function getNome()     { return $this->nome; }

    // Setters
    public function setIdStatus($idStatus) { $this->idStatus = $idStatus; }
    public function setNome($nome)         { $this->nome = $nome; }

    public function toArray() {
        return [
            'idStatus' => $this->idStatus,
            'nome' => $this->nome
        ];
    }
}
