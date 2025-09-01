<?php
namespace app\models;

class Pedido {

	private $avaliacao_id;
	private $usuario_id;
	private $produto_id;
	private $nota;
	private $comentario;
	private $data_avaliacao;

    // Getters
	public function getavaliacao_id() { return $this->avaliacao_id; }
	public function getusuario_id() { return $this->usuario_id; }
	public function getproduto_id() { return $this->produto_id; }
	public function getnota() { return $this->nota; }
	public function getcomentario() { return $this->comentario; }
	public function getdata_avaliacao() { return $this->data_avaliacao; }

    
	// Setters
	public function setavaliacao_id($avaliacao_id) { $this->avaliacao_id = $avaliacao_id; }
	public function setusuario_id($usuario_id) { $this->usuario_id = $usuario_id; }
	public function setproduto_id($produto_id) { $this->produto_id = $produto_id; }
	public function setnota($nota) { $this->nota = $nota; }
	public function setcomentario($comentario) { $this->comentario = $comentario; }
	public function setdata_avaliacao($data_avaliacao) { $this->data_avaliacao = $data_avaliacao; }

    // Construtor
	public function __construct() {}

    // Carrega valores
	public function load($avaliacao_id, $usuario_id, $produto_id, $nota, $comentario, $data_avaliacao) {
		$this->setavaliacao_id($avaliacao_id);
		$this->setusuario_id($usuario_id);
		$this->setproduto_id($produto_id);
		$this->setnota($nota);
		$this->setcomentario($comentario);
		$this->setdata_avaliacao($data_avaliacao);
    }

    public function toArray() {
        return array(
            'avaliacao_id'        => $this->getavaliacao_id(),
            'usuario_id'       => $this->getusuario_id(),
            'produto_id'      => $this->getproduto_id(),
            'nota'           => $this->getnota(),
            'comentario'      => $this->getcomentario(),
            'data_avaliacao' => $this->getdata_avaliacao(),
        );
    }

    // Retorna JSON
	public function arrayToJson() {
		return json_encode($this->toArray());
	}
	
}
?>