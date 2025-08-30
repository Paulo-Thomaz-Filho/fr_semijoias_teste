<?php
namespace app\models;

class pedido {

	private $avalicao_id;
	private $usuario_id;
	private $prudoto_id;
	private $nota;
	private $comentario;
	private $data_avaliacao;

    // Getters
	public function getavalicao_id() { return $this->avalicao_id; }
	public function getusuario_id() { return $this->usuario_id; }
	public function getprudoto_id() { return $this->prudoto_id; }
	public function getnota() { return $this->nota; }
	public function getcomentario() { return $this->comentario; }
	public function getdata_avaliacao() { return $this->data_avaliacao; }

    
	// Setters
	public function setavalicao_id($avalicao_id) { $this->avalicao_id = $avalicao_id; }
	public function setusuario_id($usuario_id) { $this->usuario_id = $usuario_id; }
	public function setprudoto_id($prudoto_id) { $this->prudoto_id = $prudoto_id; }
	public function setnota($nota) { $this->nota = $nota; }
	public function setcomentario($comentario) { $this->comentario = $comentario; }
	public function setdata_avaliacao($data_avaliacao) { $this->data_avaliacao = $data_avaliacao; }

    // Construtor
	public function __construct() {}

    // Carrega valores
	public function load($avalicao_id, $usuario_id, $prudoto_id, $nota, $comentario, $data_avaliacao) {
		$this->setavalicao_id($avalicao_id);
		$this->setusuario_id($usuario_id);
		$this->setprudoto_id($prudoto_id);
		$this->setnota($nota);
		$this->setcomentario($comentario);
		$this->setdata_avaliacao($data_avaliacao);
    }

    public function toArray() {
        return array(
            'avalicao_id'        => $this->getavalicao_id(),
            'usuario_id'       => $this->getusuario_id(),
            'prudoto_id'      => $this->getprudoto_id(),
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