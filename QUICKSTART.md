# Guide de Démarrage Rapide - Compteo TN

Lancez Compteo TN en 5 minutes avec Docker Compose.

## Prérequis

- **Docker** 24.0+ et **Docker Compose** 2.20+
- **Git** 2.40+

## Installation

### 1. Cloner le repository

```bash
git clone https://github.com/haythemsaa/compta.git
cd compta
```

### 2. Démarrer les services

```bash
# Démarrer tous les services (PostgreSQL, Redis, Backend, Frontend)
docker-compose up -d

# Attendre que les services démarrent (environ 30 secondes)
docker-compose ps
```

### 3. Initialiser la base de données

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

# Quitter le container
exit
```

### 4. Accéder à l'application

Ouvrez votre navigateur :

- **Frontend** : http://localhost:5173
- **Backend API** : http://localhost:8000/api

### 5. Se connecter

Utilisez l'un de ces comptes de test :

| Email | Mot de passe | Rôle |
|-------|--------------|------|
| admin@compteo.tn | password | Administrateur |
| manager@compteo.tn | password | Manager |
| employee@compteo.tn | password | Employé |
| accountant@compteo.tn | password | Comptable |

## Premiers pas

### Créer un rapport de frais

1. Connectez-vous avec `employee@compteo.tn`
2. Cliquez sur "Nouveau rapport"
3. Remplissez le formulaire :
   - Titre : "Frais de mission Janvier 2025"
   - Période de début : Date de début
   - Période de fin : Date de fin
4. Cliquez sur "Créer le rapport"

### Ajouter des dépenses

Sur la page du rapport :

1. Cliquez sur "Ajouter une dépense"
2. Sélectionnez une catégorie (Restaurant, Transport, Hébergement, etc.)
3. Remplissez les informations :
   - Date
   - Nom du commerçant
   - Montant TTC
   - Taux de TVA (19%, 13%, 7%, ou 0%)
4. Cliquez sur "Ajouter"

### Ajouter des frais kilométriques

1. Cliquez sur "Ajouter frais kilométrique"
2. Sélectionnez un véhicule
3. Remplissez :
   - Lieu de départ : Tunis
   - Lieu d'arrivée : Sfax
   - Cliquez sur 🔍 pour calculer la distance
   - Cochez "Aller-retour" si nécessaire
   - Objet du déplacement
4. Cliquez sur "Ajouter"

### Soumettre le rapport

1. Vérifiez tous les frais ajoutés
2. Cliquez sur "Soumettre pour approbation"
3. Le rapport passe en statut "Soumis"

### Approuver un rapport (en tant que Manager)

1. Déconnectez-vous
2. Connectez-vous avec `manager@compteo.tn`
3. Allez sur "Rapports de frais"
4. Cliquez sur un rapport en statut "Soumis"
5. Cliquez sur "Approuver" ou "Rejeter"

## Fonctionnalités principales

### Pour les Employés

- ✅ Créer des rapports de frais
- ✅ Ajouter des dépenses avec justificatifs
- ✅ Ajouter des frais kilométriques
- ✅ Soumettre pour approbation
- ✅ Suivre le statut de remboursement

### Pour les Managers

- ✅ Voir tous les rapports de l'équipe
- ✅ Approuver ou rejeter les rapports
- ✅ Ajouter des commentaires de rejet
- ✅ Dashboard avec statistiques

### Pour les Comptables

- ✅ Voir tous les rapports approuvés
- ✅ Marquer comme payés
- ✅ Exporter les données comptables
- ✅ Catégorisation automatique selon le Plan Comptable SCE

### Pour les Administrateurs

- ✅ Gérer les utilisateurs
- ✅ Gérer les véhicules
- ✅ Configurer les catégories de dépenses
- ✅ Paramétrer les taux de TVA

## Catégories de dépenses disponibles

1. 🍽️ **Restaurant** - Frais de repas et restauration
2. 🚗 **Transport** - Taxi, métro, bus, parkings
3. 🏨 **Hébergement** - Hôtels, locations
4. ✈️ **Voyage** - Billets d'avion, train
5. 🔧 **Fournitures** - Matériel et équipements
6. 📱 **Télécom** - Téléphone, internet
7. 🎓 **Formation** - Cours, séminaires
8. 📢 **Marketing** - Publicité, événements
9. 🛡️ **Assurance** - Assurances professionnelles
10. 📄 **Autres** - Dépenses diverses

## Barèmes kilométriques 2025 (Tunisie)

Le système calcule automatiquement le tarif selon :

| Puissance | 0-5000 km/an | 5001-10000 km/an | 10001-20000 km/an | +20000 km/an |
|-----------|--------------|------------------|-------------------|--------------|
| 4 CV | 0.290 TND/km | 0.235 TND/km | 0.190 TND/km | 0.170 TND/km |
| 5 CV | 0.310 TND/km | 0.255 TND/km | 0.210 TND/km | 0.190 TND/km |
| 6 CV | 0.330 TND/km | 0.275 TND/km | 0.230 TND/km | 0.210 TND/km |
| 7 CV | 0.360 TND/km | 0.305 TND/km | 0.260 TND/km | 0.240 TND/km |
| 8+ CV | 0.390 TND/km | 0.335 TND/km | 0.290 TND/km | 0.270 TND/km |

## Taux de TVA en Tunisie

- **19%** : Taux normal
- **13%** : Taux réduit
- **7%** : Taux super-réduit
- **0%** : Exonéré

## Commandes utiles

### Voir les logs

```bash
# Logs backend
docker-compose logs -f backend

# Logs frontend
docker-compose logs -f frontend

# Logs PostgreSQL
docker-compose logs -f postgres
```

### Arrêter les services

```bash
# Arrêter tous les services
docker-compose down

# Arrêter et supprimer les volumes (⚠️ supprime la base de données)
docker-compose down -v
```

### Redémarrer un service

```bash
# Redémarrer le backend
docker-compose restart backend

# Rebuild le frontend
docker-compose up -d --build frontend
```

### Réinitialiser la base de données

```bash
docker-compose exec backend bash
php artisan migrate:fresh --seed
exit
```

### Accéder à PostgreSQL

```bash
docker-compose exec postgres psql -U compteo_user -d compteo
```

Requêtes utiles :

```sql
-- Voir tous les utilisateurs
SELECT id, name, email, role FROM users;

-- Voir tous les rapports de frais
SELECT id, reference, title, status, total_amount FROM expense_reports;

-- Voir toutes les catégories
SELECT id, name, code, icon FROM expense_categories;
```

## Structure du projet

```
compta/
├── backend/              # Laravel 11 API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/   # API Controllers
│   │   │   ├── Requests/      # Form Requests
│   │   │   └── Resources/     # API Resources
│   │   └── Models/            # Eloquent Models
│   ├── database/
│   │   ├── migrations/        # Database migrations
│   │   └── seeders/           # Database seeders
│   └── routes/
│       └── api.php            # API routes
├── frontend/             # Vue.js 3 + TypeScript
│   ├── src/
│   │   ├── components/        # Vue components
│   │   ├── views/             # Vue views
│   │   ├── services/          # API service
│   │   ├── types/             # TypeScript types
│   │   └── router/            # Vue Router
│   └── public/                # Static assets
├── docs/                 # Documentation
│   └── API.md                 # API documentation
├── docker-compose.yml    # Docker Compose configuration
└── README.md             # Documentation principale
```

## Prochaines étapes

Après avoir pris en main l'application :

1. **Personnalisation** :
   - Modifier les catégories de dépenses
   - Ajouter vos propres véhicules
   - Configurer les emails

2. **Intégrations** :
   - OCR pour scanner les reçus (Google Cloud Vision API)
   - Calcul automatique de distance (Google Maps API)
   - Export comptable (formats SCE)

3. **Déploiement** :
   - Voir [DEPLOYMENT.md](DEPLOYMENT.md) pour le déploiement en production

## Support

- **Documentation complète** : [README.md](README.md)
- **API Documentation** : [docs/API.md](docs/API.md)
- **Guide de déploiement** : [DEPLOYMENT.md](DEPLOYMENT.md)
- **Issues** : https://github.com/haythemsaa/compta/issues

---

**Bon développement avec Compteo TN!** 🚀
