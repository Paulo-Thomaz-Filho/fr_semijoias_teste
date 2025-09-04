<?php 
namespace app\models;

class Marca {

    private $marca_id;
	private $nome;
	private $descrição;

    // Getters
	public function getmarca_id  () { return $this->marca_id; }
	public function getnome      () { return $this->nome; }
	public function getdescrição () { return $this->descrição; }

    // Setters
	public function setmarca_id  ($marca_id)  { $this->marca_id = $marca_id; }
	public function setnome      ($nome)      { $this->nome = $nome; }
	public function setdescrição ($descrição) { $this->descrição = $descrição; }

    // Construtor
	public function __construct() {}

    public function load($marca_id, $nome, $descrição) {
		$this->setmarca_id($marca_id);
		$this->setnome($nome);
		$this->setdescrição($descrição);
    }

    public function toArray() {
        return array(
            'marca_id'  => $this->getmarca_id(),
            'nome'      => $this->getnome(),
            'descrição' => $this->getdescrição(),
        );
    }
    
}
?>