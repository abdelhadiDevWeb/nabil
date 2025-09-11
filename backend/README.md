# SirteRH - Système de Gestion RH ANPT

Ce projet est une migration du backend serverless Hono vers Laravel, tout en conservant les fonctionnalités et la logique métier existantes.

## Architecture

Le projet est organisé en deux parties principales:

1. **Frontend** - Application React existante
2. **Backend** - API Laravel avec base de données PostgreSQL

### Structure du Backend

- **App/Models** - Modèles Eloquent pour toutes les entités du système
- **App/Http/Controllers** - Contrôleurs API pour chaque groupe fonctionnel
- **App/Http/Middleware** - Middleware d'authentification et contrôle d'accès
- **Database/migrations** - Migrations pour créer le schéma de base de données PostgreSQL
- **Routes/api.php** - Routes API avec les mêmes endpoints que le système Hono

## Configuration requise

- PHP 8.1 ou supérieur
- PostgreSQL 12 ou supérieur
- Composer
- Node.js et NPM (pour le frontend)

## Installation

### Backend

1. Cloner le dépôt
2. Naviguer vers le dossier backend: `cd backend`
3. Installer les dépendances: `composer install`
4. Copier le fichier d'environnement: `cp .env.example .env`
5. Configurer les variables d'environnement, notamment la connexion à la base de données PostgreSQL
6. Générer la clé d'application: `php artisan key:generate`
7. Exécuter les migrations: `php artisan migrate`
8. Démarrer le serveur de développement: `php artisan serve`

### Frontend

1. Naviguer vers le dossier frontend: `cd frontend`
2. Installer les dépendances: `npm install`
3. Démarrer le serveur de développement: `npm run dev`

## Déploiement

### Préparation du VPS

1. Configurer un serveur web (Apache ou Nginx)
2. Installer PHP 8.1+ et les extensions nécessaires
3. Installer PostgreSQL 12+
4. Configurer les certificats SSL pour HTTPS

### Déploiement du Backend

1. Cloner le dépôt sur le serveur
2. Configurer `.env` avec les paramètres de production
3. Installer les dépendances: `composer install --optimize-autoloader --no-dev`
4. Générer la clé d'application: `php artisan key:generate`
5. Exécuter les migrations: `php artisan migrate`
6. Configurer la propriété des fichiers et les permissions
7. Configurer le serveur web pour pointer vers le dossier `public`

### Déploiement du Frontend

1. Construire l'application: `npm run build`
2. Déployer les fichiers de build sur le serveur web

## Sécurité

- Toutes les requêtes API sont protégées par des middlewares d'authentification
- Validation des données entrantes pour prévenir les injections SQL et XSS
- Protection CSRF pour les requêtes non-API
- Stockage sécurisé des mots de passe avec hachage

## Points d'API

Les routes d'API conservent la même structure que dans le backend Hono:

- `/api/login` - Authentification par login/mot de passe
- `/api/users/me` - Information sur l'utilisateur courant
- `/api/logout` - Déconnexion
- `/api/admin/...` - Routes accessibles aux administrateurs
- `/api/employee/...` - Routes accessibles aux employés

## Auteurs et maintenance

- Ce projet est maintenu par l'équipe informatique de l'ANPT
- Contact: [email@anpt.dz](mailto:email@anpt.dz)