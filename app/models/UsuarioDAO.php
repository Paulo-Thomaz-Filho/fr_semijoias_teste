<?php
namespace App\Models;

use PDO;
use PDOException;

/**
 * DAO para a entidade Usuario, refatorado para o padrão moderno e seguro.
 */
class UsuarioDAO
{
    private PDO $conexao;

    /**
     * Recebe a conexão PDO via Injeção de Dependência.
     */
    public function __construct(PDO $db)
    {
        $this->conexao = $db;
    }

    private function hydrate(array $row): Usuario
    {
        $usuario = new Usuario();
        $usuario->setId((int)$row['id_usuario']);
        $usuario->setNome($row['nome']);
        $usuario->setEmail($row['email']);
        $usuario->setSenhaHash($row['senha_hash']);
        $usuario->setTipoAcesso($row['tipo_acesso']);
        return $usuario;
    }

    public function findById(int $id): ?Usuario
    {
        $stmt = $this->conexao->prepare("SELECT * FROM Usuarios WHERE id_usuario = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $this->hydrate($data) : null;
    }

    public function findByEmail(string $email): ?Usuario
    {
        $stmt = $this->conexao->prepare("SELECT * FROM Usuarios WHERE email = :email");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $this->hydrate($data) : null;
    }

    /**
     * Renomeado de index() para findAll() para seguir o padrão do seu routes.json
     */
    public function index(): array
    {
        $stmt = $this->conexao->query("SELECT * FROM Usuarios ORDER BY nome ASC");
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = $this->hydrate($row);
        }
        return $results;
    }

    public function save(Usuario &$usuario): bool
    {
        if ($usuario->getId()) {
            $query = "UPDATE Usuarios SET nome = :nome, email = :email, senha_hash = :hash, tipo_acesso = :tipo WHERE id_usuario = :id";
        } else {
            $query = "INSERT INTO Usuarios (nome, email, senha_hash, tipo_acesso) VALUES (:nome, :email, :hash, :tipo)";
        }

        try {
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':nome', $usuario->getNome());
            $stmt->bindValue(':email', $usuario->getEmail());
            $stmt->bindValue(':hash', $usuario->getSenhaHash());
            $stmt->bindValue(':tipo', $usuario->getTipoAcesso());

            if ($usuario->getId()) {
                $stmt->bindValue(':id', $usuario->getId(), PDO::PARAM_INT);
            }
            $stmt->execute();
            if (!$usuario->getId()) {
                $usuario->setId((int)$this->conexao->lastInsertId());
            }
            return true;
        } catch (PDOException $e) {
            error_log("Erro ao salvar usuário: " . $e->getMessage());
            return false;
        }
    }

    public function login(string $email, string $senha): ?string // Alteramos o retorno de bool para ?string
    {
        // 1. Usa o método já existente para encontrar o usuário pelo e-mail
        $usuario = $this->findByEmail($email);

        // 2. Verifica se o usuário foi encontrado E se a senha está correta
        if ($usuario && $usuario->verificarSenha($senha)) {
            
            // 3. SUCESSO! Inicia a sessão e armazena os dados do usuário.
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            $_SESSION['user_id'] = $usuario->getId();
            $_SESSION['user_nome'] = $usuario->getNome();
            $_SESSION['user_email'] = $usuario->getEmail();
            $_SESSION['user_tipo'] = $usuario->getTipoAcesso();
            $_SESSION['user_logged_in'] = true;

            session_write_close();
            return $usuario->getTipoAcesso();
        }
    return null;
    }

    public function countTotalUsuarios(): int
    {
        $stmt = $this->conexao->prepare("SELECT COUNT(id_usuario) FROM Usuarios");
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function delete(int $id): bool
    {
        try {
            $stmt = $this->conexao->prepare("DELETE FROM Usuarios WHERE id_usuario = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao deletar usuário: " . $e->getMessage());
            return false;
        }
    }
}