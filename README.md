# SirteRH - Système de Gestion RH ANPT

SirteRH est un système de gestion des ressources humaines pour l'ANPT (Agence Nationale de Promotion et de Développement des Parcs Technologiques). Cette version est une migration du backend serverless Hono vers un backend Laravel avec PostgreSQL, tout en maintenant la même fonctionnalité et structure d'API pour préserver la compatibilité avec le frontend existant.

## Architecture du Projet

Le projet est organisé en deux parties principales:

```
sirterh/
├── backend/             # API Laravel avec base de données PostgreSQL
│   ├── app/             # Code de l'application Laravel
│   ├── database/        # Migrations et seeders pour la base de données
│   ├── routes/          # Configuration des routes API
│   └── ...
└── frontend/           # Application React existante (inchangée)
    ├── src/            # Code source React
    └── ...
```

## Fonctionnalités

* **Authentification**: Connexion par login/mot de passe, gestion de sessions sécurisées
* **Gestion des Employés**: Création, consultation et gestion des profils employés
* **Fiches de Paie**: Upload et visualisation des fiches de paie mensuelles
* **Chartes Salariales**: Gestion des grilles de salaires
* **Événements**: Publication et suivi des événements d'entreprise
* **Annonces**: Publication d'annonces et communications importantes
* **Documents Administratifs**: Gestion de documents administratifs
* **Requêtes des Employés**: Système de demandes et approbations

## Prérequis

* PHP 8.1+ avec extensions requises
* Composer
* PostgreSQL 12+
* Node.js et NPM (pour le frontend)
* Docker et Docker Compose (pour l'environnement de développement)

## Installation

### Avec Docker (Recommandé)

1. Cloner ce dépôt
   ```bash
   git clone https://github.com/anpt/sirterh.git
   cd sirterh
   ```

2. Lancer l'environnement Docker
   ```bash
   docker-compose up -d
   ```

3. Installer les dépendances Laravel
   ```bash
   docker-compose exec app composer install
   ```

4. Générer la clé de l'application
   ```bash
   docker-compose exec app php artisan key:generate
   ```

5. Exécuter les migrations et seeders
   ```bash
   docker-compose exec app php artisan migrate --seed
   ```

6. L'application est accessible à:
   - Backend API: http://localhost:8000
   - Frontend: http://localhost:3000

### Installation Manuelle

#### Backend (Laravel)

1. Naviguer vers le dossier backend
   ```bash
   cd backend
   ```

2. Installer les dépendances
   ```bash
   composer install
   ```

3. Configurer l'environnement
   ```bash
   cp .env.example .env
   # Éditer .env avec les informations de connexion à la base de données PostgreSQL
   ```

4. Générer la clé de l'application
   ```bash
   php artisan key:generate
   ```

5. Exécuter les migrations et seeders
   ```bash
   php artisan migrate --seed
   ```

6. Démarrer le serveur de développement
   ```bash
   php artisan serve
   ```

#### Frontend (React)

1. Naviguer vers le dossier frontend
   ```bash
   cd frontend
   ```

2. Installer les dépendances
   ```bash
   npm install
   ```

3. Démarrer le serveur de développement
   ```bash
   npm run dev
   ```

## Déploiement sur VPS

### Configuration du Serveur

1. Installer les prérequis:
   - Nginx ou Apache
   - PHP 8.1+ avec extensions
   - PostgreSQL 12+
   - Composer
   - Node.js et NPM

2. Configurer le serveur web (exemple pour Nginx):
   ```nginx
   server {
       listen 80;
       server_name sirterh.anpt.dz;
       root /var/www/sirterh/backend/public;

       add_header X-Frame-Options "SAMEORIGIN";
       add_header X-Content-Type-Options "nosniff";

       index index.php;

       charset utf-8;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location = /favicon.ico { access_log off; log_not_found off; }
       location = /robots.txt  { access_log off; log_not_found off; }

       error_page 404 /index.php;

       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
       }

       location ~ /\.(?!well-known).* {
           deny all;
       }
   }

   server {
       listen 80;
       server_name app.sirterh.anpt.dz;
       root /var/www/sirterh/frontend/dist;

       add_header X-Frame-Options "SAMEORIGIN";
       add_header X-Content-Type-Options "nosniff";

       index index.html;

       charset utf-8;

       location / {
           try_files $uri $uri/ /index.html;
       }
   }
   ```

3. Configurer SSL avec Certbot pour HTTPS (recommandé)

### Déploiement du Backend

1. Cloner le dépôt sur le serveur
   ```bash
   git clone https://github.com/anpt/sirterh.git /var/www/sirterh
   cd /var/www/sirterh/backend
   ```

2. Installer les dépendances en mode production
   ```bash
   composer install --optimize-autoloader --no-dev
   ```

3. Configurer l'environnement
   ```bash
   cp .env.example .env
   # Éditer .env avec les informations de production
   php artisan key:generate
   ```

4. Optimiser Laravel pour la production
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

5. Configurer les permissions
   ```bash
   chown -R www-data:www-data /var/www/sirterh
   chmod -R 755 /var/www/sirterh/backend/storage
   ```

6. Exécuter les migrations
   ```bash
   php artisan migrate --force
   ```

### Déploiement du Frontend

1. Dans le dossier frontend
   ```bash
   cd /var/www/sirterh/frontend
   ```

2. Installer les dépendances
   ```bash
   npm install
   ```

3. Construire pour la production
   ```bash
   npm run build
   ```

4. Les fichiers sont générés dans `frontend/dist` et servis par Nginx

## Utilisateurs par défaut

- **Admin**: 
  - Login: `admin`
  - Mot de passe: `admin123`

- **Employé**:
  - Login: `employee`
  - Mot de passe: `employee123`

## Support et Maintenance

Pour toute question ou assistance, veuillez contacter l'équipe IT de l'ANPT.