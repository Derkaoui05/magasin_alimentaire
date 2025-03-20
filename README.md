# 📌 Gestion d'un Magasin de Produits Alimentaires  

## 🛒 Description  
Ce projet est une **application web en PHP natif** permettant de gérer un magasin de produits alimentaires. L'application offre des fonctionnalités de gestion des stocks, des commandes et des promotions, tout en assurant une expérience utilisateur fluide.  

## 👥 Acteurs du Système  
- **Vendeur** : Gestion des produits, des stocks, des commandes et des promotions.  
- **Client** : Achat de produits, gestion du panier et suivi des commandes.  
- **Administrateur** : Supervision globale et génération de statistiques.  

## 📂 Structure du Projet  
```
food_store_management/
│
├── index.php                  # Main entry point
├── .htaccess                  # URL rewriting rules
│
├── controllers/               # Controller classes
│   ├── AdminController.php    # Admin dashboard and management
│   ├── AuthController.php     # Authentication (login, register, logout)
│   ├── CartController.php     # Shopping cart functionality
│   ├── CategoryController.php # Category management
│   ├── CustomerController.php # Customer dashboard and profile
│   ├── ErrorController.php    # Error handling (404, 403, etc.)
│   ├── HomeController.php     # Home page and static pages
│   ├── OrderController.php    # Order processing and history
│   ├── ProductController.php  # Product listing and management
│   ├── PromotionController.php # Promotion management
│   ├── ReportController.php   # Sales and inventory reports
│   └── VendorController.php   # Vendor dashboard and management
│
├── models/                    # Model classes
│   ├── .env                   # Database credentials
│   ├── Database.php           # Database connection class
│   ├── UserModel.php          # User data operations
│   ├── ProductModel.php       # Product data operations
│   ├── CategoryModel.php      # Category data operations
│   ├── OrderModel.php         # Order data operations
│   ├── OrderItemModel.php     # Order item data operations
│   └── PromotionModel.php     # Promotion data operations
│
├── views/                     # View templates
│   ├── admin/                 # Admin views
│   │   ├── dashboard.php      # Admin dashboard
│   │   ├── users.php          # User management
│   │   ├── edit_user.php      # Edit user form
│   │   └── statistics.php     # Sales statistics
│   │
│   ├── auth/                  # Authentication views
│   │   ├── login.php          # Login form
│   │   └── register.php       # Registration form
│   │
│   ├── cart/                  # Cart views
│   │   ├── index.php          # Shopping cart
│   │   └── checkout.php       # Checkout process
│   │
│   ├── categories/            # Category views
│   │   ├── index.php          # List categories
│   │   ├── create.php         # Create category form
│   │   └── edit.php           # Edit category form
│   │
│   ├── customer/              # Customer views
│   │   ├── dashboard.php      # Customer dashboard
│   │   └── profile.php        # Customer profile
│   │
│   ├── error/                 # Error views
│   │   ├── 404.php            # Not found error
│   │   ├── 403.php            # Unauthorized error
│   │   └── 500.php            # Server error
│   │
│   ├── home/                  # Home views
│   │   ├── index.php          # Home page
│   │   ├── about.php          # About page
│   │   └── contact.php        # Contact page
│   │
│   ├── layout/                # Layout components
│   │   ├── header.php         # Page header
│   │   ├── footer.php         # Page footer
│   │   └── navigation.php     # Navigation menu
│   │
│   ├── orders/                # Order views
│   │   ├── index.php          # List orders
│   │   ├── view.php           # View order details
│   │   ├── history.php        # Order history
│   │   └── invoice.php        # Order invoice
│   │
│   ├── products/              # Product views
│   │   ├── index.php          # List products
│   │   ├── view.php           # View product details
│   │   ├── create.php         # Create product form
│   │   ├── edit.php           # Edit product form
│   │   └── delete.php         # Delete product confirmation
│   │
│   ├── promotions/            # Promotion views
│   │   ├── index.php          # List promotions
│   │   ├── create.php         # Create promotion form
│   │   └── edit.php           # Edit promotion form
│   │
│   ├── reports/               # Report views
│   │   ├── sales.php          # Sales reports
│   │   ├── inventory.php      # Inventory reports
│   │   └── export.php         # Export reports
│   │
│   └── vendor/                # Vendor views
│       ├── dashboard.php      # Vendor dashboard
│       └── profile.php        # Vendor profile
│
├── assets/                    # Static assets
│   ├── css/                   # CSS files
│   │   ├── style.css          # Main stylesheet
│   │   └── admin.css          # Admin panel styles
│   │
│   ├── js/                    # JavaScript files
│   │   ├── main.js            # Main JavaScript
│   │   └── cart.js            # Shopping cart functionality
│   │
│   └── images/                # Image files
│       ├── logo.png           # Site logo
│       └── default-product.jpg # Default product image
│
└── uploads/                   # User uploaded files
    └── products/              # Product images
```

## 🛠️ Technologies Utilisées  
- **Langage** : PHP (sans framework)  
- **Base de données** : MySQL  
- **Front-end** : HTML, CSS, JavaScript  

## ⚙️ Installation  
1. **Cloner le projet**  
   ```bash
   git clone https://github.com/utilisateur/magasin.git
   cd magasin
   ```
2. **Configurer la base de données**  
   - Importer le fichier `database.sql` dans MySQL.  
   - Configurer les accès à la BD dans **`models/.env`**  

3. **Lancer le projet**  
   - Placer le dossier dans `htdocs/` (si vous utilisez XAMPP)  
   - Démarrer Apache et MySQL  
   - Accéder au projet via :  
     ```
     http://localhost/magasin
     ```

## 📈 Fonctionnalités Principales  
✅ Authentification sécurisée (vendeur, client, admin)  
✅ Gestion des produits et des stocks  
✅ Commandes et facturation  
✅ Génération de rapports et statistiques  

---

🎯 **Développé par [derkaoui yassir]** | 🚀 **Projet académique**  

