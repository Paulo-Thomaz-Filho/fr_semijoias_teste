<?php

namespace app\models;

use core\database\DBQuery;
use core\database\Where;

include_once '../core/database/DBConnection.php';
include_once '../core/database/DBQuery.php';
include_once '../core/database/Where.php';

include_once '../models/Usuario.php';

class UsuarioDAO {
	
	private $dbQuery;
	
	public function __construct(){
		$this->dbQuery = new DBQuery(
				'usuarios',                                                                                                     //nome da tabela
				'idUsuario, email, senha, idNivelUsuario, nome, cpf, endereco, bairro, cidade, uf, cep, telefone, foto, ativo', //campos
				'idUsuario'                                                                                                     //chave primaria
				);
	}
	
	public function getAll(){
		$usuarios = [];
		$dados = $this->dbQuery->select();
		foreach($dados as $linha){
			$tempUsuario = new Usuarios();
			$tempUsuario->load(...array_values($linha));
			$usuarios[] = $tempUsuario;
		}
		return $usuarios;
	}
	
	public function getById($idUsuario){
		$where = new Where();
		$where->addCondition('AND', 'idUsuario', '=', $idUsuario);
		$dados = $this->dbQuery->selectFiltered($where);
		if($dados){
			return new Usuarios(...array_values($dados[0]));
		}
		return null;
	}
	
	public function insert(Usuarios $usuario){
		$dados = [
				null,
				$usuario->getEmail(),
				$usuario->getSenha(),
				$usuario->getIdNivelUsuario(),
				$usuario->getNome(),
				$usuario->getCpf(),
				$usuario->getEndereco(),
				$usuario->getBairro(),
				$usuario->getCidade(),
				$usuario->getUf(),
				$usuario->getCep(),
				$usuario->getTelefone(),
				$usuario->getFoto(),
				$usuario->getAtivo()
		];
		return $this->dbQuery->insert($dados);
	}
	
	public function update(Usuarios $usuario){
		$dados = [
				'idUsuario'      => $usuario->getIdUsuario(),
				'email'          => $usuario->getEmail(),
				'senha'          => $usuario->getSenha(),
				'idNivelUsuario' => $usuario->getIdNivelUsuario(),
				'nome'           => $usuario->getNome(),
				'cpf'            => $usuario->getCpf(),
				'endereco'       => $usuario->getEndereco(),
				'bairro'         => $usuario->getBairro(),
				'cidade'         => $usuario->getCidade(),
				'uf'             => $usuario->getUf(),
				'cep'            => $usuario->getCep(),
				'telefone'       => $usuario->getTelefone(),
				'foto'           => $usuario->getFoto(),
				'ativo'          => $usuario->getAtivo()
		];
		return $this->dbQuery->update($dados);
	}
	
	public function delete($idUsuario){
		return $this->dbQuery->delete(['idUsuario' => $idUsuario]);
	}
}
?>