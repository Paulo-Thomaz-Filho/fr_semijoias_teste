<?php
namespace app\models;

class Produto {

	private $produto_id;
	private $nome;
	private $valor;
	private $marca_id;
	private $categoria_id;
	private $disponivel;
	private $imagem_url;
	private $unidade_estoque;
	private $promocao_id;
	private $descricao;

	// Getters
	public function getproduto_id      () { return $this->produto_id; }
	public function getnome            () { return $this->nome; }
	public function getvalor           () { return $this->valor; }
	public function getmarca_id        () { return $this->marca_id; }
	public function getcategoria_id    () { return $this->categoria_id; }
	public function getdisponivel      () { return $this->disponivel; }
	public function getimagem_url      () { return $this->imagem_url; }
	public function getunidade_estoque () { return $this->unidade_estoque; }
	public function getpromocao_id     () { return $this->promocao_id; }
	public function getdescricao       () { return $this->descricao; }

	// Setters
	public function setproduto_id      ($produto_id)      { $this->produto_id = $produto_id; }
	public function setnome            ($nome)            { $this->nome = $nome; }
	public function setvalor           ($valor)           { $this->valor = $valor; }
	public function setmarca_id        ($marca_id)        { $this->marca_id = $marca_id; }
	public function setcategoria_id    ($categoria_id)    { $this->categoria_id = $categoria_id; }
	public function setdisponivel      ($disponivel)      { $this->disponivel = $disponivel; }
	public function setimagem_url      ($imagem_url)      { $this->imagem_url = $imagem_url; }
	public function setunidade_estoque ($unidade_estoque) { $this->unidade_estoque = $unidade_estoque; }
	public function setpromocao_id     ($promocao_id)     { $this->promocao_id = $promocao_id; }
	public function setdescricao       ($descricao)       { $this->descricao = $descricao; }

	// Construtor
	public function __construct() {}

	// Carrega valores
	public function load($produto_id, $nome, $valor, $avaliacao, $marca_id, $categoria_id, $disponivel, $imagem_url, $unidade_estoque, $promocao_id) {
		$this->setproduto_id($produto_id);
		$this->setnome($nome);
		$this->setvalor($valor);
		$this->setmarca_id($marca_id);
		$this->setcategoria_id($categoria_id);
		$this->setdisponivel($disponivel);
		$this->setimagem_url($imagem_url);
		$this->setunidade_estoque($unidade_estoque);
		$this->setpromocao_id($promocao_id);
		$this->setdescricao($descricao);
	}	

	// Retorna como array
	public function toArray() {
		return array(
			'produto_id'      => $this->getproduto_id(),
			'nome'            => $this->getnome(),
			'valor'           => $this->getvalor(),
			'marca_id'        => $this->getmarca_id(),
			'categoria_id'    => $this->getcategoria_id(),
			'disponivel'      => $this->getdisponivel(),
			'imagem_url'      => $this->getimagem_url(),
			'unidade_estoque' => $this->getunidade_estoque(),
			'promocao_id'     => $this->getpromocao_id(),
			'descricao'       => $this->getdescricao()
		);
	}

	// Retorna JSON
	public function arrayToJson() {
		return json_encode($this->toArray());
	}

	// Exemplo de método (precisa de implementação de banco)
	public function checkAtivo($conn) {
		$produto_id = $_POST['produto_id'] ?? '';
		$disponivel = $_POST['disponivel'] ?? '';
		$unidade_estoque = $_POST['unidade_estoque'] ?? '';

		$stmt = $conn->prepare("SELECT * FROM loja.produto WHERE produto_id = ? AND disponivel = ? AND unidade_estoque = ?");
		$stmt->bind_param("sss", $produto_id, $disponivel, $unidade_estoque);
		$stmt->execute();
		$result = $stmt->get_result();

		return $result->num_rows > 0;
	}

}
?>
