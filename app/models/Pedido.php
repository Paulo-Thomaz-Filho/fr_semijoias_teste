<?php
// Em: app/models/Pedido.php
namespace app\models;

class Pedido {
    private $idPedido;
    private $produtoNome;
    private $clienteNome;
    private $preco;
    private $endereco;
    private $dataPedido;
    private $quantidade;
    private $status;
    private $descricao;

    // --- GETTERS ---
    public function getIdPedido() { return $this->idPedido; }
    public function getProdutoNome() { return $this->produtoNome; }
    public function getClienteNome() { return $this->clienteNome; }
    public function getPreco() { return $this->preco; }
    public function getEndereco() { return $this->endereco; }
    public function getDataPedido() { return $this->dataPedido; }
    public function getQuantidade() { return $this->quantidade; }
    public function getStatus() { return $this->status; }
    public function getDescricao() { return $this->descricao; }

    // --- SETTERS ---
    public function setIdPedido($id) { $this->idPedido = $id; }
    public function setProdutoNome($nome) { $this->produtoNome = $nome; }
    public function setClienteNome($nome) { $this->clienteNome = $nome; }
    public function setPreco($preco) { $this->preco = $preco; }
    public function setEndereco($end) { $this->endereco = $end; }
    public function setDataPedido($data) { $this->dataPedido = $data; }
    public function setQuantidade($qtd) { $this->quantidade = $qtd; }
    public function setStatus($status) { $this->status = $status; }
    public function setDescricao($desc) { $this->descricao = $desc; }

    public function toArray() {
        return [
            'idPedido'    => $this->idPedido,
            'produtoNome' => $this->produtoNome,
            'clienteNome' => $this->clienteNome,
            'preco'       => $this->preco,
            'endereco'    => $this->endereco,
            'dataPedido'  => $this->dataPedido,
            'quantidade'  => $this->quantidade,
            'status'      => $this->status,
            'descricao'   => $this->descricao
        ];
    }
}