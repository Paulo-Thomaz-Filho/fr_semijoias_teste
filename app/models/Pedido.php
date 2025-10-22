<?php

namespace app\models;

class Pedido {
    private $idPedido;
    private $produtoNome;
    private $idCliente;
    private $preco;
    private $endereco;
    private $dataPedido;
    private $quantidade;
    private $idStatus;
    private $descricao;

    public function __construct($idPedido = null, $produtoNome = null, $idCliente = null, $preco = null, $endereco = null, $dataPedido = null, $quantidade = null, $idStatus = null, $descricao = null) {
        $this->idPedido = $idPedido;
        $this->produtoNome = $produtoNome;
        $this->idCliente = $idCliente;
        $this->preco = $preco;
        $this->endereco = $endereco;
        $this->dataPedido = $dataPedido;
        $this->quantidade = $quantidade;
        $this->idStatus = $idStatus;
        $this->descricao = $descricao;
    }

    // --- Getters ---
    public function getIdPedido()    { return $this->idPedido; }
    public function getProdutoNome() { return $this->produtoNome; }
    public function getIdCliente()   { return $this->idCliente; }
    public function getPreco()       { return $this->preco; }
    public function getEndereco()    { return $this->endereco; }
    public function getDataPedido()  { return $this->dataPedido; }
    public function getQuantidade()  { return $this->quantidade; }
    public function getIdStatus()    { return $this->idStatus; }
    public function getDescricao()   { return $this->descricao; }

    // --- Setters ---
    public function setIdPedido($id)       { $this->idPedido = $id; }
    public function setProdutoNome($nome)  { $this->produtoNome = $nome; }
    public function setIdCliente($id)      { $this->idCliente = $id; }
    public function setPreco($preco)       {  $this->preco = $preco; }
    public function setEndereco($end)      { $this->endereco = $end; }
    public function setDataPedido($data)   { $this->dataPedido = $data; }
    public function setQuantidade($qtd)    { $this->quantidade = $qtd; }
    public function setIdStatus($idStatus) { $this->idStatus = $idStatus; }
    public function setDescricao($desc)    { $this->descricao = $desc; }

    public function toArray() {
        return [
            'idPedido'    => $this->idPedido,
            'produtoNome' => $this->produtoNome,
            'idCliente'   => $this->idCliente,
            'preco'       => $this->preco,
            'endereco'    => $this->endereco,
            'dataPedido'  => $this->dataPedido,
            'quantidade'  => $this->quantidade,
            'idStatus'    => $this->idStatus,
            'descricao'   => $this->descricao
        ];
    }
}
