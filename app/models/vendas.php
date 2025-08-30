<?php
namespace app\models;

class promocao {

    private $venda_id;
    private $produto_id;
    private $quntidade;
    private $data_venda;

    // Getters 
    public function getvenda_id   () { return $this->venda_id; }
    public function getproduto_id () { return $this->produto_id; }
    public function getquntidade  () { return $this->quntidade; }
    public function getdata_venda () { return $this->data_venda; }

    // Setters
    public function setvenda_id   ($venda_id)   { $this->venda_id = $venda_id }
    public function setproduto_id ($produto_id) { $this->produto_id = $produto_id }
    public function setquntidade  ($quntidade)  { $this->quntidade = $quntidade }
    public function setdata_venda ($data_venda) { $this->data_venda = $data_venda }


    // Construtor
	public function __construct() {}

    public function load($venda_id, $produto_id, $quantidade, $data_venda) {
        $this->setvenda_id   ($venda_id),
        $this->setproduto_id ($produto_id),
        $this->setquntidade  ($quntidade),
        $this->setdata_venda ($data_venda),
    }

    public function toArray() {
        return array(
            'venda_id'   => $this->getvenda_id   (),
            'produto_id' => $this->getproduto_id (),
            'quntidade'  => $this->getquntidade  (),
            'data_venda' => $this->getdata_venda (),
        );
    }

    // Retorna JSON
	public function arrayToJson() {
		return json_encode($this->toArray());
	}

}
?>