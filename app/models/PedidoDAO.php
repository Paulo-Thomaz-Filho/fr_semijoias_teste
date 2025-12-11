<?php

namespace app\models;

use core\database\DBConnection;
use core\database\DBQuery;
use core\database\Where;

include_once __DIR__.'/../core/database/DBConnection.php';
include_once __DIR__.'/../core/database/DBQuery.php';
include_once __DIR__.'/../core/database/Where.php';
include_once __DIR__.'/Pedido.php';
include_once __DIR__.'/StatusDAO.php';

class PedidoDAO {
    private $dbQuery;
    private $conn;

    public function __construct(){
        $this->conn = (new DBConnection())->getConn();
        
        $this->dbQuery = new DBQuery(
            'pedidos', 
                'id_pedido, produto_nome, id_cliente, preco, endereco, data_pedido, quantidade, id_status, descricao', 
            'id_pedido'
        );
    }

    public function getAll() {
        $pedidos = [];
        $dados = $this->dbQuery->select();

        // Inclui dependências para buscar produto e status
        require_once __DIR__.'/ProdutoDAO.php';
        require_once __DIR__.'/StatusDAO.php';
        $produtoDAO = new \app\models\ProdutoDAO();
        $statusDAO = new \app\models\StatusDAO();

        foreach($dados as $row){
            $pedido = new Pedido(
                $row['id_pedido'],
                $row['produto_nome'],
                $row['id_cliente'],
                $row['preco'],
                $row['endereco'],
                $row['data_pedido'],
                $row['quantidade'],
                $row['id_status'],
                $row['descricao']
            );
            $arr = $pedido->toArray();

            // Buscar marca e categoria do produto pelo nome (ajuste se tiver id_produto)
            $produto = null;
            $produtos = $produtoDAO->getAll();
            foreach ($produtos as $prod) {
                if (strtolower($prod->getNome()) === strtolower($row['produto_nome'])) {
                    $produto = $prod;
                    break;
                }
            }
            $arr['marca'] = $produto ? $produto->getMarca() : null;
            $arr['categoria'] = $produto ? $produto->getCategoria() : null;
            $arr['caminhoImagem'] = $produto ? $produto->getCaminhoImagem() : null;

            // Buscar nome do status
            $statusObj = $statusDAO->getById($row['id_status']);
            $arr['status'] = $statusObj ? $statusObj->getNome() : null;

            $pedidos[] = $arr;
        }

        return $pedidos;
    }
    
    public function getAllByStatus($status) {
        $pedidos = [];
        $where = new Where();
        // Buscar id_status pelo nome
        $statusDAO = new StatusDAO();
        $statusObj = $statusDAO->getByName($status);
        if (!$statusObj) return [];
        $where->addCondition('AND', 'id_status', '=', $statusObj->getIdStatus());
        $dados = $this->dbQuery->selectFiltered($where);

        foreach($dados as $row){ 
            $pedido = new Pedido(
                $row['id_pedido'],
                $row['produto_nome'],
                $row['id_cliente'],
                $row['preco'],
                $row['endereco'],
                $row['data_pedido'],
                $row['quantidade'],
                $row['id_status'],
                $row['descricao']
            );
            $pedidos[] = $pedido;
        }

        return $pedidos;
    }

    public function getTotalGanhos() {
    $conn = (new \core\database\DBConnection())->getConn();
        // Buscar id_status para 'N/A'
    $statusDAO = new StatusDAO();
        $statusObj = $statusDAO->getByName('N/A');
    if (!$statusObj) return 0;
    $stmt = $conn->prepare("SELECT SUM(preco * quantidade) as total FROM pedidos WHERE id_status = :id_status");
    $stmt->bindValue(':id_status', $statusObj->getIdStatus());
    $stmt->execute();
    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $result['total'] ?? 0;
    }

    public function getTotalVendas() {
    $conn = (new \core\database\DBConnection())->getConn();
    $statusDAO = new StatusDAO();
        $statusObj = $statusDAO->getByName('N/A');
    if (!$statusObj) return 0;
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM pedidos WHERE id_status = :id_status");
    $stmt->bindValue(':id_status', $statusObj->getIdStatus());
    $stmt->execute();
    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $result['total'] ?? 0;
    }

    public function getAllStatus() {
        // Agora retorna todos os status da tabela status
        $statusDAO = new StatusDAO();
        $statusList = $statusDAO->getAll();
        // Retorna apenas os nomes
        return array_map(function($s) { return $s['nome']; }, $statusList);
    }

    public function getById($id){
        $where = new Where();
        $where->addCondition('AND', 'id_pedido', '=', $id);
        $dados = $this->dbQuery->selectFiltered($where);

        if($dados){
            $row = $dados[0];
            $pedido = new Pedido(
                $row['id_pedido'],
                $row['produto_nome'],
                $row['id_cliente'],
                $row['preco'],
                $row['endereco'],
                $row['data_pedido'],
                $row['quantidade'],
                $row['id_status'],
                $row['descricao']
            );
            // Retorna apenas o array padrão, sem statusPedido textual
            return $pedido->toArray();
        }
        return null;
    }


    public function insert(Pedido $pedido){
        $dados = [
            null,  // id_pedido (auto increment)
            $pedido->getProdutoNome(),
            $pedido->getIdCliente(),
            $pedido->getPreco(),
            $pedido->getEndereco(),
            $pedido->getDataPedido(),
            $pedido->getQuantidade(),
            $pedido->getIdStatus(),
            $pedido->getDescricao()
        ];
        return $this->dbQuery->insert($dados);
    }


    public function update(Pedido $pedido){
        $dados = [
            'id_pedido'    => $pedido->getIdPedido(),
            'id_cliente'   => $pedido->getIdCliente(),
            'endereco'     => $pedido->getEndereco(),
            'data_pedido'  => $pedido->getDataPedido(),
            'id_status'    => $pedido->getIdStatus(),
            'produto_nome' => $pedido->getProdutoNome(),
            'preco'        => $pedido->getPreco(),
            'quantidade'   => $pedido->getQuantidade(),
            'descricao'    => $pedido->getDescricao()
        ];
        return $this->dbQuery->update($dados);
    }

    public function delete($id){
        try {
            $sql = "DELETE FROM pedidos WHERE id_pedido = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            throw new \Exception('Erro ao deletar pedido: ' . $e->getMessage());
        }
    } 
}
