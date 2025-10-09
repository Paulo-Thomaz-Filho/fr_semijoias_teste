<?php
namespace app\models;

use core\database\DBQuery;
use core\database\Where;

include_once __DIR__.'/../core/database/DBConnection.php';
include_once __DIR__.'/../core/database/DBQuery.php';
include_once __DIR__.'/../core/database/Where.php';

include_once __DIR__.'/Pedido.php';

class PedidoDAO {
    private $dbQuery;
    private $conn; 

    public function __construct(){
        $this->conn = (new DBConnection())->getConn();
        $this->dbQuery = new DBQuery(
            'pedidos', 
            'idPedido, usuario_id, endereco_id, valor_total, status, data_pedido', 
            'idPedido'
        );
    }

    public function getAll() {
        // ATENÇÃO: Verifique se os nomes das suas tabelas e colunas estão corretos nesta consulta!
        // Tabela de pedidos: 'pedidos'
        // Tabela de usuários/clientes: 'usuarios'
        // Coluna de junção em 'pedidos': 'usuario_id'
        // Coluna de junção em 'usuarios': 'id'
        // Coluna com o nome do cliente: 'nome'
        $sql = "
            SELECT 
                p.idPedido,
                p.usuario_id,
                p.endereco_id,
                p.valor_total,
                p.status,
                p.data_pedido,
                u.nome as nome_cliente 
            FROM 
                pedidos p
            JOIN 
                usuarios u ON p.usuario_id = u.id
            ORDER BY 
                p.data_pedido DESC
        ";

        $stmt = $this->conn->query($sql);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Convertendo o array de resultados em um array de objetos Pedido
        $pedidos = [];
        foreach ($resultados as $row) {
            $pedido = new Pedido();
            $pedido->setIdPedido($row['idPedido']);
            $pedido->setUsuarioId($row['usuario_id']);
            $pedido->setEnderecoId($row['endereco_id']);
            $pedido->setValorTotal($row['valor_total']);
            $pedido->setStatus($row['status']);
            $pedido->setDataPedido($row['data_pedido']);
            $pedido->setNomeCliente($row['nome_cliente']);
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
            $pedido = new \app\models\Pedido();
            $pedido->load(...array_values($row));
            $pedidos[] = $pedido;
        }

        return $pedidos;
    }

    public function getTotalGanhos() {
        $conn = (new \core\database\DBConnection())->getConn();
        $stmt = $conn->prepare("SELECT SUM(valor_total) as total FROM pedidos WHERE status = 'Concluido'"); // ou o status que define um ganho
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function getTotalVendas() {
        $conn = (new \core\database\DBConnection())->getConn();
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM pedidos WHERE status = 'Concluido'");
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function getById($id){
        $where = new Where();
        $where->addCondition('AND', 'idPedido', '=', $id);
        $dados = $this->dbQuery->selectFiltered($where);

        if($dados){
            $pedido = new Pedido();
            $pedido->load(...array_values($dados[0]));
            return $pedido;
        }
        return null;
    }

    public function insert(Pedido $pedido){
        $dados = [
            null,
            $pedido->getUsuarioId(),
            $pedido->getEnderecoId(),
            $pedido->getValorTotal(),
            $pedido->getStatus(),
            $pedido->getDataPedido(),
        ];
        return $this->dbQuery->insert($dados);
    }

    public function update(Pedido $pedido){
        $dados = [
            'usuario_id'   => $pedido->getUsuarioId(),
            'endereco_id'  => $pedido->getEnderecoId(),
            'valor_total'  => $pedido->getValorTotal(),
            'status'      => $pedido->getStatus(),
            'data_pedido'  => $pedido->getDataPedido(),
        ];
        $where = new Where();
        $where->addCondition('AND', 'idPedido', '=', $pedido->getIdPedido());
        return $this->dbQuery->update($dados, $where);
    }

    public function delete($id){
        return $this->dbQuery->delete(['IdPedido' => $id]);
    }
}