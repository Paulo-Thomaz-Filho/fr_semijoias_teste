<?php
// Em: app/models/ItemPedidoDAO.php
namespace app\models;

use core\database\DBQuery;
use core\database\Where;

// Se você não usa um autoloader, mantenha os includes/require_once
require_once __DIR__.'/../core/database/DBConnection.php';
require_once __DIR__.'/../core/database/DBQuery.php';
require_once __DIR__.'/../core/database/Where.php';
require_once __DIR__.'/ItemPedido.php';

class ItemPedidoDAO {
    private $dbQuery;

    public function __construct(){
        // CORRIGIDO:
        // 1. Nomes das colunas exatamente como na sua tabela do banco de dados.
        // 2. Chave primária definida corretamente como 'IdItemPedido'.
        $colunas = 'IdItemPedido, pedido_id, produto_id, quantidade, valor_unitario';
        $this->dbQuery = new DBQuery('itens_pedido', $colunas, 'IdItemPedido');
    }

    public function getAll(){
        $itensPedido = [];
        $dados = $this->dbQuery->select();

        foreach($dados as $itemDados){
            $item = new ItemPedido();
            $item->set_id_item_pedido($itemDados['IdItemPedido']);
            $item->set_pedido_id($itemDados['pedido_id']);
            $item->set_produto_id($itemDados['produto_id']);
            $item->set_quantidade($itemDados['quantidade']);
            $item->set_valor_unitario($itemDados['valor_unitario']);
            $itensPedido[] = $item;
        }
        return $itensPedido;
    }

    public function getById($id){
        // CORRIGIDO: getById deve buscar pela chave primária 'IdItemPedido'.
        $where = new Where();
        $where->addCondition('AND', 'IdItemPedido', '=', $id);
        $dados = $this->dbQuery->selectFiltered($where);

        if($dados){
            $item = new ItemPedido();
            $item->set_id_item_pedido($dados[0]['IdItemPedido']);
            $item->set_pedido_id($dados[0]['pedido_id']);
            $item->set_produto_id($dados[0]['produto_id']);
            $item->set_quantidade($dados[0]['quantidade']);
            $item->set_valor_unitario($dados[0]['valor_unitario']);
            return $item;
        }
        return null;
    }

    public function getByPedidoId($pedidoId){
        $conn = (new \core\database\DBConnection())->getConn();
        
        $sql = "
            SELECT 
                ip.IdItemPedido, ip.pedido_id, ip.produto_id, ip.quantidade, ip.valor_unitario,
                p.nome as nome_produto
            FROM 
                itens_pedido ip
            JOIN
                produtos p ON ip.produto_id = p.IdProduto
            WHERE 
                ip.pedido_id = ?
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$pedidoId]);
        $resultados = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $itensPedido = [];
        foreach($resultados as $row){
            $item = new ItemPedido();
            $item->set_id_item_pedido($row['IdItemPedido']);
            $item->set_pedido_id($row['pedido_id']);
            $item->set_produto_id($row['produto_id']);
            $item->set_quantidade($row['quantidade']);
            $item->set_valor_unitario($row['valor_unitario']);
            $item->set_nome_produto($row['nome_produto']);
            $itensPedido[] = $item;
        }
        return $itensPedido;
    }



    public function insert(ItemPedido $itemPedido){
        // A classe DBQuery->insert espera um array numérico na ordem das colunas do construtor
        $dados = [
            null, // para o IdItemPedido (auto_increment)
            $itemPedido->get_pedido_id(),
            $itemPedido->get_produto_id(),
            $itemPedido->get_quantidade(),
            $itemPedido->get_valor_unitario(),
        ];
        return $this->dbQuery->insert($dados);
    }

    public function update(ItemPedido $itemPedido){
        // CORRIGIDO:
        // A classe DBQuery->update espera um array associativo com todas as chaves,
        // incluindo a chave primária, e com nomes idênticos aos do construtor.
        $dados = [
            'IdItemPedido'   => $itemPedido->get_id_item_pedido(),
            'pedido_id'      => $itemPedido->get_pedido_id(),
            'produto_id'     => $itemPedido->get_produto_id(),
            'quantidade'    => $itemPedido->get_quantidade(),
            'valor_unitario' => $itemPedido->get_valor_unitario(),
        ];
        // A chamada ao update da DBQuery não precisa de um $where, pois ela o constrói internamente
        return $this->dbQuery->update($dados);
    }

    public function delete($id){
        // CORRIGIDO: O delete da DBQuery espera um array com a chave primária.
        return $this->dbQuery->delete(['IdItemPedido' => $id]);
    }
}