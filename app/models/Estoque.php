<?php
namespace app\models;

class estoque {

    private $stoque_id;
	private $produto_id;
	private $quantidade;

    // Getters
	public function getstoque_id  () { return $this->stoque_id; }
	public function getproduto_id () { return $this->produto_id; }
	public function quantidade    () { return $this->quantidade; }

    // Setters
	public function setstoque_id  ($stoque_id)  { $this->stoque_id = $stoque_id; }
	public function setproduto_id ($produto_id) { $this->produto_id = $produto_id; }
	public function setquantidade ($quantidade) { $this->quantidade = $quantidade; }

    // Construtor
	public function __construct() {}

    public function load($stoque_id, $produto_id, $quantidade) {
		$this->setstoque_id($stoque_id),
		$this->setproduto_id($produto_id),
		$this->setquantidade($quantidade),
    }

    public function toArray() {
        return array(
            'stoque_id'  => $this->getstoque_id(),
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