<?php
require_once 'Database.php';

class OrderModel {
    private $db;
    
    public function __construct() {
        $this->db = Connection::Connect();
    }
    
    public function getAll($status = null) {
        $query = "SELECT o.*, u.username, u.email, u.phone 
                 FROM orders o
                 JOIN users u ON o.user_id = u.id";
        
        $params = [];
        
        if ($status) {
            $query .= " WHERE o.status = :status";
            $params[':status'] = $status;
        }
        
        $query .= " ORDER BY o.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $query = "SELECT o.*, u.username, u.email, u.phone, u.address 
                 FROM orders o
                 JOIN users u ON o.user_id = u.id
                 WHERE o.id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getByUser($user_id) {
        $query = "SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $query = "INSERT INTO orders (user_id, total_amount, status, delivery_type, delivery_date) 
                  VALUES (:user_id, :total_amount, :status, :delivery_type, :delivery_date)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':total_amount', $data['total_amount'], PDO::PARAM_STR);
        $stmt->bindParam(':status', $data['status'], PDO::PARAM_STR);
        $stmt->bindParam(':delivery_type', $data['delivery_type'], PDO::PARAM_STR);
        $stmt->bindParam(':delivery_date', $data['delivery_date'], PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    public function addOrderItem($data) {
        $query = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                  VALUES (:order_id, :product_id, :quantity, :price)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':order_id', $data['order_id'], PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $data['product_id'], PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $data['quantity'], PDO::PARAM_INT);
        $stmt->bindParam(':price', $data['price'], PDO::PARAM_STR);
        
        return $stmt->execute();
    }
    
    public function getOrderItems($order_id) {
        $query = "SELECT oi.*, p.name, p.reference 
                 FROM order_items oi
                 JOIN products p ON oi.product_id = p.id
                 WHERE oi.order_id = :order_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateStatus($id, $status) {
        $query = "UPDATE orders SET status = :status WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    public function getDailySales($date) {
        $query = "SELECT SUM(total_amount) as total, COUNT(*) as count 
                 FROM orders 
                 WHERE DATE(created_at) = :date AND status != 'cancelled'";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result['total']) {
            $result['total'] = 0;
        }
        
        return $result;
    }
    
    public function getWeeklySales($start_date, $end_date) {
        $query = "SELECT DATE(created_at) as date, SUM(total_amount) as total, COUNT(*) as count 
                 FROM orders 
                 WHERE DATE(created_at) BETWEEN :start_date AND :end_date AND status != 'cancelled'
                 GROUP BY DATE(created_at)
                 ORDER BY DATE(created_at)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
        $stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getMonthlySales($year, $month) {
        $query = "SELECT SUM(total_amount) as total, COUNT(*) as count 
                 FROM orders 
                 WHERE YEAR(created_at) = :year AND MONTH(created_at) = :month AND status != 'cancelled'";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->bindParam(':month', $month, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result['total']) {
            $result['total'] = 0;
        }
        
        return $result;
    }
    
    public function countPendingOrders() {
        $query = "SELECT COUNT(*) as count FROM orders WHERE status = 'pending'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'];
    }
    
    public function countOrdersByStatus() {
        $query = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

