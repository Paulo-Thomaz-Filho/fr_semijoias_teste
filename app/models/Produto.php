<?php
// Em: app/models/Produto.php
namespace app\models;

class Produto {
    private $idProduto;
    private $nome;
    private $descricao;
    private $preco;
    private $marca;       
    private $categoria;   
    private $idPromocao;
    private $imagem;
    private $unidadeEstoque;
    private $disponivel;

    public function __construct($idProduto = null, $nome = null, $descricao = null, $preco = null, $marca = null, $categoria = null, $idPromocao = null, $imagem = null, $unidadeEstoque = 0, $disponivel = 1) {
        $this->idProduto = $idProduto;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->preco = $preco;
        $this->marca = $marca;
        $this->categoria = $categoria;
        $this->idPromocao = $idPromocao;
        $this->imagem = $imagem;
        $this->unidadeEstoque = $unidadeEstoque;
        $this->disponivel = $disponivel;
    }

    // --- Getters ---
    public function getIdProduto() { return $this->idProduto; }
    public function getNome() { return $this->nome; }
    public function getDescricao() { return $this->descricao; }
    public function getPreco() { return $this->preco; }
    public function getMarca() { return $this->marca; }           
    public function getCategoria() { return $this->categoria; }       
    public function getIdPromocao() { return $this->idPromocao; }    
    public function getUnidadeEstoque() { return $this->unidadeEstoque; }
    public function getDisponivel() { return $this->disponivel; }

    public function toArray() {
        return [
            'idProduto'      => $this->idProduto,
            'nome'           => $this->nome,
            'descricao'      => $this->descricao,
            'preco'          => $this->preco,
            'marca'          => $this->marca,      
            'categoria'      => $this->categoria,  
            'idPromocao'     => $this->idPromocao,
            'unidadeEstoque' => $this->unidadeEstoque,
            'disponivel'     => $this->disponivel
        ];
    }
}