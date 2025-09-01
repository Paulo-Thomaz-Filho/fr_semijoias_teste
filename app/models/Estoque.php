<?php
namespace app\models;

class Estoque {

  private $estoque_id;
	private $produto_id;
	private $quantidade;

    // Getters
	public function getestoque_id  () { return $this->estoque_id; }
	public function getproduto_id () { return $this->produto_id; }
	public function quantidade    () { return $this->quantidade; }

    // Setters
	public function setestoque_id  ($estoque_id)  { $this->estoque_id = $estoque_id; }
	public function setproduto_id ($produto_id) { $this->produto_id = $produto_id; }
	public function setquantidade ($quantidade) { $this->quantidade = $quantidade; }

    // Construtor
	public function __construct() {}

    public function load($estoque_id, $produto_id, $quantidade) {
		$this->setestoque_id($estoque_id),
		$this->setproduto_id($produto_id),
		$this->setquantidade($quantidade),
    }

    public function toArray() {
        return array(
            'estoque_id'  => $this->getestoque_id(),
            'produto_id' => $this->getproduto_id(),
            'quantidade' => $this->getquantidade(),
        );
    }

    // Retorna JSON
	public function arrayToJson() {
		return json_encode($this->toArray());
	}

}
?>