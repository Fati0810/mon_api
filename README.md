# 📦 API Utilisateurs – PHP & MySQL

Cette API permet de récupérer la liste des utilisateurs depuis une base de données MySQL, avec un affichage HTML sous forme de tableau et un endpoint JSON.

---

## 🚀 Fonctionnalités

- Récupération des données utilisateur (prénom, nom, email, etc.)
- Affichage en tableau HTML
- Endpoint API REST au format JSON
- Sécurisation des identifiants avec un fichier `.env`
+ Structure MVC simple : séparation logique / vue / données

---

## 🛠️ Installation

1. **Clone le dépôt :**

```bash
git clone https://github.com/Fati0810/mon_api
cd mon_api

// mettr een forme
installer composer
composer install

installer les dépendances 
composer require vlucas/phpdotenv
composer require firebase/php-jwt
