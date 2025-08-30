<?php
namespace app\models;

class pedido {

	private $pedido_id;
	private $usuario_id;
	private $data_pedido;
	private $status;
	private $valor_total;
	private $endereco_entrega;

    // Getters
	public function getpedido_id        () { return $this->pedido_id; }
	public function getusuario_id       () { return $this->usuario_id; }
	public function getdata_pedido      () { return $this->data_pedido; }
	public function getstatus           () { return $this->status; }
	public function getvalor_total      () { return $this->valor_total; }
	public function getendereco_entrega () { return $this->endereco_entrega; }

    
	// Setters
	public function setpedido_id        ($pedido_id)        { $this->pedido_id = $pedido_id; }
	public function setusuario_id       ($usuario_id)       { $this->usuario_id = $usuario_id; }
	public function setdata_pedido      ($data_pedido)      { $this->data_pedido = $data_pedido; }
	public function setstatus           ($status)           { $this->status = $status; }
	public function setvalor_total      ($valor_total)      { $this->valor_total = $valor_total; }
	public function setendereco_entrega ($endereco_entrega) { $this->endereco_entrega = $endereco_entrega; }

    // Construtor
	public function __construct() {}

    // Carrega valores
	public function load($pedido_id, $usuario_id, $data_pedido, $status, $valor_total, $endereco_entrega) {
		$this->setpedido_id($pedido_id);
		$this->setusuario_id($usuario_id);
		$this->setdata_pedido($data_pedido);
		$this->setstatus($status);
		$this->setvalor_total($valor_total);
		$this->setendereco_entrega($endereco_entrega);
    }

    public function toArray() {
        return array(
            'pedido_id'        => $this->getpedido_id(),
            'usuario_id'       => $this->getusuario_id(),
            'data_pedido'      => $this->getdata_pedido(),
            'status'           => $this->getstatus(),
            'valor_total'      => $this->getvalor_total(),
            'endereco_entrega' => $this->getendereco_entrega(),
        );
    }

    // Retorna JSON
	public function arrayToJson() {
		return json_encode($this->toArray());
	}

    	// Exemplo de método (precisa de implementação de banco)
	public function checkPedido($conn) {
		$pedido_id      = $_POST['pedido_id'] ?? '';
		$usuario_id     = $_POST['usuario_id'] ?? '';
		$status_entrega = $_POST['status'] ?? '';

		$stmt = $conn->prepare("SELECT * FROM loja.pedido WHERE pedido_id = ? AND usuario_id = ? AND status_entrega = ?");
		$stmt->bind_param("sss", $pedido_id, $usuario_id, $status_entrega);
		$stmt->execute();
		$result = $stmt->get_result();

		return $result->num_rows > 0;
	}

}
?>