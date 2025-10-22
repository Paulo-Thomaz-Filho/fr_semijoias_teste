<?php

namespace app\models;

class Usuario {
    private $idUsuario;
    private $nome;
    private $email;
    private $senha;
    private $telefone;
    private $cpf;
    private $endereco;
    private $dataNascimento;
    private $idNivel;

    public function __construct($idUsuario = null, $nome = null, $email = null, $senha = null, $telefone = null, $cpf = null, $endereco = null, $dataNascimento = null, $idNivel = null) {
        $this->idUsuario = $idUsuario;
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
        $this->telefone = $telefone;
        $this->cpf = $cpf;
        $this->endereco = $endereco;
        $this->dataNascimento = $dataNascimento;
        $this->idNivel = $idNivel;
    }

    // --- Getters ---
    public function getIdUsuario()      { return $this->idUsuario; }
    public function getNome()           { return $this->nome; }
    public function getEmail()          { return $this->email; }
    public function getSenha()          { return $this->senha; }
    public function getTelefone()       { return $this->telefone; }
    public function getCpf()            { return $this->cpf; }
    public function getEndereco()       { return $this->endereco; }
    public function getDataNascimento() { return $this->dataNascimento; }
    public function getIdNivel()        { return $this->idNivel; }

    // --- Setters ---
    public function setIdUsuario($id)                  { $this->idUsuario = $id; }
    public function setNome($nome)                     { $this->nome = $nome; }
    public function setEmail($email)                   { $this->email = $email; }
    public function setSenha($senha)                   { $this->senha = $senha; }
    public function setTelefone($telefone)             { $this->telefone = $telefone; }
    public function setCpf($cpf)                       { $this->cpf = $cpf; }
    public function setEndereco($endereco)             { $this->endereco = $endereco; }
    public function setDataNascimento($dataNascimento) { $this->dataNascimento = $dataNascimento; }
    public function setIdNivel($idNivel)               { $this->idNivel = $idNivel; }

    // Retorna o tipo de acesso do usuário
    public function getAcesso() {
        if ($this->idNivel == 1) {
            return 'Administrador';
        } elseif ($this->idNivel == 2) {
            return 'Cliente';
        }
        return '';
    }
    // Verifica se a senha informada confere com a senha do usuário
    public function verificarSenha($senhaInformada) {
        // Senha no banco está em md5
        return md5($senhaInformada) === $this->senha;
    }

    public function toArray() {
        return [
            'idUsuario'      => $this->idUsuario,
            'nome'           => $this->nome,
            'email'          => $this->email,
            'senha'          => $this->senha,
            'telefone'       => $this->telefone,
            'cpf'            => $this->cpf,
            'endereco'       => $this->endereco,
            'dataNascimento' => $this->dataNascimento,
            'idNivel'        => $this->idNivel
        ];
    }
}
