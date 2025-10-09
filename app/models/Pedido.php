<?php
// Em: app/models/Pedido.php
namespace app\models;

class Pedido {
    private $idPedido;
    private $usuario_id;
    private $endereco_id;
    private $valor_total;
    private $status;
    private $data_pedido;
    
    // Propriedade extra para guardar o nome do cliente (vindo do JOIN)
    private $nome_cliente;

    // Setters são úteis para preencher o objeto
    public function setIdPedido($id) { $this->idPedido = $id; }
    public function setUsuarioId($id) { $this->usuario_id = $id; }
    public function setEnderecoId($id) { $this->endereco_id = $id; }
    public function setValorTotal($valor) { $this->valor_total = $valor; }
    public function setStatus($status) { $this->status = $status; }
    public function setDataPedido($data) { $this->data_pedido = $data; }
    public function setNomeCliente($nome) { $this->nome_cliente = $nome; }

    public function toArray() {
        return [
            'idPedido'     => $this->idPedido,
            'usuario_id'   => $this->usuario_id,
            'endereco_id'  => $this->endereco_id,
            'valor_total'  => $this->valor_total,
            'status'       => $this->status,
            'data_pedido'  => $this->data_pedido,
            'nome_cliente' => $this->nome_cliente // Garante que o nome do cliente seja enviado no JSON
        ];
    }
}