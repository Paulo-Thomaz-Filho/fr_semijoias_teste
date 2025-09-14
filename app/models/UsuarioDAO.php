<?php

namespace App\Models;

// Supondo que suas classes do core estarão nesses namespaces
use App\Core\Database\DBQuery;
use App\Core\Database\Where;

/**
 * Classe DAO (Data Access Object) para a entidade Usuario.
 *
 * Sua única responsabilidade é ser a PONTE entre a aplicação
 * e a tabela 'usuarios' no banco de dados.
 */
class UsuarioDAO
{
    private DBQuery $dbQuery;

    public function __construct()
    {
        // Configura o DBQuery para trabalhar com a tabela 'usuarios'
        $tableName = 'Usuarios';
        $fields = 'id_usuario, nome, email, senha_hash, tipo_acesso';
        $primaryKey = 'id_usuario';
        
        $this->dbQuery = new DBQuery($tableName, $fields, $primaryKey);
    }

    /**
     * Converte uma linha de dados do banco em um objeto Usuario.
     * @param array $row Dados vindos do banco.
     * @return Usuario Objeto preenchido.
     */
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

    /**
     * Busca um usuário pelo seu ID.
     * @return Usuario|null Retorna o objeto Usuario ou null se não encontrar.
     */
    public function findById(int $id): ?Usuario
    {
        $where = new Where();
        $where->add('id_usuario', '=', $id);
        
        $result = $this->dbQuery->selectFiltered($where);

        return $result ? $this->hydrate($result[0]) : null;
    }
    
    /**
     * Busca um usuário pelo seu e-mail.
     * @return Usuario|null Retorna o objeto Usuario ou null se não encontrar.
     */
    public function findByEmail(string $email): ?Usuario
    {
        $where = new Where();
        $where->add('email', '=', $email);
        
        $result = $this->dbQuery->selectFiltered($where);

        return $result ? $this->hydrate($result[0]) : null;
    }
    
    /**
     * Busca todos os usuários.
     * @return array Retorna um array de objetos Usuario.
     */
    public function findAll(): array
    {
        $results = [];
        $data = $this->dbQuery->select();
        
        foreach ($data as $row) {
            $results[] = $this->hydrate($row);
        }
        
        return $results;
    }

    /**
     * Salva um usuário no banco (decide entre inserir ou atualizar).
     * @param Usuario $usuario O objeto a ser salvo.
     * @return bool True se sucesso, false se falha.
     */
    public function save(Usuario &$usuario): bool
    {
        // Se o objeto já tem um ID, é uma atualização
        if ($usuario->getId()) {
            return $this->update($usuario);
        }
        // Se não, é uma inserção
        return $this->insert($usuario);
    }
    
    /**
     * Insere um novo usuário no banco de dados.
     */
    public function insert(Usuario &$usuario): bool
    {
        $data = [
            'nome' => $usuario->getNome(),
            'email' => $usuario->getEmail(),
            'senha_hash' => $usuario->getSenhaHash(),
            'tipo_acesso' => $usuario->getTipoAcesso()
        ];
        
        $newId = $this->dbQuery->insert($data);
        
        if ($newId) {
            $usuario->setId($newId); // Atualiza o objeto com o novo ID
            return true;
        }
        return false;
    }
    
    /**
     * Atualiza um usuário existente no banco de dados.
     */
    public function update(Usuario $usuario): bool
    {
        $data = [
            'nome' => $usuario->getNome(),
            'email' => $usuario->getEmail(),
            'senha_hash' => $usuario->getSenhaHash(),
            'tipo_acesso' => $usuario->getTipoAcesso()
        ];
        
        $where = new Where();
        $where->add('id_usuario', '=', $usuario->getId());

        return $this->dbQuery->update($data, $where);
    }

    /**
     * Deleta um usuário pelo ID.
     */
    public function delete(int $id): bool
    {
        $where = new Where();
        $where->add('id_usuario', '=', $id);

        return $this->dbQuery->delete($where);
    }
}
