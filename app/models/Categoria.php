<?php
namespace app\models;

class Categoria {

  private $categoria_id;
	private $nome;
	private $descricao;

    // Getters
	public function getcategoria_id  () { return $this->categoria_id; }
	public function getnome          () { return $this->nome; }
	public function descricao        () { return $this->descricao; }

    // Setters
	public function setcategoria_id  ($categoria_id) { $this->categoria_id = $categoria_id; }
	public function setnome          ($nome)         { $this->nome = $nome; }
	public function setdescricao     ($descricao)    { $this->descricao = $descricao; }

    // Construtor
	public function __construct() {}

    public function load($categoria_id, $nome, $descricao) {
		$this->setcategoria_id($categoria_id),
		$this->setnome($nome),
		$this->setdescricao($descricao),
    }

    public function toArray() {
        return array(
            'categoria_id'  => $this->getcategoria_id(),
            'nome'          => $this->getnome(),
            'descricao'     => $this->getdescricao(),
        );
    }
    
    // Retorna JSON
	public function arrayToJson() {
		return json_encode($this->toArray());
	}

}
?>