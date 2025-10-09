<?php
namespace app\models;

class ItemPedido {
    private $idItemPedido;
    private $pedidoId;
    private $produtoId;
    private $quantidade;
    private $valorUnitario;

    // --- Getters ---

    public function getIdItemPedido()  { return $this->idItemPedido; }
    public function getPedidoId()      { return $this->pedidoId; }
    public function getProdutoId()     { return $this->produtoId; }
    public function getQuantidade()    { return $this->quantidade; }
    public function getValorUnitario() { return $this->valorUnitario; }

    // --- Setters ---

    public function setIdItemPedido($idItemPedido)   { $this->idItemPedido = $idItemPedido; }
    public function setPedidoId($pedidoId)           { $this->pedidoId = $pedidoId; }
    public function setProdutoId($produtoId)         { $this->produtoId = $produtoId; }
    public function setQuantidade($quantidade)       { $this->quantidade = $quantidade; }
    public function setValorUnitario($valorUnitario) { $this->valorUnitario = $valorUnitario; }

    // --- Métodos Especiais ---

    // Carrega os dados do item do pedido
    public function load($idItemPedido, $pedidoId, $produtoId, $quantidade, $valorUnitario) {
        $this->setIdItemPedido($idItemPedido);
        $this->setPedidoId($pedidoId);
        $this->setProdutoId($produtoId);
        $this->setQuantidade($quantidade);
        $this->setValorUnitario($valorUnitario);
    }

    public function toArray() {
        return [
            'idItemPedido'  => $this->idItemPedido,
            'pedidoId'      => $this->pedidoId,
            'produtoId'     => $this->produtoId,
            'quantidade'    => $this->quantidade,
            'valorUnitario' => $this->valorUnitario
        ];
    }
}
