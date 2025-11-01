<?php
// Em: app/models/Produto.php
namespace app\models;

class Produto {
    private $id_produto;
    private $nome;
    private $descricao;
    private $preco;
    private $marca;       
    private $categoria;   
    private $id_promocao;  
    private $caminho_imagem;
    private $estoque;
    private $disponivel;

    // --- Getters ---
    public function get_id_produto() { return $this->id_produto; }
    public function get_nome() { return $this->nome; }
    public function get_descricao() { return $this->descricao; }
    public function get_preco() { return $this->preco; }
    public function get_marca() { return $this->marca; }           
    public function get_categoria() { return $this->categoria; }       
    public function get_id_promocao() { return $this->id_promocao; }    
    public function get_caminho_imagem() { return $this->caminho_imagem; }
    public function get_estoque() { return $this->estoque; }
    public function get_disponivel() { return $this->disponivel; }

    // --- Setters ---
    public function set_id_produto($id) { $this->id_produto = $id; }
    public function set_nome($nome) { $this->nome = $nome; }
    public function set_descricao($descricao) { $this->descricao = $descricao; }
    public function set_preco($preco) { $this->preco = $preco; }
    public function set_marca($marca) { $this->marca = $marca; }
    public function set_categoria($categoria) { $this->categoria = $categoria; }
    public function set_id_promocao($id) { $this->id_promocao = $id; }
    public function set_caminho_imagem($path) { $this->caminho_imagem = $path; }
    public function set_estoque($e) { $this->estoque = $e; }
    public function set_disponivel($d) { $this->disponivel = $d; }


    public function toArray() {
        return [
            'id_produto'     => $this->id_produto,
            'nome'           => $this->nome,
            'descricao'      => $this->descricao,
            'preco'          => $this->preco,
            'marca'          => $this->marca,      
            'categoria'      => $this->categoria,  
            'id_promocao'    => $this->id_promocao,
            'caminho_imagem' => $this->caminho_imagem,
            'estoque'        => $this->estoque,
            'disponivel'     => $this->disponivel
        ];
    }
}