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
        // 1. CONSTRUTOR CORRIGIDO:
        // - Nomes das colunas correspondem ao banco de dados (ex: 'data_inicio').
        // - Adicionada a nova coluna 'status'.
        $this->dbQuery = new DBQuery(
            'promocoes', 
            'IdPromocao, nome, data_inicio, data_fim, tipo, valor_desconto, status', 
            'IdPromocao'
        );
    }
    
    public function getAll(){
        $promocoes = [];
        
        // 2. GETALL CORRIGIDO:
        // - Filtra para buscar apenas as promoções com status 'ativa'.
        $where = new Where();
        $where->addCondition('AND', 'status', '=', 'ativa');

        $dados = $this->dbQuery->selectFiltered($where);

        foreach($dados as $dadosPromocao){
            $promocao = new Promocao();
            // O método load() agora recebe 7 argumentos, incluindo o status.
            $promocao->load(...array_values($dadosPromocao));
            $promocoes[] = $promocao;
        }

        return $promocoes;
    }
    
    public function getById($id){
        $where = new Where();
        $where->addCondition('AND', 'IdPromocao', '=', $id);
        $dados = $this->dbQuery->selectFiltered($where);

        if($dados){
            $promocao = new Promocao();
            $promocao->load(...array_values($dados[0]));
            return $promocao;
        }

        return null;
    }
    
    public function insert(Promocao $promocao){
        // 3. INSERT CORRIGIDO:
        // - Garante que uma nova promoção seja criada como 'ativa'.
        $status = $promocao->getStatus() ?? 'ativa';

        // O array de dados precisa ter 7 valores para corresponder aos 7 campos do construtor.
        $dados = [
            null, // IdPromocao
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
        // 4. UPDATE CORRIGIDO (WORKAROUND):
        // - Cria um array com todos os 7 campos para contornar o bug do DBQuery,
        //   que espera receber todos os campos da tabela.
        $dados = [
            'IdPromocao'     => $promocao->getIdPromocao(),
            'nome'           => $promocao->getNome(),
            'data_inicio'    => $promocao->getDataInicio(),
            'data_fim'       => $promocao->getDataFim(),
            'tipo'           => $promocao->getTipo(),
            'valor_desconto' => $promocao->getValor(),
            'status'         => $promocao->getStatus() ?? 'ativa'
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