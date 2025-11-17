# 📋 Cahier des Spécifications - Solution Expensya Tunisie

**Version:** 1.0  
**Date:** 17 Novembre 2025  
**Statut:** Draft

---

## 🎯 Vue d'Ensemble

### Vision Produit
Première solution SaaS tunisienne de gestion automatisée des notes de frais, combinant intelligence artificielle (OCR+, ML), conformité fiscale locale, et expérience mobile-first.

### Proposition de Valeur

**Pour les Collaborateurs:**
✅ Photo → Note de frais créée en 5 secondes  
✅ Remboursement en moyenne 5 jours (vs. 21 jours actuellement)  
✅ Zéro paperasse

**Pour l'Entreprise:**
✅ 80% de gain de temps sur le traitement  
✅ 100% de conformité fiscale tunisienne  
✅ ROI en <6 mois  
✅ Réduction coûts administratifs de 15-20 TND/note

---

## 📦 Documents Livrés

1. **Cahier_Specifications_Expensya_Tunisie.docx** - Document Word complet (200+ pages)
2. **Specifications_Techniques_Expensya_TN.pdf** - Résumé technique (20 pages)
3. **README_Specifications_Expensya_TN.md** - Ce fichier récapitulatif

---

## 🚀 Fonctionnalités Principales

### Phase 1 - MVP (0-6 mois)

#### 📸 Capture Intelligente OCR+
- Reconnaissance multi-langue (Français, Arabe, Anglais)
- Taux de reconnaissance >95%
- Extraction automatique:
  - Date, Montant HT/TTC/TVA
  - Fournisseur, Matricule fiscal
  - Catégorie (ML classification)
- Mode hors-ligne avec synchronisation

#### 💰 Gestion Notes de Frais
- Workflow validation multi-niveaux (manager → comptable)
- Politiques automatiques (plafonds, conformité)
- Multi-devises (TND, EUR, USD) avec taux BCT
- Gestion invités (repas d'affaires)
- Export comptable SCE compatible

#### 🚗 Frais Kilométriques
- Barèmes officiels Tunisie 2025 (4-8+ CV)
- Calcul automatique distance (Google Maps API)
- Multi-véhicules (entreprise/personnel)
- Aller simple / Aller-retour
- Justificatifs (carte grise, assurance)

#### 📱 Applications
- **Web:** Vue.js 3 + Tailwind CSS, responsive
- **Mobile:** Flutter (iOS + Android), offline-first
- **Backend:** Laravel 11.x + PostgreSQL + Redis

### Phase 2 - Growth (6-12 mois)

#### 📋 Ordres de Mission
- Demande avec budget prévisionnel
- Validation hiérarchique (manager → DAF si >1000 TND)
- Avances de frais (max 80% budget)
- Régularisation automatique (deadline 15j)
- Alertes dépassement budget

#### 📊 Analytics Avancés
- Dashboards temps réel (collaborateur, manager, DAF)
- Détection anomalies & fraudes (ML)
- Rapports personnalisés (PDF, Excel, PowerPoint)
- Alertes intelligentes
- Benchmarking sectoriel

#### 🔌 Intégrations Comptables
- Sage Tunisie
- Ciel Compta
- Format FEC standard
- API REST complète
- Webhooks temps réel

### Phase 3 - Scale (12-24 mois)

#### 💳 Cartes de Paiement Professionnelles
- Cartes virtuelles instantanées
- Budgets contrôlés par collaborateur/catégorie
- Transaction → Note de frais automatique
- Restrictions (catégories, zones géographiques, horaires)
- Notifications temps réel

#### 🤖 Intelligence Artificielle
- Prédiction budgets (ML)
- Assistant conversationnel
- Recommandations optimisation dépenses
- Détection patterns suspects

---

## 🇹🇳 Spécificités Tunisiennes

### Fiscalité TVA
| Taux | Application |
|------|------------|
| 19% | Taux standard (services, marchandises) |
| 13% | Taux réduit (secteurs spécifiques) |
| 7% | Taux super-réduit (alimentaire, médicaments) |
| 0% | Exonéré (exports) |

**Matricule Fiscal:** Format XXXXXXX/X/X/XXX avec validation automatique

### Plan Comptable SCE
- **Classe 6 - Charges:**
  - 625: Déplacements, missions, réceptions
  - 6251: Voyages et déplacements
  - 6252: Missions (indemnités km)
  - 6253: Réceptions (restaurants)
- **Classe 4 - Tiers:**
  - 436: Personnel - Créances
  - 4362: Avances
  - 4363: Notes de frais à rembourser

### Barèmes Kilométriques 2025

| Puissance | Jusqu'à 5000 km | 5000-10000 km | Au-delà 10000 km | Plafond annuel |
|-----------|----------------|---------------|------------------|----------------|
| 4 CV | 0.280 TND/km | 0.260 TND/km | 0.240 TND/km | 8,000 km |
| 5 CV | 0.310 TND/km | 0.290 TND/km | 0.270 TND/km | 10,000 km |
| 6 CV | 0.340 TND/km | 0.320 TND/km | 0.300 TND/km | 12,000 km |
| 7 CV | 0.370 TND/km | 0.350 TND/km | 0.330 TND/km | 15,000 km |
| 8+ CV | 0.400 TND/km | 0.380 TND/km | 0.360 TND/km | 18,000 km |

**Deux-roues:**
- Scooter/Moto 50cc: 0.150 TND/km (plafond 5,000 km)
- Moto 125cc+: 0.220 TND/km (plafond 8,000 km)

### Conformité Légale
- **Archivage:** 10 ans minimum (Code Commerce)
- **Valeur probante:** Loi 2000-83 (archivage numérique)
- **INPDP:** Conformité protection données personnelles
- **Déclarations:** Mensuelles/Trimestrielles selon CA

---

## 💻 Architecture Technique

### Stack Technologique

**Backend:**
- Framework: Laravel 11.x (PHP 8.3+)
- Database: PostgreSQL 16+ (principale), Redis (cache/queues)
- Storage: S3-compatible (DigitalOcean Spaces)
- OCR: Google Cloud Vision API + Tesseract (fallback)
- IA/ML: scikit-learn, TensorFlow Lite

**Frontend Web:**
- Framework: Vue.js 3 + TypeScript
- Build: Vite
- UI: Tailwind CSS 3
- Charts: Chart.js / ApexCharts
- State: Pinia

**Mobile:**
- Framework: Flutter 3.x (iOS + Android)
- Offline: Hive database locale
- Sync: Automatique à la reconnexion
- Notifications: Firebase Cloud Messaging

**Infrastructure:**
- Hébergement: DigitalOcean / AWS
- CI/CD: GitHub Actions
- Monitoring: Sentry + New Relic
- Disponibilité: >99.5% SLA

### Modèle de Données (Simplifié)

```
organizations (multi-tenant)
├── users (collaborateurs)
├── expense_reports (notes de frais)
│   ├── media (justificatifs)
│   └── mileage_expenses (frais km)
├── vehicles (parc automobile)
├── travel_orders (ordres de mission)
├── corporate_cards (cartes paiement)
├── expense_categories
├── projects
├── cost_centers
└── expense_policies (politiques)
```

### API REST

**Endpoints Principaux:**
```
POST   /api/auth/login
GET    /api/expense-reports
POST   /api/expense-reports
POST   /api/expense-reports/{id}/submit
POST   /api/expense-reports/{id}/approve
POST   /api/ocr/process
POST   /api/mileage/calculate-distance
GET    /api/reports/dashboard
POST   /api/reports/generate
```

---

## 🔐 Sécurité

### Authentification
- JWT Tokens (Laravel Sanctum)
- Access token: 1h, Refresh token: 7j
- 2FA optionnelle (TOTP)
- Rate limiting anti-brute force

### Autorisations (RBAC)
- **employee:** Créer/consulter ses notes
- **manager:** + Valider équipe
- **accountant:** + Traiter comptablement
- **daf:** + Analytics globales
- **admin:** + Configuration système

### Chiffrement
- Données repos: AES-256
- Données transit: TLS 1.3
- Mots de passe: Bcrypt (cost 12)
- Cartes bancaires: Tokenization PCI-DSS

### Conformité
- **INPDP Tunisie:** Déclaration + DPO
- **RGPD-ready:** Consentement + droit oubli
- **Archivage:** 10 ans légal
- **Audit trail:** Immutable logs

### Sauvegardes
- Automatiques quotidiennes (3h00)
- Rétention 30 jours
- Géo-redondance (2 datacenters)
- Tests restauration mensuels

---

## 📊 KPIs de Succès

### Adoption
- Taux d'activation: >80%
- Fréquence: >2 notes/mois/user
- NPS: >40
- Rétention 6 mois: >90%

### Efficacité
- Temps saisie: <2 min/note
- OCR accuracy: >95%
- Délai validation: <48h
- Réduction temps compta: >70%

### Qualité
- Taux d'erreur: <2%
- Conformité policies: >98%
- Disponibilité: >99.5%
- Response time API: <500ms (p95)

### Business
- Conversion trial: >20%
- Churn mensuel: <3%
- LTV: >24 mois
- ROI client: <6 mois

---

## 💰 Modèle Économique

### Formules d'Abonnement

| Formule | Prix/user/mois | Cible | Fonctionnalités clés |
|---------|----------------|-------|---------------------|
| **Essential** | 8 TND | TPE <20 users | Notes illimitées, OCR standard, 1 validation, Export basique |
| **Business** | 15 TND | PME 20-100 users | + Frais km, 3 validations, Ordres mission, Analytics, Intégrations |
| **Enterprise** | Sur devis | Grands comptes 100+ | + Cartes paiement, API, SLA 99.9%, Support dédié, Custom |

### Options
- **Essai gratuit:** 30 jours sans engagement
- **Support premium:** +3 TND/user/mois
- **Cartes physiques:** 15 TND/an/carte (Phase 3)
- **Onboarding:** 500-2000 TND (selon taille)

### Projection Revenus

**Année 1:**
- T1-T2: MVP + 50 clients (moyenne 25 users) → 15K TND MRR
- T3-T4: Growth + 200 clients (moyenne 30 users) → 90K TND MRR

**Année 2:**
- Scale + 500 clients (moyenne 40 users) → 300K TND MRR
- ARR: ~3.6M TND

---

## 🗓️ Roadmap Détaillée

### Q1 2026 - MVP Development
- ✅ Architecture & Setup
- ✅ Backend API (Laravel)
- ✅ OCR Integration
- ✅ Web App (Vue.js)
- ✅ Mobile App (Flutter)
- ✅ Tests & QA

### Q2 2026 - MVP Launch
- 🚀 Beta privée (10 clients pilotes)
- 🚀 Launch public
- 📈 Acquisition 50 premiers clients
- 🔧 Itérations rapides feedback
- 📊 Product-market fit validation

### Q3 2026 - Growth Features
- ⚡ Ordres de mission
- ⚡ Avances de frais
- ⚡ Analytics avancés
- 🔌 Intégrations ERP TN (Sage, Ciel)
- 📱 Optimisations mobile

### Q4 2026 - Expansion
- 📈 200 clients
- 🤖 ML améliorations (classification, fraude)
- 📊 Reporting avancé
- 🌍 Début expansion Maghreb
- 💰 Breakeven

### 2027 - Scale
- 💳 Cartes de paiement (Phase 3)
- 🤖 IA prédictive budgets
- 📱 Mobile SDK partenaires
- 🌍 Algérie, Maroc
- 💰 Série A fundraising

---

## 👥 Équipe Recommandée

### Phase MVP (0-6 mois) - 8 personnes
- **1 CTO/Lead Dev:** Architecture, backend Laravel
- **2 Développeurs Backend:** API, intégrations, OCR
- **2 Développeurs Frontend:** Vue.js (web) + Flutter (mobile)
- **1 UI/UX Designer:** Wireframes, prototypes, design system
- **1 Product Manager:** Specs, priorisation, tests utilisateurs
- **1 QA Engineer:** Tests, CI/CD, qualité

### Phase Growth (6-12 mois) - +4 personnes
- **+1 Développeur Backend:** Scalabilité, performances
- **+1 Data Scientist:** ML/IA (classification, fraude)
- **+1 DevOps Engineer:** Infrastructure, monitoring
- **+1 Customer Success:** Onboarding, support clients

### Phase Scale (12-24 mois) - +6 personnes
- **+2 Développeurs:** Features avancées (cartes, IA)
- **+1 Sales Manager:** Expansion commerciale
- **+2 Customer Success:** Croissance clients
- **+1 Comptable/Finance:** Conformité, audits

**Total Année 2:** ~18 personnes

---

## 💡 Risques & Mitigations

### Risques Techniques
- **OCR Accuracy <95%:** Entraînement modèle custom sur tickets TN
- **Scalabilité:** Architecture microservices + load balancing
- **Offline sync conflicts:** Stratégie CRDT + résolution serveur

### Risques Business
- **Adoption lente:** Programme pilotes gratuits, onboarding gratuit
- **Concurrence internationale:** Différenciation conformité TN + prix
- **Churn élevé:** Focus support client, success team dédié

### Risques Réglementaires
- **Conformité INPDP:** Déclaration préalable, audit externe annuel
- **Archivage légal:** Partenariat prestataire certifié (Leoni, etc.)
- **Évolution fiscalité:** Veille réglementaire, updates trimestrielles

---

## 📞 Contact & Support

**Documentation technique complète:**
- Cahier_Specifications_Expensya_Tunisie.docx (200+ pages)
- Specifications_Techniques_Expensya_TN.pdf (résumé 20 pages)

**Pour toute question:**
- Email: dev@expensya-tn.com
- Slack: #expensya-dev
- Wiki: wiki.expensya-tn.com

---

## 📄 Licence

© 2025 Expensya Tunisie - Tous droits réservés  
Document confidentiel - Ne pas diffuser

---

**Dernière mise à jour:** 17 Novembre 2025  
**Version:** 1.0  
**Auteur:** Équipe Produit Expensya TN
