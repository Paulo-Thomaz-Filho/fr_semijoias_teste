<?php

namespace app\models;

class NivelAcesso {
    private $idNivel;
    private $tipo;

    public function __construct($idNivel = null, $tipo = null) {
        $this->idNivel = $idNivel;
        $this->tipo = $tipo;
    }

    // Getters
    public function getIdNivel() { return $this->idNivel; }
    public function getTipo()    { return $this->tipo; }

    // Setters
    public function setIdNivel($idNivel) { $this->idNivel = $idNivel; }
    public function setTipo($tipo)       { $this->tipo = $tipo; }

    public function toArray() {
      return [
        'idNivel' => $this->idNivel,
        'tipo'    => $this->tipo
      ];
    }
}
