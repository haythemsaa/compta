# Guide de Déploiement - Compteo TN

Guide complet pour déployer Compteo TN en environnement de développement et production.

## Table des matières

- [Prérequis](#prérequis)
- [Installation Développement](#installation-développement)
- [Configuration](#configuration)
- [Déploiement Production](#déploiement-production)
- [Maintenance](#maintenance)

---

## Prérequis

### Développement

- **Docker** 24.0+ et **Docker Compose** 2.20+
- **Git** 2.40+
- **Node.js** 20+ (pour le développement frontend local)
- **PHP** 8.3+ (optionnel, pour développement backend local)

### Production

- **Serveur** : Ubuntu 22.04 LTS ou Debian 12
- **Nginx** 1.18+ ou **Apache** 2.4+
- **PHP** 8.3+ avec extensions : pgsql, redis, gd, mbstring, xml, zip, bcmath
- **PostgreSQL** 16+
- **Redis** 7+
- **Node.js** 20+ (pour build frontend)
- **Composer** 2.6+
- **Certificat SSL** (Let's Encrypt recommandé)

---

## Installation Développement

### 1. Cloner le repository

```bash
git clone https://github.com/haythemsaa/compta.git
cd compta
```

### 2. Configuration Backend

```bash
cd backend

# Copier le fichier d'environnement
cp .env.example .env

# Éditer .env avec vos paramètres
nano .env
```

Configuration `.env` minimale pour développement :

```env
APP_NAME="Compteo TN"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=compteo
DB_USERNAME=compteo_user
DB_PASSWORD=your_secure_password

REDIS_HOST=localhost
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
```

### 3. Démarrer avec Docker Compose

```bash
# Retour à la racine du projet
cd ..

# Démarrer tous les services
docker-compose up -d

# Vérifier que tout fonctionne
docker-compose ps
```

### 4. Installation des dépendances Backend

```bash
# Accéder au container backend
docker-compose exec backend bash

# Installer les dépendances
composer install

# Générer la clé d'application
php artisan key:generate

# Exécuter les migrations
php artisan migrate

# Installer l'API Sanctum
php artisan install:api

# Créer le lien symbolique pour le storage
php artisan storage:link

# Seed les données de démonstration
php artisan db:seed
```

### 5. Installation Frontend

```bash
# Dans un nouveau terminal
cd frontend

# Installer les dépendances
npm install

# Copier le fichier d'environnement
cp .env.example .env

# Éditer .env
nano .env
```

Configuration `.env` frontend pour développement :

```env
VITE_API_BASE_URL=http://localhost:8000/api
```

### 6. Démarrer le serveur de développement

```bash
# Terminal 1 - Backend (si vous n'utilisez pas Docker)
cd backend
php artisan serve

# Terminal 2 - Frontend
cd frontend
npm run dev
```

### 7. Accéder à l'application

- **Frontend** : http://localhost:5173
- **Backend API** : http://localhost:8000/api
- **Documentation API** : Voir [docs/API.md](docs/API.md)

### 8. Comptes de test

Après le seeding, utilisez ces comptes :

| Email | Mot de passe | Rôle |
|-------|--------------|------|
| admin@compteo.tn | password | Administrateur |
| manager@compteo.tn | password | Manager |
| employee@compteo.tn | password | Employé |
| accountant@compteo.tn | password | Comptable |

---

## Configuration

### Variables d'environnement Backend

#### Application

```env
APP_NAME="Compteo TN"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://compteo.votredomaine.tn
APP_TIMEZONE=Africa/Tunis
APP_LOCALE=fr
```

#### Base de données

```env
DB_CONNECTION=pgsql
DB_HOST=your-db-host
DB_PORT=5432
DB_DATABASE=compteo_prod
DB_USERNAME=compteo_user
DB_PASSWORD=strong_random_password
```

#### Redis

```env
REDIS_HOST=your-redis-host
REDIS_PASSWORD=redis_password
REDIS_PORT=6379
REDIS_CLIENT=predis
```

#### Email (Production)

Pour un serveur SMTP :

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.votredomaine.tn
MAIL_PORT=587
MAIL_USERNAME=noreply@compteo.tn
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@compteo.tn
MAIL_FROM_NAME="${APP_NAME}"
```

#### Stockage (Production avec S3)

```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=eu-west-1
AWS_BUCKET=compteo-storage
```

### Variables d'environnement Frontend

```env
VITE_API_BASE_URL=https://api.compteo.votredomaine.tn/api
VITE_APP_NAME="Compteo TN"
```

---

## Déploiement Production

### Option 1 : Déploiement Manuel

#### 1. Préparation du serveur

```bash
# Mettre à jour le système
sudo apt update && sudo apt upgrade -y

# Installer les dépendances
sudo apt install -y nginx postgresql-16 redis-server php8.3-fpm \
  php8.3-pgsql php8.3-redis php8.3-gd php8.3-mbstring \
  php8.3-xml php8.3-zip php8.3-bcmath php8.3-curl \
  git curl unzip

# Installer Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Installer Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

#### 2. Configuration PostgreSQL

```bash
# Se connecter à PostgreSQL
sudo -u postgres psql

# Créer la base de données et l'utilisateur
CREATE DATABASE compteo_prod;
CREATE USER compteo_user WITH ENCRYPTED PASSWORD 'strong_password';
GRANT ALL PRIVILEGES ON DATABASE compteo_prod TO compteo_user;
\q
```

#### 3. Déploiement Backend

```bash
# Créer le répertoire de l'application
sudo mkdir -p /var/www/compteo
sudo chown $USER:$USER /var/www/compteo

# Cloner le repository
cd /var/www/compteo
git clone https://github.com/haythemsaa/compta.git .

# Installation Backend
cd backend
composer install --optimize-autoloader --no-dev
cp .env.example .env

# Éditer .env avec les vraies valeurs de production
nano .env

# Générer la clé et migrer
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permissions
sudo chown -R www-data:www-data /var/www/compteo/backend/storage
sudo chown -R www-data:www-data /var/www/compteo/backend/bootstrap/cache
sudo chmod -R 775 /var/www/compteo/backend/storage
sudo chmod -R 775 /var/www/compteo/backend/bootstrap/cache
```

#### 4. Build Frontend

```bash
cd /var/www/compteo/frontend
npm install
cp .env.example .env

# Éditer .env avec l'URL de production
nano .env

# Build pour production
npm run build
```

#### 5. Configuration Nginx

```bash
sudo nano /etc/nginx/sites-available/compteo
```

Contenu du fichier :

```nginx
# Backend API
server {
    listen 80;
    server_name api.compteo.votredomaine.tn;
    root /var/www/compteo/backend/public;

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
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}

# Frontend
server {
    listen 80;
    server_name compteo.votredomaine.tn;
    root /var/www/compteo/frontend/dist;

    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

Activer le site :

```bash
sudo ln -s /etc/nginx/sites-available/compteo /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

#### 6. SSL avec Let's Encrypt

```bash
# Installer Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtenir les certificats
sudo certbot --nginx -d compteo.votredomaine.tn -d api.compteo.votredomaine.tn

# Renouvellement automatique
sudo certbot renew --dry-run
```

### Option 2 : Déploiement avec Docker (Production)

#### 1. Créer docker-compose.prod.yml

```yaml
version: '3.8'

services:
  postgres:
    image: postgres:16-alpine
    restart: always
    environment:
      POSTGRES_DB: compteo_prod
      POSTGRES_USER: compteo_user
      POSTGRES_PASSWORD: ${DB_PASSWORD}
    volumes:
      - postgres_data:/var/lib/postgresql/data
    networks:
      - compteo-network

  redis:
    image: redis:7-alpine
    restart: always
    command: redis-server --requirepass ${REDIS_PASSWORD}
    volumes:
      - redis_data:/data
    networks:
      - compteo-network

  backend:
    build:
      context: ./backend
      dockerfile: Dockerfile.prod
    restart: always
    environment:
      APP_ENV: production
      DB_HOST: postgres
      REDIS_HOST: redis
    volumes:
      - ./backend/storage:/var/www/html/storage
    networks:
      - compteo-network
    depends_on:
      - postgres
      - redis

  nginx:
    image: nginx:alpine
    restart: always
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./nginx/conf.d:/etc/nginx/conf.d
      - ./frontend/dist:/usr/share/nginx/html
      - ./certbot/conf:/etc/letsencrypt
      - ./certbot/www:/var/www/certbot
    networks:
      - compteo-network
    depends_on:
      - backend

volumes:
  postgres_data:
  redis_data:

networks:
  compteo-network:
    driver: bridge
```

#### 2. Dockerfile.prod pour Backend

Créer `backend/Dockerfile.prod` :

```dockerfile
FROM php:8.3-fpm-alpine

# Install dependencies
RUN apk add --no-cache \
    postgresql-dev \
    zip \
    unzip \
    git

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage

CMD ["php-fpm"]
```

#### 3. Déployer

```bash
# Build et démarrer
docker-compose -f docker-compose.prod.yml up -d --build

# Exécuter les migrations
docker-compose -f docker-compose.prod.yml exec backend php artisan migrate --force

# Cache optimization
docker-compose -f docker-compose.prod.yml exec backend php artisan config:cache
docker-compose -f docker-compose.prod.yml exec backend php artisan route:cache
docker-compose -f docker-compose.prod.yml exec backend php artisan view:cache
```

---

## Maintenance

### Sauvegardes

#### Base de données

```bash
# Backup manuel
pg_dump -U compteo_user -h localhost compteo_prod > backup_$(date +%Y%m%d).sql

# Restauration
psql -U compteo_user -h localhost compteo_prod < backup_20250117.sql
```

#### Script de sauvegarde automatique

Créer `/usr/local/bin/backup-compteo.sh` :

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/compteo"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Backup database
pg_dump -U compteo_user compteo_prod | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup storage
tar -czf $BACKUP_DIR/storage_$DATE.tar.gz /var/www/compteo/backend/storage

# Garder seulement les 30 derniers jours
find $BACKUP_DIR -name "*.gz" -mtime +30 -delete

echo "Backup completed: $DATE"
```

Ajouter à crontab :

```bash
sudo crontab -e
# Ajouter :
0 2 * * * /usr/local/bin/backup-compteo.sh
```

### Mises à jour

#### Mise à jour de l'application

```bash
cd /var/www/compteo

# Pull les dernières modifications
git pull origin main

# Backend
cd backend
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Frontend
cd ../frontend
npm install
npm run build

# Redémarrer les services
sudo systemctl restart php8.3-fpm
sudo systemctl restart nginx
```

### Monitoring

#### Logs Backend

```bash
# Logs Laravel
tail -f /var/www/compteo/backend/storage/logs/laravel.log

# Logs Nginx
tail -f /var/log/nginx/error.log
tail -f /var/log/nginx/access.log

# Logs PHP-FPM
tail -f /var/log/php8.3-fpm.log
```

#### Performance

```bash
# Vérifier les processus
ps aux | grep php-fpm
ps aux | grep nginx

# Vérifier la mémoire
free -h

# Vérifier l'espace disque
df -h
```

### Optimisation Production

#### PHP-FPM

Éditer `/etc/php/8.3/fpm/pool.d/www.conf` :

```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500
```

#### PostgreSQL

Éditer `/etc/postgresql/16/main/postgresql.conf` :

```ini
shared_buffers = 256MB
effective_cache_size = 1GB
maintenance_work_mem = 64MB
checkpoint_completion_target = 0.9
wal_buffers = 16MB
default_statistics_target = 100
random_page_cost = 1.1
effective_io_concurrency = 200
work_mem = 4MB
min_wal_size = 1GB
max_wal_size = 4GB
```

#### Redis

Éditer `/etc/redis/redis.conf` :

```ini
maxmemory 256mb
maxmemory-policy allkeys-lru
```

---

## Dépannage

### Erreur 500 Backend

```bash
# Vérifier les logs
tail -f backend/storage/logs/laravel.log

# Vérifier les permissions
sudo chown -R www-data:www-data backend/storage
sudo chmod -R 775 backend/storage

# Reconstruire le cache
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

### Problème de connexion base de données

```bash
# Tester la connexion
php artisan tinker
>>> DB::connection()->getPdo();

# Vérifier PostgreSQL
sudo systemctl status postgresql
sudo -u postgres psql -c "SELECT version();"
```

### Frontend ne charge pas

```bash
# Vérifier que le build existe
ls -la frontend/dist

# Rebuild
cd frontend
npm run build

# Vérifier la configuration Nginx
sudo nginx -t
```

---

## Support

Pour toute question ou problème :

- **Documentation** : Voir [README.md](README.md) et [docs/API.md](docs/API.md)
- **Issues** : https://github.com/haythemsaa/compta/issues
- **Email** : support@compteo.tn

---

**Dernière mise à jour** : 2025-01-17
