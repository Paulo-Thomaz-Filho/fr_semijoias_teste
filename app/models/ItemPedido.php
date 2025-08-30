<?php
namespace app\models;

class ItemPedido {

    private $itempedido_id;
	private $pedido;
	private $produto_id;
	private $quantidade;
	private $valor_unitario;

    // Getters
	public function getitempedido_id  () { return $this->itempedido_id; }
	public function getpedido         () { return $this->pedido; }
	public function getproduto_id     () { return $this->produto_id; }
	public function getquantidade     () { return $this->quantidade; }
	public function getvalor_unitario () { return $this->valor_unitario; }

    // Setters
	public function setitempedido_id  ($itempedido_id)  { $this->itempedido_id = $itempedido_id; }
	public function setpedido         ($pedido)         { $this->pedido = $pedido; }
	public function setproduto_id     ($produto_id)     { $this->produto_id = $produto_id; }
	public function setquantidade     ($quantidade)     { $this->quantidade = $quantidade; }
	public function setvalor_unitario ($valor_unitario) { $this->valor_unitario = $valor_unitario; }

    // Construtor
	public function __construct() {}

    public function load($itempedido_id, $pedido, $produto_id, $quantidade, $valor_unitario) {
		$this->setitempedido_id($itempedido_id),
		$this->setpedido($pedido),
		$this->setproduto_id($produto_id),
		$this->setquantidade($quantidade),
		$this->setvalor_unitario($valor_unitario),
    }

    public function toArray() {
        return array(
            'itempedido_id'  => $this->getitempedido_id(),
            'pedido'         => $this->getpedido(),
            'produto_id'     => $this->getproduto_id(),
            'quantidade'     => $this->getquantidade(),
            'valor_unitario' => $this->getvalor_unitario(),
        );
    }

    // Retorna JSON
	public function arrayToJson() {
		return json_encode($this->toArray());
	}
    
}
?>