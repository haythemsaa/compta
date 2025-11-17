# Guide d'Installation - Expensya Tunisia

## Prérequis

### Logiciels Requis
- **Docker** >= 24.0
- **Docker Compose** >= 2.20
- **Git** >= 2.30
- **Node.js** >= 20.x (pour développement frontend sans Docker)
- **PHP** >= 8.3 (pour développement backend sans Docker)
- **Composer** >= 2.6 (pour développement backend sans Docker)

## Installation avec Docker (Recommandé)

### 1. Cloner le Repository

```bash
git clone https://github.com/haythemsaa/compta.git
cd compta
```

### 2. Configuration de l'Environnement

```bash
# Copier le fichier d'environnement
cp .env.example .env

# Configurer le backend Laravel
cd backend
cp .env.example .env
php artisan key:generate
cd ..
```

### 3. Démarrer les Services Docker

```bash
# Construire et démarrer tous les services
docker-compose up -d

# Vérifier que tous les services sont démarrés
docker-compose ps
```

### 4. Initialiser la Base de Données

```bash
# Accéder au conteneur backend
docker exec -it expensya-backend sh

# Exécuter les migrations
php artisan migrate

# Charger les données de test (optionnel)
php artisan db:seed

# Sortir du conteneur
exit
```

### 5. Installer les Dépendances Frontend

```bash
# Accéder au conteneur frontend
docker exec -it expensya-frontend sh

# Les dépendances sont déjà installées, mais vous pouvez les réinstaller
npm install

# Sortir du conteneur
exit
```

### 6. Accéder à l'Application

- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8000
- **Mailhog (emails de test)**: http://localhost:8025
- **PostgreSQL**: localhost:5432
- **Redis**: localhost:6379

## Installation Manuelle (Sans Docker)

### Backend Laravel

```bash
cd backend

# Installer les dépendances
composer install

# Configuration
cp .env.example .env
php artisan key:generate

# Configurer la base de données dans .env
# Puis exécuter les migrations
php artisan migrate

# Démarrer le serveur
php artisan serve
```

### Frontend Vue.js

```bash
cd frontend

# Installer les dépendances
npm install

# Démarrer le serveur de développement
npm run dev
```

## Configuration des Services Externes

### Google Cloud Vision API (OCR)

1. Créer un projet sur Google Cloud Console
2. Activer l'API Cloud Vision
3. Créer une clé API
4. Ajouter dans `.env`:
```
GOOGLE_CLOUD_VISION_API_KEY=votre_cle_api
```

### Tesseract OCR (Fallback)

```bash
# Ubuntu/Debian
sudo apt-get install tesseract-ocr tesseract-ocr-fra tesseract-ocr-ara

# macOS
brew install tesseract tesseract-lang
```

## Comptes de Test

### Connexion Demo
- **Email**: demo@expensya.tn
- **Mot de passe**: demo

## Commandes Utiles

### Docker

```bash
# Arrêter tous les services
docker-compose down

# Reconstruire les conteneurs
docker-compose up -d --build

# Voir les logs
docker-compose logs -f

# Voir les logs d'un service spécifique
docker-compose logs -f backend
```

### Laravel (Backend)

```bash
# Accéder au conteneur
docker exec -it expensya-backend sh

# Exécuter les migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Créer un nouveau modèle avec migration
php artisan make:model NomModele -m

# Créer un contrôleur
php artisan make:controller NomController

# Nettoyer le cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Frontend

```bash
# Accéder au conteneur
docker exec -it expensya-frontend sh

# Build de production
npm run build

# Linter
npm run lint

# Type checking
npm run type-check
```

## Résolution de Problèmes

### Erreur: Port déjà utilisé

```bash
# Vérifier les ports utilisés
sudo lsof -i :3000
sudo lsof -i :8000
sudo lsof -i :5432

# Arrêter les processus ou changer les ports dans docker-compose.yml
```

### Erreur: Permission denied (storage Laravel)

```bash
docker exec -it expensya-backend sh
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Base de données non accessible

```bash
# Vérifier que PostgreSQL est démarré
docker-compose ps postgres

# Redémarrer le service
docker-compose restart postgres

# Vérifier les logs
docker-compose logs postgres
```

## Tests

### Backend (PHPUnit)

```bash
docker exec -it expensya-backend sh
php artisan test
```

### Frontend (Vitest)

```bash
docker exec -it expensya-frontend sh
npm run test
```

## Support

Pour toute question ou problème:
- Documentation complète: `docs/`
- Issues GitHub: https://github.com/haythemsaa/compta/issues
- Email: dev@expensya-tn.com
