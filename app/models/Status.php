<?php

namespace app\models;

class Status {
    private $id_status;
    private $nome;

    public function __construct($id_status = null, $nome = null) {
        $this->id_status = $id_status;
        $this->nome = $nome;
    }

    // Getters
    public function getIdStatus() { return $this->id_status; }
    public function getNome()     { return $this->nome; }

    // Setters
    public function setIdStatus($id_status) { $this->id_status = $id_status; }
    public function setNome($nome)          { $this->nome = $nome; }

    public function toArray() {
        return [
            'id_status' => $this->id_status,
            'nome' => $this->nome
        ];
    }
}
