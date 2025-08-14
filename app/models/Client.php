<?php

class Client {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Verifica se um usuário já tem um perfil de cliente.
     * @param int $userId O ID do usuário.
     * @return bool Verdadeiro se o perfil de cliente existe, falso caso contrário.
     */
    public function findByUserId($userId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM clientes WHERE usuario_id = :user_id");
            $stmt->execute([':user_id' => $userId]);
            // fetch() retorna false se não encontrar nada
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Cria o perfil de cliente para um usuário.
     * @param array $data Os dados do formulário.
     * @return bool Verdadeiro em caso de sucesso, falso em caso de falha.
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO clientes (usuario_id, cpf, cnh, data_nascimento, telefone, endereco, sexo) 
                    VALUES (:usuario_id, :cpf, :cnh, :data_nascimento, :telefone, :endereco, :sexo)";
            
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                ':usuario_id' => $data['usuario_id'],
                ':cpf' => $data['cpf'],
                ':cnh' => $data['cnh'],
                ':data_nascimento' => $data['data_nascimento'],
                ':telefone' => $data['telefone'],
                ':endereco' => $data['endereco'],
                ':sexo' => $data['sexo']
            ]);
        } catch (PDOException $e) {
            // Pode falhar por CPF/CNH duplicado
            return false;
        }
    }
}
?>