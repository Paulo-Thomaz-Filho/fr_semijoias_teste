<?php

namespace app\models;

require_once __DIR__ . '/../core/database/DBConnection.php';
require_once __DIR__ . '/Status.php';

class StatusDAO {
    public function getByName($nome) {
        $stmt = $this->conn->prepare('SELECT id_status, nome FROM status WHERE nome = :nome');
        $stmt->bindValue(':nome', $nome);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($row) {
            return new Status($row['id_status'], $row['nome']);
        }
        return null;
    }
    private $conn;
    public function __construct() {
        $this->conn = (new \core\database\DBConnection())->getConn();
    }

    public function getAll() {
        $stmt = $this->conn->prepare('SELECT id_status, nome FROM status ORDER BY nome');
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $statusList = [];
        foreach ($result as $row) {
            $statusList[] = (new Status($row['id_status'], $row['nome']))->toArray();
        }
        return $statusList;
    }

    public function salvar($nome) {
        $stmt = $this->conn->prepare('INSERT INTO status (nome) VALUES (:nome)');
        $stmt->bindValue(':nome', $nome);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    public function getById($id_status) {
        $stmt = $this->conn->prepare('SELECT id_status, nome FROM status WHERE id_status = :id_status');
        $stmt->bindValue(':id_status', $id_status);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($row) {
            return new Status($row['id_status'], $row['nome']);
        }
        return null;
    }
}
