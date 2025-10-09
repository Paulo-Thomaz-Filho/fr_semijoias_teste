<?php
// Em: app/models/Usuario.php

namespace app\models;

class Usuario {
 	private $id;
	private $nome;
	private $email;
	private $senhaHash;
	private $acesso;
	private $status; 

	// --- Getters ---

	public function getId()        { return $this->id; }
	public function getNome()      { return $this->nome; }
	public function getEmail()     { return $this->email; }
	public function getSenhaHash() { return $this->senhaHash; }
	public function getAcesso()    { return $this->acesso; }
	public function getStatus()    { return $this->status; } 

	// --- Setters ---

	public function setId($id)           { $this->id = $id; }
	public function setNome($nome)       { $this->nome = $nome; }
	public function setEmail($email)     { $this->email = $email; }
	public function setAcesso($acesso)   { $this->acesso = $acesso; }
	public function setSenha($senhaPura) { if (!empty($senhaPura)) {$this->senhaHash = password_hash($senhaPura, PASSWORD_DEFAULT);}}
	public function setSenhaHash($hash)  { $this->senhaHash = $hash; }
	public function setStatus($status)   { $this->status = $status; } 

	// --- Métodos Especiais ---

    public function verificarSenha($senhaPura) {
        return password_verify($senhaPura, $this->senhaHash);
    }

	public function load($id, $nome, $email, $senhaHash, $acesso, $status) {
		$this->setId($id);
		$this->setNome($nome);
		$this->setEmail($email);
		$this->setSenhaHash($senhaHash);
		$this->setAcesso($acesso);
		$this->setStatus($status);
	}
	
	public function toArray() {
		return [
			'id'     => $this->id,
			'nome'   => $this->nome,
			'email'  => $this->email,
			'acesso' => $this->acesso,
			'status' => $this->status
		];
	}
}