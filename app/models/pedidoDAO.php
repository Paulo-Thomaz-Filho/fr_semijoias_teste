<?php
namespace app\models;


include_once __DIR__.'/../core/database/DBConnection.php';
include_once __DIR__.'/../core/database/DBQuery.php';
include_once __DIR__.'/../core/database/Where.php';

use core\database\DBConnection;
use core\database\DBQuery;
use core\database\Where;
use PDO;

require_once __DIR__.'/Pedido.php';

class PedidoDAO {
    private $dbQuery;
    private $conn;

    public function __construct(){
        $this->conn = (new DBConnection())->getConn();
        
        $this->dbQuery = new DBQuery(
            'pedidos', 
            'id_pedido, produto_nome, cliente_nome, preco, endereco, data_pedido, quantidade, status, descricao', 
            'id_pedido'
        );
    }

    public function getAll() {
        $pedidos = [];
        $dados = $this->dbQuery->select();

        foreach($dados as $row){ 
            $pedido = new Pedido();
            $pedido->setIdPedido($row['id_pedido']);
            $pedido->setProdutoNome($row['produto_nome']);
            $pedido->setClienteNome($row['cliente_nome']);
            $pedido->setPreco($row['preco']);
            $pedido->setEndereco($row['endereco']);
            $pedido->setDataPedido($row['data_pedido']);
            $pedido->setQuantidade($row['quantidade']);
            $pedido->setStatus($row['status']);
            $pedido->setDescricao($row['descricao']);
            $pedidos[] = $pedido;
        }

        return $pedidos;
    }
    
    public function getAllByStatus($status) {
        $pedidos = [];
        $where = new Where();
        $where->addCondition('AND', 'status', '=', $status);
        $dados = $this->dbQuery->selectFiltered($where);

        foreach($dados as $row){ 
            $pedido = new Pedido();
            $pedido->setIdPedido($row['id_pedido']);
            $pedido->setProdutoNome($row['produto_nome']);
            $pedido->setClienteNome($row['cliente_nome']);
            $pedido->setPreco($row['preco']);
            $pedido->setEndereco($row['endereco']);
            $pedido->setDataPedido($row['data_pedido']);
            $pedido->setQuantidade($row['quantidade']);
            $pedido->setStatus($row['status']);
            $pedido->setDescricao($row['descricao']);
            $pedidos[] = $pedido;
        }

        return $pedidos;
    }

    public function getTotalGanhos() {
        $conn = (new \core\database\DBConnection())->getConn();
        $stmt = $conn->prepare("SELECT SUM(preco * quantidade) as total FROM pedidos WHERE status = 'Concluído'");
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function getTotalVendas() {
        $conn = (new \core\database\DBConnection())->getConn();
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM pedidos WHERE status = 'Concluído'");
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function getAllStatus() {
        $conn = (new \core\database\DBConnection())->getConn();
        $stmt = $conn->prepare("SELECT DISTINCT status FROM pedidos WHERE status IS NOT NULL AND status != '' ORDER BY status");
        $stmt->execute();
        
        $status = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $status[] = $row['status'];
        }
        
        // Retorna array vazio se não houver status no banco
        // O frontend já está preparado para lidar com isso
        return $status;
    }

    public function getById($id){
        $where = new Where();
        $where->addCondition('AND', 'id_pedido', '=', $id);
        $dados = $this->dbQuery->selectFiltered($where);

        if($dados){
            $pedido = new Pedido();
            $row = $dados[0];

            $pedido->setIdPedido($row['id_pedido']);
            $pedido->setProdutoNome($row['produto_nome']);
            $pedido->setClienteNome($row['cliente_nome']);
            $pedido->setPreco($row['preco']);
            $pedido->setEndereco($row['endereco']);
            $pedido->setDataPedido($row['data_pedido']);
            $pedido->setQuantidade($row['quantidade']);
            $pedido->setStatus($row['status']);
            $pedido->setDescricao($row['descricao']);
            return $pedido;
        }
    return null;
    }

    public function insert(Pedido $pedido){
        $dados = [
            null,  // id_pedido (auto increment)
            $pedido->getProdutoNome(),
            $pedido->getClienteNome(),
            $pedido->getPreco(),
            $pedido->getEndereco(),
            $pedido->getDataPedido(),
            $pedido->getQuantidade(),
            $pedido->getStatus(),
            $pedido->getDescricao()
        ];
        return $this->dbQuery->insert($dados);
    }


    public function update(Pedido $pedido){
        $dados = [
            'id_pedido'    => $pedido->getIdPedido(),
            'cliente_nome' => $pedido->getClienteNome(),
            'endereco'     => $pedido->getEndereco(),
            'data_pedido'  => $pedido->getDataPedido(),
            'status'       => $pedido->getStatus(),
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