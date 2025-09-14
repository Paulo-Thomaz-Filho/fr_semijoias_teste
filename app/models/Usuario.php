<?php

namespace App\Models;

/**
 * Classe Modelo (POPO - Plain Old PHP Object).
 *
 * Sua única responsabilidade é REPRESENTAR os dados de um usuário.
 * Ela não sabe como se conectar ou salvar no banco de dados.
 */
class Usuario
{
    // Propriedades
    private ?int $id_usuario = null;
    private ?string $nome = null;
    private ?string $email = null;
    private ?string $senha_hash = null;
    private ?string $tipo_acesso = 'cliente';

    // --- Getters (para ler os dados) ---
    public function getId(): ?int
    {
        return $this->id_usuario;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getSenhaHash(): ?string
    {
        return $this->senha_hash;
    }

    public function getTipoAcesso(): ?string
    {
        return $this->tipo_acesso;
    }
    
    // --- Setters (para definir os dados com validação) ---
    
    // O ID geralmente é definido apenas internamente (pelo DAO), então o setter é privado.
    public function setId(int $id): void
    {
        $this->id_usuario = $id;
    }

    public function setNome(string $nome): void
    {
        $this->nome = trim(strip_tags($nome));
    }

    public function setEmail(string $email): void
    {
        $this->email = trim(strip_tags($email));
    }

    /**
     * Define a senha, aplicando o hash.
     * Esta é uma regra de negócio, por isso permanece no modelo.
     */
    public function setSenha(string $senha_pura): void
    {
        if (!empty($senha_pura)) {
            $this->senha_hash = password_hash($senha_pura, PASSWORD_BCRYPT);
        }
    }
    
    // O setter para o hash é útil ao carregar dados do banco.
    public function setSenhaHash(string $hash): void
    {
        $this->senha_hash = $hash;
    }

    public function setTipoAcesso(string $tipo): void
    {
        $this->tipo_acesso = $tipo;
    }

    /**
     * Verifica a senha. Também é uma regra de negócio do usuário.
     */
    public function verificarSenha(string $senha_pura): bool
    {
        if (empty($senha_pura) || empty($this->senha_hash)) {
            return false;
        }
        return password_verify($senha_pura, $this->senha_hash);
    }
}
