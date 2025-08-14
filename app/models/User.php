<?php

class User {
    private $db;

    public function __construct() {
        // Pega a conexão do nosso singleton
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Encontra um usuário pelo seu e-mail.
     * Retorna os dados do usuário ou false se não encontrar.
     */
    public function findByEmail($email) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = :email");
            $stmt->execute([':email' => $email]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function create($nome, $email, $senha, $tipo = 'comun') { // CORREÇÃO AQUI
        if ($this->findByEmail($email)) {
            return false;
        }
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO usuarios (nome, email, senha, tipo) VALUES (:nome, :email, :senha, :tipo)"
            );
            $stmt->execute([
                ':nome' => $nome,
                ':email' => $email,
                ':senha' => $senhaHash,
                ':tipo' => $tipo
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Busca todos os usuários cadastrados no sistema.
     */
    public function findAll() {
        try {
            $stmt = $this->db->prepare("SELECT id, nome, email, tipo FROM usuarios ORDER BY tipo, nome ASC");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Busca um usuário pelo seu ID.
     * @param int $id O ID do usuário.
     * @return mixed Array com dados do usuário ou false se não encontrar.
     */
    public function findById($id) {
        try {
            $stmt = $this->db->prepare("SELECT id, nome, email, tipo FROM usuarios WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Atualiza os dados de um usuário no banco.
     */
    public function update($id, $data) {
        try {
            $stmt = $this->db->prepare(
                "UPDATE usuarios SET nome = :nome, email = :email, tipo = :tipo WHERE id = :id"
            );
            
            $params = [
                ':id' => $id,
                ':nome' => $data['nome'],
                ':email' => $data['email'],
                ':tipo' => $data['tipo']
            ];
            // O campo 'sexo' foi removido desta query pois não está mais na tabela 'usuarios'

            return $stmt->execute($params);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Deleta um usuário do banco de dados pelo seu ID.
     * @param int $id O ID do usuário a ser deletado.
     * @return bool Verdadeiro em caso de sucesso, falso em caso de falha.
     */
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Conta o número total de usuários, opcionalmente filtrando por nome ou email.
     */
    public function countAll($searchTerm = null) {
        try {
            $sql = "SELECT COUNT(u.id) as total FROM usuarios u";
            $params = [];
            if ($searchTerm) {
                $sql .= " WHERE u.nome LIKE :searchTerm OR u.email LIKE :searchTerm";
                $params[':searchTerm'] = '%' . $searchTerm . '%';
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch()['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Busca usuários com paginação e busca, e determina o status (Cliente/Comum).
     */
    public function findPaginated($limit, $offset, $searchTerm = null) {
        try {
            $sql = "SELECT u.id, u.nome, u.email, u.tipo, c.usuario_id as cliente_id
                    FROM usuarios u
                    LEFT JOIN clientes c ON u.id = c.usuario_id";
            $params = [];

            if ($searchTerm) {
                $sql .= " WHERE u.nome LIKE :searchTerm OR u.email LIKE :searchTerm";
                $params[':searchTerm'] = '%' . $searchTerm . '%';
            }

            $sql .= " ORDER BY u.tipo, u.nome ASC LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            
            foreach ($params as $key => &$val) {
                $stmt->bindParam($key, $val);
            }

            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>