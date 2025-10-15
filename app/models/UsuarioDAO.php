<?php
namespace app\models;

use core\database\DBQuery;
use core\database\Where;

include_once __DIR__.'/../core/database/DBConnection.php';
include_once __DIR__.'/../core/database/DBQuery.php';
include_once __DIR__.'/../core/database/Where.php';

include_once __DIR__.'/Usuario.php';

class UsuarioDAO {
	private $dbQuery;
	
	public function __construct(){
    	$this->dbQuery = new DBQuery('usuarios', 'id_usuario, nome, email, senha, telefone, cpf, data_nascimento, id_nivel', 'id_usuario');
	}

	
	public function getAll(){
		$usuarios = [];

		// Removido filtro de status pois a coluna não existe na tabela
		$dados = $this->dbQuery->select();

		foreach($dados as $dadosDoUsuario){
			$usuario = new Usuario();
			$usuario->load(...array_values($dadosDoUsuario));
			$usuarios[] = $usuario;
		}
		return $usuarios;
	}
	
	public function getById($id){
		$where = new Where();
		$where->addCondition('AND', 'id_usuario', '=', $id);
		$dados = $this->dbQuery->selectFiltered($where);

		if($dados){
			$usuario = new Usuario();
			$usuario->load(...array_values($dados[0]));
			return $usuario;
		}

		return null;
	}

	public function getTotalCadastrados() {
		$conn = (new \core\database\DBConnection())->getConn();
		$stmt = $conn->prepare("SELECT COUNT(*) as total FROM usuarios");
		$stmt->execute();
		$result = $stmt->fetch(\PDO::FETCH_ASSOC);
		return $result['total'] ?? 0;
	}
	
	public function getByEmail($email){
		$where = new Where();
		$where->addCondition('AND', 'email', '=', $email);
		$dados = $this->dbQuery->selectFiltered($where);

		if($dados){

			$usuario = new Usuario();
			$usuario->load(...array_values($dados[0]));
			return $usuario;
		}

		return null;
	}
	
	public function insert(Usuario $usuario){
		$dados = [
				null,
				$usuario->getNome(),
				$usuario->getEmail(),
				$usuario->getSenhaHash(),
				$usuario->getAcesso(),
				$usuario->getstatus(),
		];
		return $this->dbQuery->insert($dados);
	}
	
	public function update(Usuario $usuario){
		$dados = [
			'id_usuario' => $usuario->getIdUsuario(),
			'nome'       => $usuario->getNome(),
			'email'      => $usuario->getEmail(),
			'senha'      => $usuario->getSenhaHash(),
			'acesso'     => $usuario->getAcesso(),
			'status'     => $usuario->getstatus(),
		];

		$where = new Where();
		$where->addCondition('AND', 'id_usuario', '=', $usuario->getIdUsuario());

		return $this->dbQuery->update($dados, $where);
	}

	
	public function inativar($id) {
		$usuario = $this->getById($id);
		if (!$usuario) {
			return false;
		}
		$usuario->setStatus('inativo');
		return $this->update($usuario);
	}
}