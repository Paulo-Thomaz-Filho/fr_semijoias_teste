<?php
namespace app\models;

class Usuarios {

	private $idUsuario;
	private $email;
	private $senha;
	private $idNivelUsuario;
	private $nome;
	private $cpf;
	private $endereco;
	private $bairro;
	private $cidade;
	private $uf;
	private $cep;
	private $telefone;
	private $foto;
	private $ativo;

	// Getters
	public function getIdUsuario      () { return $this->idUsuario; }
	public function getEmail          () { return $this->email; }
	public function getSenha          () { return $this->senha; }
	public function getIdNivelUsuario () { return $this->idNivelUsuario; }
	public function getNome           () { return $this->nome; }
	public function getCpf            () { return $this->cpf; }
	public function getEndereco       () { return $this->endereco; }
	public function getBairro         () { return $this->bairro; }
	public function getCidade         () { return $this->cidade; }
	public function getUf             () { return $this->uf; }
	public function getCep            () { return $this->cep; }
	public function getTelefone       () { return $this->telefone; }
	public function getFoto           () { return $this->foto; }
	public function getAtivo          () { return $this->ativo; }

	// Setters
	public function setIdUsuario      ($idUsuario)      { $this->idUsuario = $idUsuario; }
	public function setEmail          ($email)          { $this->email = $email; }
	public function setSenha          ($senha)          { $this->senha = $senha; }
	public function setIdNivelUsuario ($idNivelUsuario) { $this->idNivelUsuario = $idNivelUsuario; }
	public function setNome           ($nome)           { $this->nome = $nome; }
	public function setCpf            ($cpf)            { $this->cpf = $cpf; }
	public function setEndereco       ($endereco)       { $this->endereco = $endereco; }
	public function setBairro         ($bairro)         { $this->bairro = $bairro; }
	public function setCidade         ($cidade)         { $this->cidade = $cidade; }
	public function setUf             ($uf)             { $this->uf = $uf; }
	public function setCep            ($cep)            { $this->cep = $cep; }
	public function setTelefone       ($telefone)       { $this->telefone = $telefone; }
	public function setFoto           ($foto)           { $this->foto = $foto; }
	public function setAtivo          ($ativo)          { $this->ativo = $ativo; }

	// Construtor
	public function __construct() {}

	// Carrega os dados de usuário
	public function load($idUsuario, $email, $senha, $idNivelUsuario, $nome, $cpf, $endereco, $bairro, $cidade, $uf, $cep, $telefone, $foto, $ativo) {
		$this->setIdUsuario($idUsuario);
		$this->setEmail($email);
		$this->setSenha($senha);
		$this->setIdNivelUsuario($idNivelUsuario);
		$this->setNome($nome);
		$this->setCpf($cpf);
		$this->setEndereco($endereco);
		$this->setBairro($bairro);
		$this->setCidade($cidade);
		$this->setUf($uf);
		$this->setCep($cep);
		$this->setTelefone($telefone);
		$this->setFoto($foto);
		$this->setAtivo($ativo);
	}

	// Retorna como array
	public function toArray() {
		return array(
			'idUsuario'      => $this->getIdUsuario(),
			'email'          => $this->getEmail(),
			'senha'          => $this->getSenha(),
			'idNivelUsuario' => $this->getIdNivelUsuario(),
			'nome'           => $this->getNome(),
			'cpf'            => $this->getCpf(),
			'endereco'       => $this->getEndereco(),
			'bairro'         => $this->getBairro(),
			'cidade'         => $this->getCidade(),
			'uf'             => $this->getUf(),
			'cep'            => $this->getCep(),
			'telefone'       => $this->getTelefone(),
			'foto'           => $this->getFoto(),
			'ativo'          => $this->getAtivo()
		);
	}

	// Retorna JSON
	public function arrayToJson() {
		return json_encode($this->toArray());
	}

	// Verifica login
	public function checkLogin($conn) {
		if (empty($_POST['email']) || empty($_POST['senha'])) {
			return false;
		}

		$email = trim($_POST['email']);
		$senha = $_POST['senha'];

		$stmt = $conn->prepare("SELECT * FROM lojinha.usuarios WHERE email = ?");
		if (!$stmt) {
			error_log("Erro na preparação da consulta: " . $conn->error);
			return false;
		}

		$stmt->bind_param("s", $email);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($usuario = $result->fetch_assoc()) {
			if (password_verify($senha, $usuario['senha'])) {
				return true;
			}
		}
		return false;
	}
}
?>