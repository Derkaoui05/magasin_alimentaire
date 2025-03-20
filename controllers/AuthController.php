<?php
require_once 'models/UserModel.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $this->sanitizeInput($_POST['username']);
            $password = $_POST['password'];
            
            $user = $this->userModel->getByUsername($username);
            
            if ($user && password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = $user['role'];
                
                // Redirect based on role
                switch ($user['role']) {
                    case 'admin':
                        header('Location: /admin/dashboard');
                        break;
                    case 'vendor':
                        header('Location: /vendor/dashboard');
                        break;
                    case 'customer':
                        header('Location: /customer/dashboard');
                        break;
                }
                exit;
            } else {
                $error = "Invalid username or password";
            }
        }
        
        // Load the login view
        include 'views/auth/login.php';
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $this->sanitizeInput($_POST['username']);
            $email = $this->sanitizeInput($_POST['email']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            $phone = $this->sanitizeInput($_POST['phone'] ?? '');
            $address = $this->sanitizeInput($_POST['address'] ?? '');
            
            // Validate input
            $errors = [];
            
            if (empty($username)) {
                $errors[] = "Username is required";
            } elseif ($this->userModel->getByUsername($username)) {
                $errors[] = "Username already exists";
            }
            
            if (empty($email)) {
                $errors[] = "Email is required";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format";
            } elseif ($this->userModel->getByEmail($email)) {
                $errors[] = "Email already exists";
            }
            
            if (empty($password)) {
                $errors[] = "Password is required";
            } elseif (strlen($password) < 6) {
                $errors[] = "Password must be at least 6 characters";
            }
            
            if ($password !== $confirm_password) {
                $errors[] = "Passwords do not match";
            }
            
            if (empty($errors)) {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Create user
                $userData = [
                    'username' => $username,
                    'password' => $hashed_password,
                    'email' => $email,
                    'phone' => $phone,
                    'address' => $address,
                    'role' => 'customer' // Default role for registration
                ];
                
                $user_id = $this->userModel->create($userData);
                
                if ($user_id) {
                    // Set session variables
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $username;
                    $_SESSION['user_role'] = 'customer';
                    
                    // Redirect to customer dashboard
                    header('Location: /customer/dashboard');
                    exit;
                } else {
                    $errors[] = "Registration failed";
                }
            }
        }
        
        // Load the register view
        include 'views/auth/register.php';
    }
    
    public function logout() {
        // Destroy session
        session_unset();
        session_destroy();
        
        // Redirect to home
        header('Location: /');
        exit;
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function getUserRole() {
        return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
    }
    
    public function getUserId() {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }
    
    private function sanitizeInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
}

