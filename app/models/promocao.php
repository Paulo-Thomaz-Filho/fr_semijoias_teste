<?php
namespace app\models;

class Promocao {

    private $promocao_idpromocao_id;
    private $descricaodescricao;
    private $tipotipo;
    private $valorvalor;
    private $data_iniciodata_inicio;
    private $data_fimdata_fim;

    // Getters 
    public function getpromocao_id () { return $this->promocao_id; }
    public function getdescricao   () { return $this->descricao; }
    public function gettipo        () { return $this->tipo; }
    public function getvalor       () { return $this->valor; }
    public function getdata_inicio () { return $this->data_inicio; }
    public function gedata_fim     () { return $this->data_fim; }

    // Setters
    public function setpromocao_id ($promocao_id) { $this->promocao_id = $promocao_id; }
    public function setdescricao   ($descricao)    { $this->descricao = $descricao; }
    public function settipo        ($tipo)         { $this->tipo = $tipo; }
    public function setvalor       ($valor)        { $this->valor = $valor; }
    public function setdata_inicio ($data_inicio)  { $this->data_inicio = $data_inicio; }
    public function setdata_fim    ($data_fim)     { $this->data_fim = $data_fim; }

    // Construtor
	public function __construct() {}

    public function load($promocao_id, $descricao, $tipo, $valor, $data_inicio, $data_fim) {
        $this->setpromocao_id ($promocao_id);
        $this->setdescricao   ($descricao);
        $this->settipo        ($tipo);
        $this->setvalor       ($valor);
        $this->setdata_inicio ($data_inicio);
        $this->setdata_fim    ($data_fim);
    }

    public function toArray() {
        return array(
            'promocao_idpromocao_id' => $this->getpromocao_idpromocao_id (),
            'descricaodescricao'     => $this->getdescricaodescricao     (),
            'tipotipo'               => $this->gettipotipo               (),
            'valorvalor'             => $this->getvalorvalor             (),
            'data_iniciodata_inicio' => $this->getdata_iniciodata_inicio (),
            'data_fimdata_fim'       => $this->getdata_fimdata_fim       (),
        );
    }

    // Retorna JSON
	public function arrayToJson() {
		return json_encode($this->toArray());
	}

}
?>