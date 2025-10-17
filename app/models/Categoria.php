<?php

namespace app\models;

class Categoria {
    private $idCategoria;
    private $nome;

    // --- Getters ---
    public function getIdCategoria() { return $this->idCategoria; }
    public function getNome()        { return $this->nome; }

    // --- Setters ---
    public function setIdCategoria($idCategoria) { $this->idCategoria = $idCategoria; }
    public function setNome($nome)               { $this->nome = $nome; }

    // --- Métodos Especiais ---

    // Carrega os dados da categoria
    public function load($idCategoria, $nome) {
        $this->setIdCategoria($idCategoria);
        $this->setNome($nome);
    }

    public function toArray() {
        return [
            'idCategoria' => $this->idCategoria,
            'nome'        => $this->nome
        ];
    }
}
