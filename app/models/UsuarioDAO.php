<?php

namespace app\models;

use core\database\DBConnection;
use core\database\DBQuery;
use core\database\Where;

include_once __DIR__.'/../core/database/DBConnection.php';
include_once __DIR__.'/../core/database/DBQuery.php';
include_once __DIR__.'/../core/database/Where.php';
include_once __DIR__.'/Usuario.php';

class UsuarioDAO {
		private $dbQuery;
		private $conn;

		public function __construct(){
			$this->conn = (new DBConnection())->getConn();

			$this->dbQuery = new DBQuery(
				'usuarios',
				'id_usuario, nome, email, senha, telefone, cpf, endereco, data_nascimento, id_nivel, status, token_ativacao',
				'id_usuario'
			);
		}

		public function getAll(){
				$usuarios = [];
				$dados = $this->dbQuery->select();

			foreach($dados as $row){
				$usuario = new Usuario(
				    $row['id_usuario'],
				    $row['nome'],
				    $row['email'],
				    $row['senha'],
				    $row['telefone'],
				    $row['cpf'],
				    $row['endereco'],
				    $row['data_nascimento'],
				    $row['id_nivel'],
						$row['status'],
    				$row['token_ativacao']
				);
				$usuarios[] = $usuario;
			}
        
			return $usuarios;
		}

		public function getById($id){
				$where = new Where();
				$where->addCondition('AND', 'id_usuario', '=', $id);
				$dados = $this->dbQuery->selectFiltered($where);

			if($dados){
				$row = $dados[0];
				return new Usuario(
				    $row['id_usuario'],
				    $row['nome'],
				    $row['email'],
				    $row['senha'],
				    $row['telefone'],
				    $row['cpf'],
				    $row['endereco'],
				    $row['data_nascimento'],
				    $row['id_nivel'],
						$row['status'],
						$row['token_ativacao']
				);
			}
        
			return null;
		}

		public function getByCpf($cpf){
			$where = new Where();
			$where->addCondition('AND', 'cpf', '=', $cpf);
			$dados = $this->dbQuery->selectFiltered($where);
			if($dados){
				$row = $dados[0];
				return new Usuario(
					$row['id_usuario'],
					$row['nome'],
					$row['email'],
					$row['senha'],
					$row['telefone'],
					$row['cpf'],
					$row['endereco'],
					$row['data_nascimento'],
					$row['id_nivel'],
					$row['status'],
					$row['token_ativacao']
				);
			}
			return null;
		}
		
		public function getByEmail($email){
			$where = new Where();
			$where->addCondition('AND', 'email', '=', $email);
			$dados = $this->dbQuery->selectFiltered($where);
			if($dados){
				$row = $dados[0];
				return new Usuario(
					$row['id_usuario'],
					$row['nome'],
					$row['email'],
					$row['senha'],
					$row['telefone'],
					$row['cpf'],
					$row['endereco'],
					$row['data_nascimento'],
					$row['id_nivel'],
					$row['status'],
					$row['token_ativacao']
				);
			}
			return null;
		}

		public function insert(Usuario $usuario){
				$dados = [
						null,
						$usuario->getNome(),
						$usuario->getEmail(),
						$usuario->getSenha(),
						$usuario->getTelefone(),
						$usuario->getCpf(),
						$usuario->getEndereco(),
						$usuario->getDataNascimento(),
						$usuario->getIdNivel(),
						$usuario->getStatus(),
    				$usuario->getTokenAtivacao()
				];
				return $this->dbQuery->insert($dados);
		}

		public function update(Usuario $usuario){
				$dados = [
						'id_usuario'      => $usuario->getIdUsuario(),
						'nome'            => $usuario->getNome(),
						'email'           => $usuario->getEmail(),
						'senha'           => $usuario->getSenha(),
						'telefone'        => $usuario->getTelefone(),
						'cpf'             => $usuario->getCpf(),
						'endereco'        => $usuario->getEndereco(),
						'data_nascimento' => $usuario->getDataNascimento(),
						'id_nivel'        => $usuario->getIdNivel(),
						'status'          => $usuario->getStatus(),
						'token_ativacao'  => $usuario->getTokenAtivacao()
				];

				return $this->dbQuery->update($dados);

		}

		public function delete($id){
				try {
						$sql = "DELETE FROM usuarios WHERE id_usuario = :id";
						$stmt = $this->conn->prepare($sql);
						$stmt->bindParam(':id', $id, \PDO::PARAM_INT);
						$stmt->execute();
						return $stmt->rowCount() > 0;
				} catch (\PDOException $e) {
					throw new \Exception('Erro ao deletar usuário: ' . $e->getMessage());
				}
		}

		public function getByToken($token){
			$where = new Where();
			$where->addCondition('AND', 'token_ativacao', '=', $token);
			$dados = $this->dbQuery->selectFiltered($where);

			if($dados){
				$row = $dados[0];
				return new Usuario(
					$row['id_usuario'],
					$row['nome'],
					$row['email'],
					$row['senha'],
					$row['telefone'],
					$row['cpf'],
					$row['endereco'],
					$row['data_nascimento'],
					$row['id_nivel'],
					$row['status'],
					$row['token_ativacao']
				);
			}
        return null;
    }
}
