<?php

namespace app\models;

class NivelAcesso {
    private $idNivel;
    private $tipo;

    // Getters
    public function getIdNivel() { return $this->idNivel; }
    public function getTipo()    { return $this->tipo; }

    // Setters
    public function setIdNivel($idNivel) { $this->idNivel = $idNivel; }
    public function setTipo($tipo)       { $this->tipo = $tipo; }

    // Carrega os dados do nível de acesso
    public function load($idNivel, $tipo) {
      $this->setIdNivel($idNivel);
      $this->setTipo($tipo);
    }

    public function toArray() {
      return [
        'idNivel' => $this->idNivel,
        'tipo'    => $this->tipo
      ];
    }
}
