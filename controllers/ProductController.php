<?php
require_once 'models/ProductModel.php';
require_once 'models/CategoryModel.php';
require_once 'models/PromotionModel.php';

class ProductController {
    private $productModel;
    private $categoryModel;
    private $promotionModel;
    
    public function __construct() {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->promotionModel = new PromotionModel();
    }
    
    public function index() {
        $category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
        $search = isset($_GET['search']) ? $_GET['search'] : null;
        
        // Get products with optional filters
        $products = $this->productModel->getAll(null, $category_id, $search);
        
        // Get all categories for filter
        $categories = $this->categoryModel->getAll();
        
        // Load the products view
        include 'views/products/index.php';
    }
    
    public function view($id) {
        // Get product details
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            header('Location: /error/notFound');
            exit;
        }
        
        // Load the product details view
        include 'views/products/view.php';
    }
    
    public function create() {
        // Check if user is vendor or admin
        if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'vendor' && $_SESSION['user_role'] !== 'admin')) {
            header('Location: /error/unauthorized');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Process form submission
            $name = $this->sanitizeInput($_POST['name']);
            $description = $this->sanitizeInput($_POST['description']);
            $price = (float)$_POST['price'];
            $stock_quantity = (int)$_POST['stock_quantity'];
            $category_id = (int)$_POST['category_id'];
            $expiry_date = $_POST['expiry_date'] ? $_POST['expiry_date'] : null;
            $promotion_id = !empty($_POST['promotion_id']) ? (int)$_POST['promotion_id'] : null;
            
            // Handle image upload
            $image_url = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/products/';
                
                // Create directory if it doesn't exist
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_name = time() . '_' . basename($_FILES['image']['name']);
                $target_file = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $image_url = $target_file;
                }
            }
            
            // Generate unique reference
            $reference = $this->productModel->generateReference();
            
            $productData = [
                'reference' => $reference,
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'stock_quantity' => $stock_quantity,
                'category_id' => $category_id,
                'expiry_date' => $expiry_date,
                'promotion_id' => $promotion_id,
                'image_url' => $image_url
            ];
            
            $product_id = $this->productModel->create($productData);
            
            if ($product_id) {
                header('Location: /product/view/' . $product_id);
                exit;
            } else {
                $error = "Failed to create product";
            }
        }
        
        // Get categories and promotions for form
        $categories = $this->categoryModel->getAll();
        $promotions = $this->promotionModel->getActive();
        
        // Load the create product view
        include 'views/products/create.php';
    }
    
    public function edit($id) {
        // Check if user is vendor or admin
        if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'vendor' && $_SESSION['user_role'] !== 'admin')) {
            header('Location: /error/unauthorized');
            exit;
        }
        
        // Get product details
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            header('Location: /error/notFound');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Process form submission
            $name = $this->sanitizeInput($_POST['name']);
            $description = $this->sanitizeInput($_POST['description']);
            $price = (float)$_POST['price'];
            $stock_quantity = (int)$_POST['stock_quantity'];
            $category_id = (int)$_POST['category_id'];
            $expiry_date = $_POST['expiry_date'] ? $_POST['expiry_date'] : null;
            $promotion_id = !empty($_POST['promotion_id']) ? (int)$_POST['promotion_id'] : null;
            
            // Handle image upload
            $image_url = $product['image_url'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/products/';
                
                // Create directory if it doesn't exist
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_name = time() . '_' . basename($_FILES['image']['name']);
                $target_file = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $image_url = $target_file;
                }
            }
            
            $productData = [
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'stock_quantity' => $stock_quantity,
                'category_id' => $category_id,
                'expiry_date' => $expiry_date,
                'promotion_id' => $promotion_id,
                'image_url' => $image_url
            ];
            
            $result = $this->productModel->update($id, $productData);
            
            if ($result) {
                header('Location: /product/view/' . $id);
                exit;
            } else {
                $error = "Failed to update product";
            }
        }
        
        // Get categories and promotions for form
        $categories = $this->categoryModel->getAll();
        $promotions = $this->promotionModel->getActive();
        
        // Load the edit product view
        include 'views/products/edit.php';
    }
    
    public function delete($id) {
        // Check if user is vendor or admin
        if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'vendor' && $_SESSION['user_role'] !== 'admin')) {
            header('Location: /error/unauthorized');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->productModel->delete($id);
            
            if ($result) {
                header('Location: /product');
                exit;
            } else {
                $error = "Failed to delete product";
            }
        }
        
        // Get product details
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            header('Location: /error/notFound');
            exit;
        }
        
        // Load the delete confirmation view
        include 'views/products/delete.php';
    }
    
    private function sanitizeInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
}

