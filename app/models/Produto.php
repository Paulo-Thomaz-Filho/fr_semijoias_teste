<?php
// Em: app/models/Produto.php
namespace app\models;

class Produto {
    private $IdProduto;
    private $nome;
    private $descricao;
    private $valor;
    private $marca;       
    private $categoria;   
    private $idPromocao;  
    private $status; // ADICIONADO

    public function __construct($IdProduto = null, $nome = null, $descricao = null, $valor = null, $marca = null, $categoria = null, $idPromocao = null, $status = 'ativo') {
        $this->IdProduto = $IdProduto;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->valor = $valor;
        $this->marca = $marca;
        $this->categoria = $categoria;
        $this->idPromocao = $idPromocao;
        $this->status = $status; // ADICIONADO
    }

    // --- Getters ---
    public function getIdProduto() { return $this->IdProduto; }
    public function getNome() { return $this->nome; }
    public function getDescricao() { return $this->descricao; }
    public function getValor() { return $this->valor; }
    public function getMarca() { return $this->marca; }           
    public function getCategoria() { return $this->categoria; }       
    public function getIdPromocao() { return $this->idPromocao; }    
    public function getStatus() { return $this->status; } // ADICIONADO

    public function toArray() {
        return [
            'IdProduto'   => $this->IdProduto,
            'nome'        => $this->nome,
            'descricao'   => $this->descricao,
            'valor'       => $this->valor,
            'marca'       => $this->marca,      
            'categoria'   => $this->categoria,  
            'idPromocao'  => $this->idPromocao,
            'status'      => $this->status // ADICIONADO
        ];
    }
}