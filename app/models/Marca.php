<?php
namespace app\models;

class Marca {
    private $idMarca;
    private $nome;

    // --- Getters ---

    public function getIdMarca() { return $this->idMarca; }
    public function getNome()    { return $this->nome; }

    // --- Setters ---

    public function setIdMarca($idMarca) { $this->idMarca = $idMarca; }
    public function setNome($nome)       { $this->nome = $nome; }
    
    // --- Métodos Especiais ---

    // Carrega os dados da marca
    public function load($idMarca, $nome) {
        $this->setIdMarca($idMarca);
        $this->setNome($nome);
    }

    public function toArray() {
        return [
            'idMarca' => $this->idMarca,
            'nome'    => $this->nome
        ];
    }
}
