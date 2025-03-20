<?php
require_once 'Database.php';

class ProductModel {
    private $db;
    
    public function __construct() {
        $this->db = Connection::Connect();
    }
    
    public function getAll($limit = null, $category = null, $search = null) {
        $query = "SELECT p.*, c.name as category_name, 
                 CASE 
                     WHEN pr.discount_type = 'percentage' THEN p.price * (1 - pr.discount_value/100)
                     WHEN pr.discount_type = 'fixed' THEN p.price - pr.discount_value
                     ELSE p.price
                 END as discounted_price,
                 pr.discount_value, pr.discount_type
                 FROM products p
                 LEFT JOIN categories c ON p.category_id = c.id
                 LEFT JOIN promotions pr ON p.promotion_id = pr.id
                 WHERE 1=1";
        
        $params = [];
        
        if ($category) {
            $query .= " AND p.category_id = :category";
            $params[':category'] = $category;
        }
        
        if ($search) {
            $query .= " AND (p.name LIKE :search OR p.description LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        $query .= " ORDER BY p.created_at DESC";
        
        if ($limit) {
            $query .= " LIMIT :limit";
            $params[':limit'] = $limit;
        }
        
        $stmt = $this->db->prepare($query);
        
        foreach ($params as $key => $value) {
            if ($key === ':limit' || $key === ':category') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $query = "SELECT p.*, c.name as category_name, 
                 CASE 
                     WHEN pr.discount_type = 'percentage' THEN p.price * (1 - pr.discount_value/100)
                     WHEN pr.discount_type = 'fixed' THEN p.price - pr.discount_value
                     ELSE p.price
                 END as discounted_price,
                 pr.discount_value, pr.discount_type
                 FROM products p
                 LEFT JOIN categories c ON p.category_id = c.id
                 LEFT JOIN promotions pr ON p.promotion_id = pr.id
                 WHERE p.id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $query = "INSERT INTO products (reference, name, description, price, stock_quantity, 
                  category_id, expiry_date, promotion_id, image_url) 
                  VALUES (:reference, :name, :description, :price, :stock_quantity, 
                  :category_id, :expiry_date, :promotion_id, :image_url)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':reference', $data['reference'], PDO::PARAM_STR);
        $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
        $stmt->bindParam(':price', $data['price'], PDO::PARAM_STR);
        $stmt->bindParam(':stock_quantity', $data['stock_quantity'], PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $data['category_id'], PDO::PARAM_INT);
        $stmt->bindParam(':expiry_date', $data['expiry_date'], PDO::PARAM_STR);
        $stmt->bindParam(':promotion_id', $data['promotion_id'], PDO::PARAM_INT);
        $stmt->bindParam(':image_url', $data['image_url'], PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    public function update($id, $data) {
        $query = "UPDATE products SET 
                  name = :name,
                  description = :description,
                  price = :price,
                  stock_quantity = :stock_quantity,
                  category_id = :category_id,
                  expiry_date = :expiry_date,
                  promotion_id = :promotion_id,
                  image_url = :image_url
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
        $stmt->bindParam(':price', $data['price'], PDO::PARAM_STR);
        $stmt->bindParam(':stock_quantity', $data['stock_quantity'], PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $data['category_id'], PDO::PARAM_INT);
        $stmt->bindParam(':expiry_date', $data['expiry_date'], PDO::PARAM_STR);
        $stmt->bindParam(':promotion_id', $data['promotion_id'], PDO::PARAM_INT);
        $stmt->bindParam(':image_url', $data['image_url'], PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    public function updateStock($id, $quantity) {
        $query = "UPDATE products SET stock_quantity = stock_quantity - :quantity WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    public function delete($id) {
        $query = "DELETE FROM products WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    public function getLowStock($threshold = 10) {
        $query = "SELECT p.*, c.name as category_name
                 FROM products p
                 LEFT JOIN categories c ON p.category_id = c.id
                 WHERE p.stock_quantity <= :threshold
                 ORDER BY p.stock_quantity ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':threshold', $threshold, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getExpiring($days = 7) {
        $query = "SELECT p.*, c.name as category_name, 
                 DATEDIFF(p.expiry_date, CURDATE()) as days_remaining
                 FROM products p
                 LEFT JOIN categories c ON p.category_id = c.id
                 WHERE p.expiry_date IS NOT NULL 
                 AND p.expiry_date <= DATE_ADD(CURDATE(), INTERVAL :days DAY)
                 AND p.expiry_date >= CURDATE()
                 ORDER BY p.expiry_date ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTopSelling($limit = 5, $period = 'month') {
        $date_condition = "";
        
        switch ($period) {
            case 'day':
                $date_condition = "DATE(o.created_at) = CURDATE()";
                break;
            case 'week':
                $date_condition = "DATE(o.created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                break;
            case 'month':
            default:
                $date_condition = "DATE(o.created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
                break;
        }
        
        $query = "SELECT p.id, p.name, p.reference, SUM(oi.quantity) as total_quantity, 
                 SUM(oi.quantity * oi.price) as total_sales
                 FROM order_items oi
                 JOIN products p ON oi.product_id = p.id
                 JOIN orders o ON oi.order_id = o.id
                 WHERE $date_condition AND o.status != 'cancelled'
                 GROUP BY p.id
                 ORDER BY total_quantity DESC
                 LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function countAll() {
        $query = "SELECT COUNT(*) as count FROM products";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'];
    }
    
    public function generateReference() {
        return 'PROD-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
    }
}

