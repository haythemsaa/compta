# Guide de Développement - Compteo Tunisia

## 🚀 Démarrage Rapide

### Prérequis
- Docker & Docker Compose
- Git

### Installation Complète

```bash
# 1. Cloner le projet
git clone https://github.com/haythemsaa/compta.git
cd compta

# 2. Configuration
cp .env.example .env
cp backend/.env.example backend/.env

# 3. Générer la clé Laravel
cd backend
php artisan key:generate
cd ..

# 4. Démarrer les services Docker
docker-compose up -d

# 5. Installer les dépendances backend
docker exec -it compteo-backend composer install

# 6. Exécuter les migrations et seeders
docker exec -it compteo-backend php artisan migrate:fresh --seed

# 7. Installer les dépendances frontend
docker exec -it compteo-frontend npm install

# 8. Accéder à l'application
# Frontend: http://localhost:3000
# Backend API: http://localhost:8000/api
# Mailhog: http://localhost:8025
```

## 🗄️ Base de Données

### Migrations

```bash
# Exécuter toutes les migrations
docker exec -it compteo-backend php artisan migrate

# Réinitialiser la base de données
docker exec -it compteo-backend php artisan migrate:fresh

# Avec les données de test
docker exec -it compteo-backend php artisan migrate:fresh --seed
```

### Seeders

Le projet inclut des seeders pour les données de démonstration :

**ExpenseCategorySeeder**
- 10 catégories par défaut (Transport, Restaurant, Hôtel, etc.)
- Codes comptables tunisiens (SCE)
- Icônes et couleurs

**DemoDataSeeder**
- Organisation de démonstration: "Compteo Demo"
- 5 utilisateurs avec différents rôles
- 2 véhicules (personnel et entreprise)

### Comptes de Test

| Email | Mot de passe | Rôle | Permissions |
|-------|--------------|------|-------------|
| demo@compteo.tn | demo | employee | Créer/consulter ses notes |
| manager@compteo.tn | demo | manager | + Valider équipe |
| comptable@compteo.tn | demo | accountant | + Traitement comptable |
| daf@compteo.tn | demo | daf | + Analytics globales |
| admin@compteo.tn | demo | admin | + Configuration système |

## 📊 Structure de la Base de Données

### Tables Principales

```
organizations (multi-tenant)
├── users (employés, rôles)
├── expense_reports (notes de frais)
│   ├── expense_items (lignes de dépense)
│   └── mileage_expenses (frais kilométriques)
├── vehicles (parc automobile)
├── media (justificatifs, OCR)
└── expense_policies (politiques)
```

### Statuts des Notes de Frais

```
draft → submitted → approved → paid
              ↓
           rejected
```

## 🔧 Développement Backend

### Créer un Contrôleur

```bash
docker exec -it compteo-backend php artisan make:controller Api/MyController --api
```

### Créer un Modèle

```bash
docker exec -it compteo-backend php artisan make:model MyModel -m
```

### Tester l'API

```bash
# Avec curl
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"demo@compteo.tn","password":"demo"}'

# Avec token
curl http://localhost:8000/api/dashboard \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 💻 Développement Frontend

### Structure Vue.js

```
frontend/src/
├── assets/          # CSS, images
├── components/      # Composants réutilisables
├── views/           # Pages/vues
│   ├── auth/        # Login, register
│   ├── expenses/    # Notes de frais
│   └── ...
├── router/          # Vue Router
├── stores/          # Pinia stores
├── services/        # API service
├── types/           # TypeScript types
└── utils/           # Utilitaires
```

### Commandes Utiles

```bash
# Dev server
docker exec -it compteo-frontend npm run dev

# Build production
docker exec -it compteo-frontend npm run build

# Type checking
docker exec -it compteo-frontend npm run type-check

# Lint
docker exec -it compteo-frontend npm run lint
```

## 🧪 Tests

### Backend (PHPUnit)

```bash
docker exec -it compteo-backend php artisan test
```

### Frontend (Vitest)

```bash
docker exec -it compteo-frontend npm run test
```

## 🇹🇳 Fonctionnalités Tunisiennes

### Taux de TVA

```php
19% - Taux standard
13% - Taux réduit
7%  - Taux super-réduit
0%  - Exonéré (exports)
```

### Matricule Fiscal

Format: `XXXXXXX/X/X/XXX`
Validation automatique dans les modèles.

### Barèmes Kilométriques 2025

```php
Vehicle::getMileageRate(int $annualKm): float

Exemple pour 5 CV:
- 0-5000 km:    0.310 TND/km
- 5000-10000:   0.290 TND/km
- 10000+:       0.270 TND/km
```

### Plan Comptable SCE

```
625: Déplacements, missions, réceptions
6251: Voyages et déplacements
6252: Missions (indemnités km)
6253: Réceptions (restaurants)
```

## 📝 Workflow Notes de Frais

### 1. Création (Draft)

```php
$report = ExpenseReport::create([
    'organization_id' => $org->id,
    'user_id' => $user->id,
    'title' => 'Déplacement Tunis-Sfax',
    'description' => 'Mission commerciale',
]);
```

### 2. Ajout d'Items

```php
$report->items()->create([
    'expense_category_id' => 1,
    'date' => now(),
    'merchant_name' => 'Restaurant Le Gourmet',
    'amount' => 45.500,
    'tva_rate' => 19,
]);
```

### 3. Soumission

```php
$report->submit();
// Status: draft → submitted
```

### 4. Approbation

```php
$report->approve($manager);
// Status: submitted → approved
```

### 5. Paiement

```php
$report->update(['status' => 'paid', 'paid_at' => now()]);
```

## 🔐 Sécurité

### Middleware Auth

```php
Route::middleware('auth:sanctum')->group(function () {
    // Routes protégées
});
```

### Permissions par Rôle

```php
// Dans le contrôleur
if (!$user->canApprove()) {
    abort(403, 'Non autorisé');
}

// Dans le modèle User
public function canApprove(): bool
{
    return $this->isManager();
}
```

## 🐛 Débogage

### Logs Laravel

```bash
docker exec -it compteo-backend tail -f storage/logs/laravel.log
```

### Logs Docker

```bash
docker-compose logs -f backend
docker-compose logs -f frontend
```

### Base de Données

```bash
# Accéder à PostgreSQL
docker exec -it compteo-postgres psql -U compteo -d compteo_tunisia

# Voir les tables
\dt

# Voir les données
SELECT * FROM users;
```

## 📦 Production

### Build Frontend

```bash
cd frontend
npm run build
# Les fichiers sont dans dist/
```

### Optimisations Laravel

```bash
docker exec -it compteo-backend php artisan config:cache
docker exec -it compteo-backend php artisan route:cache
docker exec -it compteo-backend php artisan view:cache
docker exec -it compteo-backend php artisan optimize
```

## 🆘 Problèmes Courants

### Ports déjà utilisés

```bash
# Vérifier les ports
sudo lsof -i :3000
sudo lsof -i :8000
sudo lsof -i :5432

# Changer les ports dans docker-compose.yml
```

### Permissions Laravel

```bash
docker exec -it compteo-backend chmod -R 775 storage bootstrap/cache
docker exec -it compteo-backend chown -R www-data:www-data storage bootstrap/cache
```

### Réinitialiser tout

```bash
docker-compose down -v
docker-compose up -d --build
docker exec -it compteo-backend php artisan migrate:fresh --seed
```

## 📚 Ressources

- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js Documentation](https://vuejs.org/guide/)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [PostgreSQL Documentation](https://www.postgresql.org/docs/)
- [Docker Documentation](https://docs.docker.com/)

## 🤝 Contribution

```bash
# Créer une branche
git checkout -b feature/ma-nouvelle-fonctionnalite

# Commit
git commit -m "feat: ajout de ma fonctionnalité"

# Push
git push origin feature/ma-nouvelle-fonctionnalite
```

## 📄 Licence

© 2025 Compteo Tunisia - Tous droits réservés
