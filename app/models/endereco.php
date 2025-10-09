<?php
namespace app\models;

class Endereco {
    private $idEnderecos;
    private $usuarioId;
    private $cep;
    private $logradouro;
    private $numero;
    private $complemento;
    private $bairro;
    private $cidade;
    private $estado;

    // --- Getters ---

    public function getIdEnderecos() { return $this->idEnderecos; }
    public function getUsuarioId()   { return $this->usuarioId; }
    public function getCep()         { return $this->cep; }
    public function getLogradouro()  { return $this->logradouro; }
    public function getNumero()      { return $this->numero; }
    public function getComplemento() { return $this->complemento; }
    public function getBairro()      { return $this->bairro; }
    public function getCidade()      { return $this->cidade; }
    public function getEstado()      { return $this->estado; }

    // --- Setters ---

    public function setIdEnderecos($idEnderecos) { $this->idEnderecos = $idEnderecos; }
    public function setUsuarioId($usuarioId)     { $this->usuarioId = $usuarioId; }
    public function setCep($cep)                 { $this->cep = $cep; }
    public function setLogradouro($logradouro)   { $this->logradouro = $logradouro; }
    public function setNumero($numero)           { $this->numero = $numero; }
    public function setComplemento($complemento) { $this->complemento = $complemento; }
    public function setBairro($bairro)           { $this->bairro = $bairro; }
    public function setCidade($cidade)           { $this->cidade = $cidade; }
    public function setEstado($estado)           { $this->estado = $estado; }

    // --- Métodos Especiais ---

    // Carrega os dados do endereço
    public function load($idEnderecos, $usuarioId, $cep, $logradouro, $numero, $complemento, $bairro, $cidade, $estado) {
        $this->setIdEnderecos($idEnderecos);
        $this->setUsuarioId($usuarioId);
        $this->setCep($cep);
        $this->setLogradouro($logradouro);
        $this->setNumero($numero);
        $this->setComplemento($complemento);
        $this->setBairro($bairro);
        $this->setCidade($cidade);
        $this->setEstado($estado);
    }

    // Converte o objeto para um array associativo
    public function toArray() {
        return [
            'idEnderecos' => $this->idEnderecos,
            'usuarioId'   => $this->usuarioId,
            'cep'         => $this->cep,
            'logradouro'  => $this->logradouro,
            'numero'      => $this->numero,
            'complemento' => $this->complemento,
            'bairro'      => $this->bairro,
            'cidade'      => $this->cidade,
            'estado'      => $this->estado
        ];
    }
}
