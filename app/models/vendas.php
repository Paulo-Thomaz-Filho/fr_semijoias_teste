<?php
namespace app\models;

class Vendas {

    private $venda_id;
    private $produto_id;
    private $quantidade;
    private $data_venda;

    // Getters 
    public function getvenda_id   () { return $this->venda_id; }
    public function getproduto_id () { return $this->produto_id; }
    public function getquantidade  () { return $this->quantidade; }
    public function getdata_venda () { return $this->data_venda; }

    // Setters
    public function setvenda_id   ($venda_id)   { $this->venda_id = $venda_id }
    public function setproduto_id ($produto_id) { $this->produto_id = $produto_id }
    public function setquantidade  ($quantidade)  { $this->quantidade = $quantidade }
    public function setdata_venda ($data_venda) { $this->data_venda = $data_venda }


    // Construtor
	public function __construct() {}

    public function load($venda_id, $produto_id, $quantidade, $data_venda) {
        $this->setvenda_id   ($venda_id),
        $this->setproduto_id ($produto_id),
        $this->setquantidade  ($quantidade),
        $this->setdata_venda ($data_venda),
    }

    public function toArray() {
        return array(
            'venda_id'   => $this->getvenda_id   (),
            'produto_id' => $this->getproduto_id (),
            'quantidade'  => $this->getquantidade  (),
            'data_venda' => $this->getdata_venda (),
        );
    }

    // Retorna JSON
	public function arrayToJson() {
		return json_encode($this->toArray());
	}

}
?>