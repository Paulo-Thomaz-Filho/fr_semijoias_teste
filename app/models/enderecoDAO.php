<?php
namespace app\models;

use core\database\DBQuery;
use core\database\Where;

include_once __DIR__.'/../core/database/DBConnection.php';
include_once __DIR__.'/../core/database/DBQuery.php';
include_once __DIR__.'/../core/database/Where.php';

include_once __DIR__.'/Endereco.php';

class EnderecoDAO {
    private $dbQuery;

    public function __construct(){
        $this->dbQuery = new DBQuery(
            'enderecos', 
            'idEnderecos, usuario_id, cep, logradouro, numero, complemento, bairro, cidade, estado', 
            'idEnderecos'
        );
    }

    public function getAll(){
        $enderecos = [];
        $dados = $this->dbQuery->select();

        foreach($dados as $endereco){
            $enderecos[] = new Endereco(...array_values($endereco));
        }

        return $enderecos;
    }

    public function getById($id){
        $where = new Where();
        $where->addCondition('AND', 'idEnderecos', '=', $id);
        $dados = $this->dbQuery->selectFiltered($where);

        if($dados){
            $endereco = new Endereco();
            $endereco->load(...array_values($dados[0]));
            return $endereco;
        }

        return null;
}


    public function getByUsuarioId($usuarioId) {
        $where = new Where();
        $where->addCondition('AND', 'usuario_id', '=', $usuarioId); 
        return $this->dbQuery->selectFiltered($where);
    }



    public function insert(Endereco $endereco){
        $dados = [
            null,
            $endereco->getUsuarioId(),
            $endereco->getCep(),
            $endereco->getLogradouro(),
            $endereco->getNumero(),
            $endereco->getComplemento(),
            $endereco->getBairro(),
            $endereco->getCidade(),
            $endereco->getEstado(),
        ];
        return $this->dbQuery->insert($dados);
    }

    public function update(Endereco $endereco){
        $dados = [
            'idEnderecos' => $endereco->getidEnderecos(),
            'UsuarioId'   => $endereco->getUsuarioId(),
            'Cep'         => $endereco->getCep(),
            'Logradouro'  => $endereco->getLogradouro(),
            'Numero'      => $endereco->getNumero(),
            'Complemento' => $endereco->getComplemento(),
            'Bairro'      => $endereco->getBairro(),
            'Cidade'      => $endereco->getCidade(),
            'Estado'      => $endereco->getEstado(),
        ];
        return $this->dbQuery->update($dados);
    }

    public function delete($id){
        return $this->dbQuery->delete(['idEnderecos' => $id]);
    }
}
