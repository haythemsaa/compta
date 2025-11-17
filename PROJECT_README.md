# 🇹🇳 Expensya Tunisia - Gestion Automatisée des Notes de Frais

[![License](https://img.shields.io/badge/license-Proprietary-blue.svg)](LICENSE)
[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.x-green.svg)](https://vuejs.org)
[![TypeScript](https://img.shields.io/badge/TypeScript-5.x-blue.svg)](https://www.typescriptlang.org)

> Première solution SaaS tunisienne de gestion automatisée des notes de frais avec intelligence artificielle, conformité fiscale locale et expérience mobile-first.

## ✨ Caractéristiques Principales

### 🚀 Pour les Collaborateurs
- ✅ **OCR Intelligent**: Photo → Note de frais créée en 5 secondes
- ✅ **Multi-langue**: Reconnaissance Français, Arabe, Anglais
- ✅ **Mobile-First**: Application iOS & Android (Flutter)
- ✅ **Offline Mode**: Saisie hors-ligne avec synchronisation automatique
- ✅ **Remboursement Rapide**: En moyenne 5 jours (vs 21 jours actuellement)

### 💼 Pour l'Entreprise
- ✅ **Gain de Temps**: 80% de réduction du temps de traitement
- ✅ **Conformité Fiscale**: 100% conforme à la réglementation tunisienne
- ✅ **ROI Rapide**: Retour sur investissement en moins de 6 mois
- ✅ **Automatisation**: Workflow de validation multi-niveaux
- ✅ **Analytics**: Tableaux de bord et rapports en temps réel

### 🇹🇳 Spécificités Tunisiennes
- ✅ **TVA**: Taux 19%, 13%, 7%, 0% avec calculs automatiques
- ✅ **Matricule Fiscal**: Validation format XXXXXXX/X/X/XXX
- ✅ **Plan Comptable SCE**: Export compatible
- ✅ **Barèmes Kilométriques 2025**: Calculs automatiques selon puissance CV
- ✅ **Archivage Légal**: 10 ans (Code Commerce Tunisien)
- ✅ **INPDP**: Conforme protection données personnelles

## 🛠️ Stack Technologique

### Backend
- **Framework**: Laravel 11.x (PHP 8.3+)
- **Base de données**: PostgreSQL 16+
- **Cache/Queue**: Redis 7+
- **API**: RESTful avec Laravel Sanctum (JWT)
- **OCR**: Google Cloud Vision API + Tesseract
- **Storage**: S3-compatible (DigitalOcean Spaces)

### Frontend Web
- **Framework**: Vue.js 3 (Composition API)
- **Langage**: TypeScript 5.x
- **Build**: Vite 6.x
- **UI**: Tailwind CSS 3
- **State**: Pinia
- **Charts**: Chart.js / vue-chartjs

### Mobile (Phase 2)
- **Framework**: Flutter 3.x
- **Platforms**: iOS + Android
- **Offline**: Hive database
- **Sync**: Background synchronisation

### Infrastructure
- **Containers**: Docker + Docker Compose
- **CI/CD**: GitHub Actions
- **Hosting**: DigitalOcean / AWS
- **Monitoring**: Sentry + New Relic

## 📁 Structure du Projet

```
compta/
├── backend/                 # Laravel 11.x API
│   ├── app/
│   │   ├── Http/Controllers/
│   │   ├── Models/
│   │   └── Services/
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   └── routes/
├── frontend/               # Vue.js 3 + TypeScript
│   ├── src/
│   │   ├── assets/
│   │   ├── components/
│   │   ├── views/
│   │   ├── router/
│   │   ├── stores/
│   │   └── services/
│   └── public/
├── mobile/                 # Flutter (Phase 2)
│   └── lib/
├── docs/                   # Documentation
│   ├── ARCHITECTURE.md
│   ├── SETUP.md
│   └── API.md
├── docker-compose.yml      # Services Docker
└── README.md              # Ce fichier
```

## 🚀 Démarrage Rapide

### Prérequis
- Docker >= 24.0
- Docker Compose >= 2.20
- Git >= 2.30

### Installation

```bash
# 1. Cloner le repository
git clone https://github.com/haythemsaa/compta.git
cd compta

# 2. Configurer l'environnement
cp .env.example .env
cd backend && cp .env.example .env && php artisan key:generate && cd ..

# 3. Démarrer les services Docker
docker-compose up -d

# 4. Initialiser la base de données
docker exec -it expensya-backend php artisan migrate
docker exec -it expensya-backend php artisan db:seed

# 5. Accéder à l'application
# Frontend: http://localhost:3000
# Backend API: http://localhost:8000
# Mailhog: http://localhost:8025
```

### Compte de Test
- **Email**: demo@expensya.tn
- **Mot de passe**: demo

## 📚 Documentation

- **[Guide d'Installation Détaillé](docs/SETUP.md)**: Instructions complètes pour le développement
- **[Architecture Technique](docs/ARCHITECTURE.md)**: Vue d'ensemble du système
- **[Documentation API](docs/API.md)**: Référence complète des endpoints
- **[Spécifications Fonctionnelles](README_Specifications_Expensya_TN.md)**: Cahier des charges complet

## 🎯 Roadmap

### ✅ Phase 1 - MVP (Q1-Q2 2026)
- [x] Architecture & Setup
- [x] Backend API Laravel
- [x] Frontend Web Vue.js
- [ ] Intégration OCR
- [ ] Workflow de validation
- [ ] Frais kilométriques
- [ ] Tests & QA

### 🚧 Phase 2 - Growth (Q3-Q4 2026)
- [ ] Application mobile Flutter
- [ ] Ordres de mission
- [ ] Analytics avancés
- [ ] Intégrations ERP (Sage, Ciel)
- [ ] Détection fraudes (ML)

### 📅 Phase 3 - Scale (2027)
- [ ] Cartes de paiement professionnelles
- [ ] IA prédictive budgets
- [ ] Expansion Maghreb (Algérie, Maroc)
- [ ] SDK Mobile pour partenaires

## 🤝 Contribution

Ce projet est actuellement en développement privé. Pour toute question:
- Email: dev@expensya-tn.com
- Issues: https://github.com/haythemsaa/compta/issues

## 📊 KPIs de Succès

- ⏱️ Temps de saisie: **< 2 min/note**
- 🎯 OCR Accuracy: **> 95%**
- ⚡ Temps de validation: **< 48h**
- 💰 ROI client: **< 6 mois**
- 📈 Disponibilité: **> 99.5%**

## 🔒 Sécurité

- **Authentification**: JWT tokens (Laravel Sanctum)
- **Chiffrement**: AES-256 (repos), TLS 1.3 (transit)
- **RBAC**: 5 rôles (employee, manager, accountant, daf, admin)
- **Conformité**: INPDP Tunisie + RGPD-ready
- **Audit**: Logs immutables de toutes les opérations

## 📄 Licence

© 2025 Expensya Tunisia - Tous droits réservés
Document confidentiel - Ne pas diffuser

---

**Version**: 1.0.0
**Date**: 17 Novembre 2025
**Auteur**: Équipe Expensya TN
