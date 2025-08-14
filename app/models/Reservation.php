<?php

class Reservation {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Verifica se um carro está disponível num dado período.
     */
    public function isCarAvailable($carId, $startDate, $endDate) {
        try {
            $sql = "SELECT COUNT(*) as count FROM reservas
                    WHERE id_carro = :car_id
                    AND status = 'confirmada'
                    AND (data_inicio < :end_date AND data_fim > :start_date)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':car_id' => $carId,
                ':start_date' => $startDate,
                ':end_date' => $endDate
            ]);
            return $stmt->fetch()['count'] == 0;
        } catch (PDOException $e) { 
            return false; 
        }
    }

    /**
     * Cria um novo registo de reserva na base de dados.
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO reservas (id_cliente, id_carro, data_inicio, data_fim, preco_total, status)
                    VALUES (:id_cliente, :id_carro, :data_inicio, :data_fim, :preco_total, 'confirmada')";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id_cliente' => $data['id_cliente'],
                ':id_carro' => $data['id_carro'],
                ':data_inicio' => $data['data_inicio'],
                ':data_fim' => $data['data_fim'],
                ':preco_total' => $data['preco_total']
            ]);
        } catch (PDOException $e) { 
            return false; 
        }
    }

    /**
     * Busca o histórico de reservas de um cliente.
     */
    public function findHistoryByClient($clientId) {
        try {
            $sql = "SELECT r.*, c.modelo 
                    FROM reservas r 
                    JOIN carros c ON r.id_carro = c.id
                    WHERE r.id_cliente = :client_id
                    ORDER BY r.data_inicio DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':client_id' => $clientId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) { 
            return []; 
        }
    }

    /**
     * Cancela uma reserva (muda o status para 'cancelada').
     */
    public function cancelById($reservationId, $clientId) {
        try {
            // A condição id_cliente garante que um utilizador só pode cancelar as suas próprias reservas
            $sql = "UPDATE reservas SET status = 'cancelada' 
                    WHERE id = :id AND id_cliente = :client_id AND status = 'confirmada'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $reservationId, ':client_id' => $clientId]);
            // rowCount() retorna 1 se a atualização foi bem-sucedida, 0 caso contrário
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>