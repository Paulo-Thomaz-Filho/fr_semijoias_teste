<?php

namespace App\Models;

use PDO;
use PDOException;

/**
 * DAO (Data Access Object) para a entidade Promocao.
 * Responsável por toda a comunicação com a tabela `Promocoes` no banco de dados.
 */
class PromocaoDAO
{
    /** @var PDO A conexão com o banco de dados. */
    private PDO $conexao;

    public function __construct(PDO $db)
    {
        $this->conexao = $db;
    }

    /**
     * Salva (insere ou atualiza) uma promoção no banco de dados.
     * @param Promocao $promocao O objeto Promocao a ser salvo.
     * @return bool Retorna true em caso de sucesso, false em caso de falha.
     */
    public function save(Promocao &$promocao): bool
    {
        if ($promocao->getId()) {
            // Atualiza uma promoção existente
            $query = "UPDATE Promocoes SET nome = :nome, tipo_desconto = :tipo, valor_desconto = :valor, ativo = :ativo, data_inicio = :inicio, data_fim = :fim WHERE id_promocao = :id";
        } else {
            // Insere uma nova promoção
            $query = "INSERT INTO Promocoes (nome, tipo_desconto, valor_desconto, ativo, data_inicio, data_fim) VALUES (:nome, :tipo, :valor, :ativo, :inicio, :fim)";
        }

        try {
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':nome', $promocao->getNome());
            $stmt->bindValue(':tipo', $promocao->getTipoDesconto());
            $stmt->bindValue(':valor', $promocao->getValorDesconto());
            $stmt->bindValue(':ativo', $promocao->isAtivo(), PDO::PARAM_BOOL);
            $stmt->bindValue(':inicio', $promocao->getDataInicio());
            $stmt->bindValue(':fim', $promocao->getDataFim());

            if ($promocao->getId()) {
                $stmt->bindValue(':id', $promocao->getId());
            }

            $stmt->execute();

            if (!$promocao->getId()) {
                $promocao->setId((int)$this->conexao->lastInsertId());
            }
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Busca uma promoção pelo seu ID.
     * @return Promocao|null Retorna o objeto Promocao ou null se não encontrar.
     */
    public function findById(int $id): ?Promocao
    {
        $query = "SELECT * FROM Promocoes WHERE id_promocao = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        return $dados ? $this->hydrate($dados) : null;
    }

    /**
     * Busca todas as promoções.
     * @return array Retorna um array de objetos Promocao.
     */
    public function findAll(): array
    {
        $query = "SELECT * FROM Promocoes ORDER BY nome ASC";
        $stmt = $this->conexao->query($query);
        
        $listaPromocoes = [];
        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $listaPromocoes[] = $this->hydrate($dados);
        }
        return $listaPromocoes;
    }

    /**
     * Exclui uma promoção pelo seu ID.
     * @param int $id O ID da promoção a ser excluída.
     * @return bool Retorna true em caso de sucesso, false em caso de falha.
     */
    public function delete(int $id): bool
    {
        $query = "DELETE FROM Promocoes WHERE id_promocao = :id";
        try {
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Método auxiliar para "hidratar" um objeto Promocao com dados do banco.
     * @param array $dados Os dados vindos do banco.
     * @return Promocao Retorna um objeto Promocao preenchido.
     */
    private function hydrate(array $dados): Promocao
    {
        $promocao = new Promocao();
        $promocao->setId((int)$dados['id_promocao']);
        $promocao->setNome($dados['nome']);
        $promocao->setTipoDesconto($dados['tipo_desconto']);
        $promocao->setValorDesconto((float)$dados['valor_desconto']);
        $promocao->setAtivo((bool)$dados['ativo']);
        $promocao->setDataInicio($dados['data_inicio']);
        $promocao->setDataFim($dados['data_fim']);
        return $promocao;
    }
}

