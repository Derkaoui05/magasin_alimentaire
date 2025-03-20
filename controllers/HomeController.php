<?php
require_once 'models/ProductModel.php';
require_once 'models/CategoryModel.php';

class HomeController {
    private $productModel;
    private $categoryModel;
    
    public function __construct() {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
    }
    
    public function index() {
        // Get featured products
        $featuredProducts = $this->productModel->getAll(8);
        
        // Get all categories
        $categories = $this->categoryModel->getAll();
        
        // Load the home view
        include 'views/home/index.php';
    }
    
    public function about() {
        include 'views/home/about.php';
    }
    
    public function contact() {
        include 'views/home/contact.php';
    }
}

