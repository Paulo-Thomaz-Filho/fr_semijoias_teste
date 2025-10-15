<?php
// Em: app/models/PromocaoDAO.php

namespace app\models;

use core\database\DBQuery;
use core\database\Where;

include_once __DIR__.'/../core/database/DBConnection.php';
include_once __DIR__.'/../core/database/DBQuery.php';
include_once __DIR__.'/../core/database/Where.php';
include_once __DIR__.'/Promocao.php';

class PromocaoDAO {
    private $dbQuery;
    
    public function __construct(){
        $this->dbQuery = new DBQuery(
            'promocoes', 
            'id_promocao, nome, data_inicio, data_fim, desconto', 
            'id_promocao'
        );
    }
    
    public function getAll(){
        $promocoes = [];
        
        $dados = $this->dbQuery->select();

        foreach($dados as $dadosPromocao){
            $promocao = new Promocao();
            // Mapear campos do banco (snake_case) para o Model (camelCase)
            $promocao->load(
                $dadosPromocao['id_promocao'],
                $dadosPromocao['nome'],
                $dadosPromocao['data_inicio'],
                $dadosPromocao['data_fim'],
                'percentual', // tipo padrão
                $dadosPromocao['desconto'],
                'ativa' // status padrão
            );
            $promocoes[] = $promocao;
        }

        return $promocoes;
    }
    
    public function getById($id){
        $where = new Where();
        $where->addCondition('AND', 'id_promocao', '=', $id);
        $dados = $this->dbQuery->selectFiltered($where);

        if($dados){
            $promocao = new Promocao();
            $promocao->load(...array_values($dados[0]));
            return $promocao;
        }

        return null;
    }
    
    public function insert(Promocao $promocao){
        $status = $promocao->getStatus() ?? 'ativa';

        $dados = [
            null, // id_promocao (auto increment)
            $promocao->getNome(),
            $promocao->getDataInicio(),
            $promocao->getDataFim(),
            $promocao->getTipo(),
            $promocao->getValor(),
            $status
        ];
        return $this->dbQuery->insert($dados);
    }

    public function update(Promocao $promocao){
        $dados = [
            'id_promocao'    => $promocao->getIdPromocao(),
            'nome'           => $promocao->getNome(),
            'data_inicio'    => $promocao->getDataInicio(),
            'data_fim'       => $promocao->getDataFim(),
            'desconto'       => $promocao->getValor(),
        ];
        
        return $this->dbQuery->update($dados);
    }
    
    // 5. MÉTODO DELETE SUBSTITUÍDO POR INATIVAR:
    //    Este método implementa o "soft delete".
    public function inativar($id) {
        // Busca a promoção completa para poder passá-la ao método update.
        $promocao = $this->getById($id);
        if (!$promocao) {
            return false;
        }

        // Altera apenas o status do objeto.
        $promocao->setStatus('inativa');

        // Chama o método update do próprio DAO, que já contém o workaround para o DBQuery.
        return $this->update($promocao);
    }
}