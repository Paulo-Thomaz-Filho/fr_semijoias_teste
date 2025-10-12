<?php
// Em: app/models/ItemPedido.php
namespace app\models;

class ItemPedido {
    private $id_item_pedido;
    private $pedido_id;
    private $produto_id;
    private $quantidade;
    private $valor_unitario;
    private $nome_produto;

    // --- Getters ---
    public function get_id_item_pedido() { return $this->id_item_pedido; }
    public function get_pedido_id()      { return $this->pedido_id; }
    public function get_produto_id()     { return $this->produto_id; }
    public function get_quantidade()     { return $this->quantidade; }
    public function get_valor_unitario() { return $this->valor_unitario; }
    public function get_nome_produto()   { return $this->nome_produto; }

    // --- Setters ---
    public function set_id_item_pedido($id)    { $this->id_item_pedido = $id; }
    public function set_pedido_id($id)         { $this->pedido_id = $id; }
    public function set_produto_id($id)        { $this->produto_id = $id; }
    public function set_quantidade($qtd)       { $this->quantidade = $qtd; }
    public function set_valor_unitario($valor) { $this->valor_unitario = $valor; }
    public function set_nome_produto($nome)    { $this->nome_produto = $nome; }
    
    public function toArray() {
        return [
            'id_item_pedido' => $this->id_item_pedido,
            'pedido_id'      => $this->pedido_id,
            'produto_id'     => $this->produto_id,
            'quantidade'     => $this->quantidade,
            'valor_unitario' => $this->valor_unitario,
            'nome_produto'    => $this->nome_produto
        ];
    }
}