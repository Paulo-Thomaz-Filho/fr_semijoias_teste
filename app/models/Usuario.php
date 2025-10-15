<?php
// Em: app/models/Usuario.php

namespace app\models;

class Usuario {
	private $idUsuario;
	private $nome;
	private $email;
	private $senhaHash;
	private $acesso;
	private $status; 

	// --- Getters ---

	public function getIdUsuario() { return $this->idUsuario; }
	public function getNome()      { return $this->nome; }
	public function getEmail()     { return $this->email; }
	public function getSenhaHash() { return $this->senhaHash; }
	public function getAcesso()    { return $this->acesso; }
	public function getStatus()    { return $this->status; } 

	// --- Setters ---

	public function setIdUsuario($id) { $this->idUsuario = $id; }
	public function setNome($nome)    { $this->nome = $nome; }
	public function setEmail($email)     { $this->email = $email; }
	public function setAcesso($acesso)   { $this->acesso = $acesso; }
	public function setSenha($senhaPura) { if (!empty($senhaPura)) {$this->senhaHash = password_hash($senhaPura, PASSWORD_DEFAULT);}}
	public function setSenhaHash($hash)  { $this->senhaHash = $hash; }
	public function setStatus($status)   { $this->status = $status; } 

	// --- Métodos Especiais ---

    public function verificarSenha($senhaPura) {
        // Usando MD5 para simplicidade (não recomendado em produção)
        return md5($senhaPura) === $this->senhaHash;
    }

	public function load($id, $nome, $email, $senha, $telefone, $cpf, $dataNascimento, $idNivel) {
		$this->setIdUsuario($id);
		$this->setNome($nome);
		$this->setEmail($email);
		$this->setSenhaHash($senha);
		// Telefone, CPF e DataNascimento podem ser adicionados como propriedades futuras se necessário
		$this->setAcesso($idNivel == 1 ? 'admin' : 'user');
		$this->setStatus('ativo');
	}
	
	public function toArray() {
		return [
			'idUsuario' => $this->idUsuario,
			'nome'      => $this->nome,
			'email'     => $this->email,
			'acesso'    => $this->acesso,
			'status'    => $this->status
		];
	}
}