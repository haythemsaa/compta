# Architecture Technique - Expensya Tunisia

## Vue d'ensemble

Expensya Tunisia est une solution SaaS de gestion automatisée des notes de frais, conçue spécifiquement pour le marché tunisien avec une conformité totale aux réglementations fiscales et comptables locales.

## Stack Technologique

### Backend
- **Framework**: Laravel 11.x (PHP 8.3+)
- **Base de données**: PostgreSQL 16+
- **Cache/Queue**: Redis 7+
- **API**: RESTful avec Laravel Sanctum (JWT)
- **Storage**: S3-compatible (DigitalOcean Spaces)
- **OCR**: Google Cloud Vision API + Tesseract (fallback)

### Frontend Web
- **Framework**: Vue.js 3 avec Composition API
- **Langage**: TypeScript
- **Build**: Vite
- **UI**: Tailwind CSS 3
- **State Management**: Pinia
- **Routing**: Vue Router 4
- **Charts**: Chart.js / vue-chartjs

### Mobile (Prochaine phase)
- **Framework**: Flutter 3.x
- **Platforms**: iOS + Android
- **Offline**: Hive database
- **Sync**: Background synchronisation

## Architecture Système

```
┌─────────────────────────────────────────────────────────────┐
│                      Load Balancer / CDN                     │
└─────────────────────────────────────────────────────────────┘
                              │
              ┌───────────────┴───────────────┐
              │                               │
┌─────────────▼──────────┐      ┌────────────▼─────────────┐
│   Frontend Web App     │      │   Frontend Mobile App    │
│   (Vue.js + Vite)      │      │   (Flutter)              │
└─────────────┬──────────┘      └────────────┬─────────────┘
              │                               │
              └───────────────┬───────────────┘
                              │ HTTPS/REST API
                  ┌───────────▼────────────┐
                  │   Backend API Server   │
                  │   (Laravel 11.x)       │
                  └───────────┬────────────┘
                              │
          ┌───────────────────┼───────────────────┐
          │                   │                   │
┌─────────▼─────────┐ ┌───────▼────────┐ ┌───────▼────────┐
│   PostgreSQL      │ │     Redis      │ │  File Storage  │
│   (Main DB)       │ │  (Cache/Queue) │ │     (S3)       │
└───────────────────┘ └────────────────┘ └────────────────┘
```

## Modèle de Données Principal

### Tables Principales

#### `organizations`
- Gestion multi-tenant
- Configuration fiscale tunisienne
- Paramètres de politiques de dépenses

#### `users`
- Collaborateurs
- Rôles: employee, manager, accountant, daf, admin
- Authentification JWT

#### `expense_reports`
- Notes de frais principales
- Workflow de validation
- Calculs automatiques TVA

#### `expense_items`
- Lignes de dépense
- Lien vers justificatifs (media)
- Classification automatique (ML)

#### `mileage_expenses`
- Frais kilométriques
- Barèmes tunisiens 2025
- Calcul distance automatique

#### `vehicles`
- Parc automobile
- Puissance fiscale (CV)
- Documents (carte grise, assurance)

#### `media`
- Justificatifs scannés
- Résultats OCR
- Stockage S3

#### `expense_policies`
- Politiques de dépenses
- Plafonds par catégorie
- Règles de validation

## Sécurité

### Authentification
- JWT tokens via Laravel Sanctum
- Access token: 1h
- Refresh token: 7 jours
- 2FA optionnelle (TOTP)

### Autorisation (RBAC)
- `employee`: CRUD sur ses notes
- `manager`: + Validation équipe
- `accountant`: + Traitement comptable
- `daf`: + Analytics globales
- `admin`: + Configuration système

### Chiffrement
- Données au repos: AES-256
- Données en transit: TLS 1.3
- Mots de passe: Bcrypt (cost 12)

### Conformité Tunisienne
- Archivage 10 ans (Code Commerce)
- INPDP: Protection données personnelles
- Valeur probante: Loi 2000-83

## APIs Principales

### Authentification
```
POST   /api/auth/login
POST   /api/auth/logout
POST   /api/auth/refresh
POST   /api/auth/register
```

### Notes de Frais
```
GET    /api/expense-reports
POST   /api/expense-reports
GET    /api/expense-reports/{id}
PUT    /api/expense-reports/{id}
DELETE /api/expense-reports/{id}
POST   /api/expense-reports/{id}/submit
POST   /api/expense-reports/{id}/approve
POST   /api/expense-reports/{id}/reject
```

### OCR
```
POST   /api/ocr/process
GET    /api/ocr/status/{id}
```

### Frais Kilométriques
```
POST   /api/mileage/calculate-distance
GET    /api/mileage/rates
POST   /api/mileage/expenses
```

### Rapports & Analytics
```
GET    /api/reports/dashboard
POST   /api/reports/generate
GET    /api/reports/export/{format}
```

## Intégrations

### OCR (Phase 1)
- Google Cloud Vision API (primaire)
- Tesseract OCR (fallback)
- Classification ML (scikit-learn)

### Taux de Change
- API Banque Centrale de Tunisie (BCT)
- Update quotidien automatique

### Comptabilité (Phase 2)
- Sage Tunisie
- Ciel Compta
- Export FEC standard

### Paiements (Phase 3)
- Cartes virtuelles
- Gateway de paiement tunisien

## Performance & Scalabilité

### Caching
- Redis pour sessions
- Cache API responses (5-60 min)
- Cache taux de change (24h)

### Queue Jobs
- Processing OCR (asynchrone)
- Envoi emails
- Génération rapports
- Synchronisation mobile

### Optimisations
- Lazy loading images
- Pagination API (50 items/page)
- Compression Gzip
- CDN pour assets statiques

## Monitoring & Logs

### Métriques
- New Relic APM
- Temps de réponse API
- Taux d'erreur
- Utilisation ressources

### Logs
- Application: Laravel logs
- Erreurs: Sentry
- Audit trail: PostgreSQL
- Analytics: Google Analytics 4

## Déploiement

### Environnements
- **Development**: Docker local
- **Staging**: DigitalOcean Droplet
- **Production**: DigitalOcean Kubernetes

### CI/CD
- GitHub Actions
- Tests automatiques (PHPUnit, Vitest)
- Déploiement automatique sur staging
- Déploiement manuel sur production

### Sauvegardes
- Base de données: Quotidienne (3h00)
- Files storage: Continuous backup
- Rétention: 30 jours
- Géo-redondance: 2 datacenters

## Évolutions Futures

### Phase 2 (Q3-Q4 2026)
- Ordres de mission
- Analytics avancés
- Intégrations ERP tunisiens

### Phase 3 (2027)
- Cartes de paiement professionnelles
- IA prédictive budgets
- Expansion Maghreb
