<?php
namespace app\models;

use core\database\DBQuery;
use core\database\Where;

include_once __DIR__.'/../core/database/DBConnection.php';
include_once __DIR__.'/../core/database/DBQuery.php';
include_once __DIR__.'/../core/database/Where.php';

include_once __DIR__.'/ItemPedido.php';

class ItemPedidoDAO {
    private $dbQuery;

    public function __construct(){
        $this->dbQuery = new DBQuery(
            'itens_pedido', 
            'IdItemPedido, PedidoId, ProdutoId, Quantidade, ValorUnitario', 
            'IdItemPedido'
        );
    }

    public function getAll(){
        $itensPedido = [];
        $dados = $this->dbQuery->select();

        foreach($dados as $itemPedido){
            $itensPedido[] = new ItemPedido(...array_values($itemPedido));
        }

        return $itensPedido;
    }

    public function getById($id){
        $where = new Where();
        $where->addCondition('AND', 'IdItemPedido', '=', $id);
        $dados = $this->dbQuery->selectFiltered($where);

        if($dados){
            return new ItemPedido(...array_values($dados[0]));
        }

        return null;
    }

    public function getByPedidoId($pedidoId){
        $itensPedido = [];
        $where = new Where();
        $where->addCondition('AND', 'PedidoId', '=', $pedidoId);
        $dados = $this->dbQuery->selectFiltered($where);

        foreach($dados as $itemPedido){
            $itensPedido[] = new ItemPedido(...array_values($itemPedido));
        }

        return $itensPedido;
    }


    public function insert(ItemPedido $itemPedido){
        $dados = [
            null,
            $itemPedido->getPedidoId(),
            $itemPedido->getProdutoId(),
            $itemPedido->getQuantidade(),
            $itemPedido->getValorUnitario(),
        ];
        return $this->dbQuery->insert($dados);
    }

    public function update(ItemPedido $itemPedido){
        $dados = [
            'IdItemPedido'  => $itemPedido->getIdItemPedido(),
            'PedidoId'      => $itemPedido->getPedidoId(),
            'ProdutoId'     => $itemPedido->getProdutoId(),
            'Quantidade'    => $itemPedido->getQuantidade(),
            'ValorUnitario' => $itemPedido->getValorUnitario(),
        ];
        return $this->dbQuery->update($dados);
    }

    public function delete($id){
        return $this->dbQuery->delete(['IdItemPedido' => $id]);
    }
}
