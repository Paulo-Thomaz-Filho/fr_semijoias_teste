<?php

namespace app\models;

use core\database\DBConnection;
use core\database\DBQuery;
use core\database\Where;

include_once __DIR__.'/../core/database/DBConnection.php';
include_once __DIR__.'/../core/database/DBQuery.php';
include_once __DIR__.'/../core/database/Where.php';
include_once __DIR__.'/Promocao.php';

class PromocaoDAO {
    // Remove promoções expiradas ou inativas dos produtos
    public function removerPromocoesExpiradasDosProdutos() {
        $hoje = date('Y-m-d');
        $db = new \core\database\DBConnection();
        // Busca todas promoções expiradas (status=1 e data_fim < hoje)
        $sqlExp = "SELECT id_promocao FROM promocoes WHERE status = 1 AND data_fim < ?";
        $stmtExp = $db->getConn()->prepare($sqlExp);
        $stmtExp->execute([$hoje]);
        $expiradas = $stmtExp->fetchAll(\PDO::FETCH_COLUMN);

        // Busca todas promoções inativas (status=0 ou status='inativa')
        $sqlInat = "SELECT id_promocao FROM promocoes WHERE status = 0 OR status = 'inativa'";
        $stmtInat = $db->getConn()->query($sqlInat);
        $inativas = $stmtInat->fetchAll(\PDO::FETCH_COLUMN);

        $idsRemover = array_unique(array_merge($expiradas ?: [], $inativas ?: []));
        if ($idsRemover && count($idsRemover) > 0) {
            $in = implode(',', array_map('intval', $idsRemover));
            if (!empty($in)) {
                // Remove dos produtos
                $sqlUpdate = "UPDATE produtos SET id_promocao = NULL WHERE id_promocao IN ($in)";
                $db->getConn()->exec($sqlUpdate);
                // Inativa promoções expiradas
                if (!empty($expiradas)) {
                    $inExp = implode(',', array_map('intval', $expiradas));
                    $sqlInativa = "UPDATE promocoes SET status = 0 WHERE id_promocao IN ($inExp)";
                    $db->getConn()->exec($sqlInativa);
                }
            }
        }
    }
    private $dbQuery;
    
    public function __construct(){
        $this->dbQuery = new DBQuery(
            'promocoes', 
            'id_promocao, nome, data_inicio, data_fim, desconto, tipo_desconto, status, descricao', 
            'id_promocao'
        );
    }
    
    public function getAll(){
    $promocoes = [];
    $hoje = date('Y-m-d');
    // Retorna todas as promoções, sem filtro
    $dados = $this->dbQuery->select();

        foreach($dados as $row){
            $promocao = new Promocao(
                $row['id_promocao'],
                $row['nome'],
                $row['data_inicio'],
                $row['data_fim'],
                $row['descricao'],
                $row['desconto'],
                $row['tipo_desconto'] ?? 'percentual',
                $row['status'] ?? null
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
            $row = $dados[0];
            return new Promocao(
                $row['id_promocao'],
                $row['nome'],
                $row['data_inicio'],
                $row['data_fim'],
                $row['descricao'],
                $row['desconto'],
                $row['tipo_desconto'] ?? 'percentual',
                $row['status'] ?? null
            );
        }

        return null;
    }
    
    public function insert(Promocao $promocao){
        $dados = [
            null, // id_promocao (auto increment)
            $promocao->getNome(),
            $promocao->getDataInicio(),
            $promocao->getDataFim(),
            $promocao->getDescricao(),
            $promocao->getDesconto(),
            $promocao->getTipoDesconto(),
            $promocao->getStatus()
        ];
        return $this->dbQuery->insert($dados);
    }

    public function update(Promocao $promocao){
        $dados = [
            'id_promocao'    => $promocao->getIdPromocao(),
            'nome'           => $promocao->getNome(),
            'data_inicio'    => $promocao->getDataInicio(),
            'data_fim'       => $promocao->getDataFim(),
            'descricao'      => $promocao->getDescricao(),
            'desconto'       => $promocao->getDesconto(),
            'tipo_desconto'  => $promocao->getTipoDesconto(),
            'status'         => $promocao->getStatus()
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
