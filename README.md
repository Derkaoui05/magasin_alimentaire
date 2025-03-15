# 📌 Gestion d'un Magasin de Produits Alimentaires  

## 🛒 Description  
Ce projet est une **application web en PHP natif** permettant de gérer un magasin de produits alimentaires. L'application offre des fonctionnalités de gestion des stocks, des commandes et des promotions, tout en assurant une expérience utilisateur fluide.  

## 👥 Acteurs du Système  
- **Vendeur** : Gestion des produits, des stocks, des commandes et des promotions.  
- **Client** : Achat de produits, gestion du panier et suivi des commandes.  
- **Administrateur** : Supervision globale et génération de statistiques.  

## 📂 Structure du Projet  
```
magasin/
│── controllers/       # Contrôleurs pour gérer les actions
│── models/           # Modèles de données et connexion à la BD
│   │── .env         # Configuration des variables d'environnement
│── views/            # Interfaces utilisateur (Vendeur, Client, Admin, Auth)
│   │── vendeur/     # Vues pour les vendeurs
│   │── client/      # Vues pour les clients
│   │── admin/       # Vues pour l'administrateur
│── public/           # Fichiers CSS, JS et images
│── .htaccess         # Configuration des routes
│── index.php         # Point d'entrée principal
│── README.md         # Documentation du projet
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

