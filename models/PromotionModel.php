<?php
require_once 'Database.php';

class PromotionModel {
    private $db;
    
    public function __construct() {
        $this->db = Connection::Connect();
    }
    
    public function getAll() {
        $query = "SELECT * FROM promotions ORDER BY end_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getActive() {
        $query = "SELECT * FROM promotions WHERE end_date >= CURDATE() ORDER BY end_date";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $query = "SELECT * FROM promotions WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $query = "INSERT INTO promotions (name, discount_type, discount_value, start_date, end_date) 
                  VALUES (:name, :discount_type, :discount_value, :start_date, :end_date)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(':discount_type', $data['discount_type'], PDO::PARAM_STR);
        $stmt->bindParam(':discount_value', $data['discount_value'], PDO::PARAM_STR);
        $stmt->bindParam(':start_date', $data['start_date'], PDO::PARAM_STR);
        $stmt->bindParam(':end_date', $data['end_date'], PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    public function update($id, $data) {
        $query = "UPDATE promotions SET 
                  name = :name, 
                  discount_type = :discount_type, 
                  discount_value = :discount_value, 
                  start_date = :start_date, 
                  end_date = :end_date 
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(':discount_type', $data['discount_type'], PDO::PARAM_STR);
        $stmt->bindParam(':discount_value', $data['discount_value'], PDO::PARAM_STR);
        $stmt->bindParam(':start_date', $data['start_date'], PDO::PARAM_STR);
        $stmt->bindParam(':end_date', $data['end_date'], PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    public function delete($id) {
        $query = "DELETE FROM promotions WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    public function getProductCount($promotion_id) {
        $query = "SELECT COUNT(*) as count FROM products WHERE promotion_id = :promotion_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':promotion_id', $promotion_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'];
    }
}

