<?php

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
    private $estoque;
    private $disponivel;

    // --- Getters ---
    public function getIdProduto()  { return $this->idProduto; }
    public function getNome()       { return $this->nome; }
    public function getDescricao()  { return $this->descricao; }
    public function getPreco()      { return $this->preco; }
    public function getMarca()      { return $this->marca; }           
    public function getCategoria()  { return $this->categoria; }       
    public function getIdPromocao() { return $this->idPromocao; }
    public function getImagem()     { return $this->imagem; }
    public function getEstoque()    { return $this->estoque; }
    public function getDisponivel() { return $this->disponivel; }

    // --- Setters ---
    public function setIdProduto($idProduto)   { $this->idProduto = $idProduto; }
    public function setNome($nome)             { $this->nome = $nome; }
    public function setDescricao($descricao)   { $this->descricao = $descricao; }
    public function setPreco($preco)           { $this->preco = $preco; }
    public function setMarca($marca)           { $this->marca = $marca; }
    public function setCategoria($categoria)   { $this->categoria = $categoria; }
    public function setIdPromocao($idPromocao) { $this->idPromocao = $idPromocao; }
    public function setImagem($imagem)         { $this->imagem = $imagem; }
    public function setEstoque($estoque)       { $this->estoque = $estoque; }
    public function setDisponivel($disponivel) { $this->disponivel = $disponivel; }

    public function load($idProduto, $nome, $descricao, $preco, $marca, $categoria, $idPromocao, $imagem, $estoque, $disponivel) {
        $this->setIdProduto($idProduto);
        $this->setNome($nome);
        $this->setDescricao($descricao);
        $this->setPreco($preco);
        $this->setMarca($marca);
        $this->setCategoria($categoria);
        $this->setIdPromocao($idPromocao);
        $this->setImagem($imagem);
        $this->setEstoque($estoque);
        $this->setDisponivel($disponivel);
    }

    public function toArray() {
        return [
            'idProduto'      => $this->idProduto,
            'nome'           => $this->nome,
            'descricao'      => $this->descricao,
            'preco'          => $this->preco,
            'marca'          => $this->marca,      
            'categoria'      => $this->categoria,  
            'idPromocao'     => $this->idPromocao,
            'estoque'        => $this->estoque,
            'disponivel'     => $this->disponivel
        ];
    }
}