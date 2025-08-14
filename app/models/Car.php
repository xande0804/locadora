<?php

class Car {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // MÉTODOS DE BUSCA E LISTAGEM
    public function findPaginated($limit, $offset, $searchTerm = null) {
        try {
            $sql = "SELECT c.*, m.nome as modelo_nome, ma.nome as marca_nome 
                    FROM carros c
                    JOIN modelos m ON c.modelo_id = m.id
                    JOIN marcas ma ON m.marca_id = ma.id";
            $params = [];
            if ($searchTerm) {
                $sql .= " WHERE m.nome LIKE :searchTerm OR ma.nome LIKE :searchTerm";
                $params[':searchTerm'] = '%' . $searchTerm . '%';
            }
            $sql .= " ORDER BY ma.nome, m.nome ASC LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => &$val) { $stmt->bindParam($key, $val); }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }

    public function findById($id) {
        try {
            $sql = "SELECT c.*, m.nome as modelo_nome, ma.nome as marca_nome, m.marca_id
                    FROM carros c
                    JOIN modelos m ON c.modelo_id = m.id
                    JOIN marcas ma ON m.marca_id = ma.id
                    WHERE c.id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) { return false; }
    }

    public function findAllAvailable() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM carros WHERE quantidade_disponivel > 0 ORDER BY modelo_id ASC");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }

    public function countAll($searchTerm = null) {
        try {
            $sql = "SELECT COUNT(c.id) as total 
                    FROM carros c
                    LEFT JOIN modelos m ON c.modelo_id = m.id
                    LEFT JOIN marcas ma ON m.marca_id = ma.id";
            $params = [];
            if ($searchTerm) {
                $sql .= " WHERE m.nome LIKE :searchTerm OR ma.nome LIKE :searchTerm";
                $params[':searchTerm'] = '%' . $searchTerm . '%';
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch()['total'];
        } catch (PDOException $e) { return 0; }
    }

    // MÉTODOS PARA O FORMULÁRIO DINÂMICO
    public function findAllBrands() {
        try {
            $stmt = $this->db->query("SELECT * FROM marcas ORDER BY nome ASC");
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }

    public function findModelsByBrandId($brandId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM modelos WHERE marca_id = :brand_id ORDER BY nome ASC");
            $stmt->execute([':brand_id' => $brandId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }
    
    public function searchBrandsByName($term) {
        try {
            $sql = "SELECT id, nome FROM marcas WHERE nome LIKE :term ORDER BY 
                    CASE
                        WHEN nome LIKE :exact_term THEN 1
                        WHEN nome LIKE :start_term THEN 2
                        ELSE 3
                    END, nome ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':term' => '%' . $term . '%',
                ':exact_term' => $term,
                ':start_term' => $term . '%'
            ]);
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }

    public function searchModelsByName($brandId, $term) {
        try {
            $sql = "SELECT id, nome FROM modelos 
                    WHERE marca_id = :brand_id AND nome LIKE :term 
                    ORDER BY 
                    CASE
                        WHEN nome LIKE :exact_term THEN 1
                        WHEN nome LIKE :start_term THEN 2
                        ELSE 3
                    END, nome ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':brand_id' => $brandId,
                ':term' => '%' . $term . '%',
                ':exact_term' => $term,
                ':start_term' => $term . '%'
            ]);
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }

    // MÉTODOS DE MANIPULAÇÃO DE DADOS (CRUD)
    public function create($modelo_id, $ano, $preco_diaria, $preco_semanal, $preco_mensal, $quantidade) {
        try {
            $sql = "INSERT INTO carros (modelo_id, ano, preco_diaria, preco_semanal, preco_mensal, quantidade_disponivel) 
                    VALUES (:modelo_id, :ano, :preco_diaria, :preco_semanal, :preco_mensal, :quantidade)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':modelo_id' => $modelo_id,
                ':ano' => $ano,
                ':preco_diaria' => $preco_diaria,
                ':preco_semanal' => !empty($preco_semanal) ? $preco_semanal : null,
                ':preco_mensal' => !empty($preco_mensal) ? $preco_mensal : null,
                ':quantidade' => $quantidade
            ]);
        } catch (PDOException $e) { return false; }
    }

    public function update($id, $modelo_id, $ano, $preco_diaria, $preco_semanal, $preco_mensal, $quantidade) {
        try {
            $sql = "UPDATE carros SET 
                        modelo_id = :modelo_id, 
                        ano = :ano, 
                        preco_diaria = :preco_diaria, 
                        preco_semanal = :preco_semanal, 
                        preco_mensal = :preco_mensal, 
                        quantidade_disponivel = :quantidade 
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                ':id' => $id,
                ':modelo_id' => $modelo_id,
                ':ano' => $ano,
                ':preco_diaria' => $preco_diaria,
                ':preco_semanal' => !empty($preco_semanal) ? $preco_semanal : null,
                ':preco_mensal' => !empty($preco_mensal) ? $preco_mensal : null,
                ':quantidade' => $quantidade
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM carros WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return true;
        } catch (PDOException $e) { return false; }
    }
}
?>