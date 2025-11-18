# Checklist de Test et Déploiement - Compteo TN

**Version**: 2.0.0  
**Date**: Novembre 2025  
**Status**: À compléter avant production

---

## 📋 Tests Fonctionnels

### Authentification

- [ ] **Login avec email/password valides**
  - [ ] Employé peut se connecter
  - [ ] Manager peut se connecter
  - [ ] Admin peut se connecter
  - [ ] Token est stocké dans localStorage
  - [ ] Redirection vers dashboard

- [ ] **Login avec identifiants invalides**
  - [ ] Message d'erreur affiché
  - [ ] Animation shake sur erreur
  - [ ] Pas de redirection

- [ ] **Logout**
  - [ ] Token supprimé
  - [ ] Redirection vers login
  - [ ] Session terminée

- [ ] **Toggle password visibility**
  - [ ] Icône change (eye/eye-slash)
  - [ ] Type input change (password/text)

### Dashboard

- [ ] **Chargement des données**
  - [ ] Spinner affiché pendant chargement
  - [ ] Stats affichées correctement
  - [ ] Derniers rapports visibles
  - [ ] Répartition par catégorie affichée

- [ ] **Stats Cards**
  - [ ] Brouillons count correct
  - [ ] Soumis count correct
  - [ ] Approuvés count correct
  - [ ] Total mois affiché en TND

- [ ] **Derniers rapports**
  - [ ] Max 5 rapports affichés
  - [ ] Titre, référence, montant visibles
  - [ ] Badges de statut colorés
  - [ ] Clic redirige vers détail

- [ ] **Quick actions**
  - [ ] 4 cartes affichées
  - [ ] Liens fonctionnent
  - [ ] Hover effects actifs

### Liste des Rapports

- [ ] **Affichage**
  - [ ] Tous les rapports visibles
  - [ ] Table sur desktop
  - [ ] Cards sur mobile
  - [ ] Empty state si aucun rapport

- [ ] **Filtres**
  - [ ] Filtre par statut fonctionne
  - [ ] Filtre par date début fonctionne
  - [ ] Filtre par date fin fonctionne
  - [ ] Combinaison de filtres fonctionne

- [ ] **Navigation**
  - [ ] Clic sur ligne ouvre détail
  - [ ] Bouton "Nouveau" fonctionne
  - [ ] Breadcrumb visible

### Création de Rapport

- [ ] **Formulaire**
  - [ ] Tous les champs affichés
  - [ ] Placeholders clairs
  - [ ] Validation HTML5 active
  - [ ] Champs requis marqués (*)

- [ ] **Validation**
  - [ ] Titre requis
  - [ ] Période début requise
  - [ ] Période fin requise
  - [ ] Date fin >= date début
  - [ ] Dates <= aujourd'hui

- [ ] **Création**
  - [ ] Spinner pendant création
  - [ ] Redirection vers détail
  - [ ] Rapport en statut "draft"
  - [ ] Toast de succès (si implémenté)

- [ ] **Tips card**
  - [ ] 4 conseils affichés
  - [ ] Icônes visibles
  - [ ] Texte lisible

### Détail du Rapport

- [ ] **Affichage général**
  - [ ] Breadcrumb correct
  - [ ] Titre et référence affichés
  - [ ] Badge statut correct
  - [ ] Layout 2 colonnes (desktop)

- [ ] **Informations générales**
  - [ ] Titre éditable (si draft)
  - [ ] Dates affichées
  - [ ] Description éditable (si draft)
  - [ ] Card récapitulatif visible

- [ ] **Actions contextuelles**
  - [ ] "Enregistrer" visible si draft
  - [ ] "Soumettre" visible si draft non vide
  - [ ] "Approuver/Rejeter" visibles si submitted + manager
  - [ ] Boutons disabled pendant action

- [ ] **Liste dépenses**
  - [ ] Toutes les dépenses affichées
  - [ ] Catégorie avec icône
  - [ ] Montant et TVA visibles
  - [ ] Empty state si aucune dépense
  - [ ] Bouton "Ajouter" visible si draft

- [ ] **Liste frais km**
  - [ ] Tous les frais affichés
  - [ ] Trajet avec flèche
  - [ ] Distance et montant visibles
  - [ ] Badge "Aller-retour" si activé
  - [ ] Empty state si aucun frais
  - [ ] Bouton "Ajouter" visible si draft

### Modal Ajout Dépense

- [ ] **Ouverture/Fermeture**
  - [ ] Modal s'ouvre avec animation
  - [ ] Clic extérieur ferme modal
  - [ ] Bouton X ferme modal
  - [ ] ESC ferme modal

- [ ] **Formulaire**
  - [ ] Catégories chargées
  - [ ] Select avec icônes emoji
  - [ ] Date max = aujourd'hui
  - [ ] Input montant avec step 0.001

- [ ] **TVA Radio Buttons**
  - [ ] 4 options visibles
  - [ ] 19% sélectionné par défaut
  - [ ] Clic change sélection
  - [ ] Visual feedback (checked state)

- [ ] **Champ convives**
  - [ ] Visible seulement si catégorie Restaurant
  - [ ] Masqué pour autres catégories
  - [ ] Input number fonctionnel

- [ ] **Soumission**
  - [ ] Validation avant envoi
  - [ ] Spinner pendant save
  - [ ] Fermeture après succès
  - [ ] Liste mise à jour
  - [ ] Total recalculé

### Modal Frais Kilométrique

- [ ] **Chargement véhicules**
  - [ ] Liste des véhicules affichée
  - [ ] Puissance fiscale visible dans select
  - [ ] Message si aucun véhicule

- [ ] **Calcul distance**
  - [ ] Bouton disabled si pas départ/arrivée
  - [ ] Spinner pendant calcul
  - [ ] Distance remplie automatiquement
  - [ ] Message si erreur

- [ ] **Toggle aller-retour**
  - [ ] Switch visible et grand
  - [ ] État change au clic
  - [ ] Distance doublée dans calcul

- [ ] **Montant estimé**
  - [ ] Card visible si distance + véhicule
  - [ ] Barème affiché
  - [ ] Distance totale correcte (x2 si A/R)
  - [ ] Montant calculé exact

- [ ] **Soumission**
  - [ ] Validation complète
  - [ ] Save fonctionnel
  - [ ] Liste mise à jour
  - [ ] Total recalculé

### Workflow Rapports

- [ ] **Draft → Submitted**
  - [ ] Bouton "Soumettre" visible
  - [ ] Confirmation demandée
  - [ ] Statut change
  - [ ] Badge mis à jour
  - [ ] Boutons d'édition disparaissent

- [ ] **Submitted → Approved**
  - [ ] Bouton "Approuver" visible (manager)
  - [ ] Confirmation demandée
  - [ ] Statut change
  - [ ] Badge devient vert

- [ ] **Submitted → Rejected**
  - [ ] Bouton "Rejeter" visible (manager)
  - [ ] Modal raison s'ouvre
  - [ ] Raison obligatoire
  - [ ] Statut change
  - [ ] Alert rouge visible
  - [ ] Raison affichée

### Véhicules

- [ ] **Liste**
  - [ ] Tous les véhicules affichés
  - [ ] Grid responsive (1/2/3 colonnes)
  - [ ] Cards élégantes
  - [ ] Plaque stylisée
  - [ ] Empty state si aucun

- [ ] **Détails carte**
  - [ ] Nom et marque/modèle
  - [ ] Badge Actif/Inactif correct
  - [ ] Immatriculation dans plaque
  - [ ] Puissance, carburant, type affichés
  - [ ] Hover lift effect

- [ ] **Actions**
  - [ ] "Voir barème" fonctionne
  - [ ] Alert avec info barème
  - [ ] "Modifier" fonctionne (modal à implémenter)

---

## 🎨 Tests UI/UX

### Design System

- [ ] **Couleurs**
  - [ ] Palette cohérente
  - [ ] Gradients visibles
  - [ ] Contraste suffisant (WCAG AA)

- [ ] **Typographie**
  - [ ] Police système chargée
  - [ ] Hiérarchie claire (h1-h6)
  - [ ] Tailles lisibles
  - [ ] Line-height confortable

- [ ] **Icônes**
  - [ ] Bootstrap Icons chargées
  - [ ] Toutes les icônes visibles
  - [ ] Taille cohérente
  - [ ] Couleurs appropriées

### Animations

- [ ] **Page load**
  - [ ] FadeIn sur éléments
  - [ ] Cascade delays fonctionnels
  - [ ] Pas de flash of unstyled content

- [ ] **Modals**
  - [ ] ZoomIn à l'ouverture
  - [ ] Smooth close
  - [ ] Backdrop visible

- [ ] **Interactions**
  - [ ] Hover effects sur boutons
  - [ ] Card lift au hover
  - [ ] Transition smooth (0.3s)
  - [ ] Ripple effect sur boutons

- [ ] **Loading**
  - [ ] Spinners visibles
  - [ ] Messages appropriés
  - [ ] Pas de lag visuel

### Responsive

- [ ] **Mobile (< 768px)**
  - [ ] Menu hamburger fonctionne
  - [ ] Navigation collapse
  - [ ] Cards empilées
  - [ ] Table → Cards
  - [ ] Inputs pleine largeur
  - [ ] Boutons tactiles (min 44px)

- [ ] **Tablet (768-992px)**
  - [ ] Layout 2 colonnes
  - [ ] Navigation complète
  - [ ] Table visible
  - [ ] Modals adaptés

- [ ] **Desktop (> 992px)**
  - [ ] Layout complet
  - [ ] 3-4 colonnes grids
  - [ ] Sidebar visible
  - [ ] Toutes fonctionnalités

### Accessibilité

- [ ] **Keyboard**
  - [ ] Tab order logique
  - [ ] Focus visible
  - [ ] Enter soumet forms
  - [ ] ESC ferme modals

- [ ] **Screen readers**
  - [ ] Labels sur inputs
  - [ ] ARIA labels sur icons
  - [ ] Alt text sur images
  - [ ] Role attributes

- [ ] **Contraste**
  - [ ] Texte lisible
  - [ ] Ratios WCAG AA
  - [ ] Icons distinguables

---

## 🔧 Tests Techniques

### Performance

- [ ] **Temps de chargement**
  - [ ] Page login < 1s
  - [ ] Dashboard < 2s
  - [ ] Navigation < 500ms

- [ ] **Bundle size**
  - [ ] JS bundle < 500KB
  - [ ] CSS bundle < 100KB
  - [ ] Images optimisées

- [ ] **API calls**
  - [ ] Pas de calls inutiles
  - [ ] Cache utilisé quand possible
  - [ ] Interceptors fonctionnent

### Compatibilité

- [ ] **Navigateurs**
  - [ ] Chrome (latest)
  - [ ] Firefox (latest)
  - [ ] Safari (latest)
  - [ ] Edge (latest)

- [ ] **Appareils**
  - [ ] iPhone (Safari)
  - [ ] Android (Chrome)
  - [ ] iPad
  - [ ] Desktop Windows/Mac/Linux

### Sécurité

- [ ] **Auth**
  - [ ] Token sécurisé
  - [ ] Expiration gérée
  - [ ] 401 redirige vers login
  - [ ] Pas de token dans URL

- [ ] **XSS**
  - [ ] Inputs sanitisés
  - [ ] v-html évité
  - [ ] Content-Type correct

- [ ] **CSRF**
  - [ ] Token CSRF si nécessaire
  - [ ] Headers sécurisés

---

## 🚀 Checklist de Déploiement

### Backend

- [ ] **Configuration**
  - [ ] .env.production configuré
  - [ ] APP_DEBUG=false
  - [ ] APP_URL correct
  - [ ] Database credentials sécurisées

- [ ] **Migrations**
  - [ ] Toutes les migrations exécutées
  - [ ] Seed de production (catégories, etc.)
  - [ ] Pas de seed de démo

- [ ] **Optimisation**
  - [ ] `php artisan config:cache`
  - [ ] `php artisan route:cache`
  - [ ] `php artisan view:cache`
  - [ ] `composer install --optimize-autoloader --no-dev`

- [ ] **Permissions**
  - [ ] storage/ writable
  - [ ] bootstrap/cache/ writable
  - [ ] Propriétaire correct (www-data)

### Frontend

- [ ] **Configuration**
  - [ ] .env.production avec VITE_API_BASE_URL
  - [ ] Build production (`npm run build`)
  - [ ] dist/ généré

- [ ] **Optimisation**
  - [ ] Assets minifiés
  - [ ] Images optimisées
  - [ ] Tree-shaking actif
  - [ ] Lazy loading routes

### Infrastructure

- [ ] **Serveur**
  - [ ] PHP 8.3+ installé
  - [ ] PostgreSQL 16+ configuré
  - [ ] Redis installé et actif
  - [ ] Nginx/Apache configuré

- [ ] **SSL**
  - [ ] Certificat installé
  - [ ] HTTPS forcé
  - [ ] HTTP → HTTPS redirect
  - [ ] Mixed content résolu

- [ ] **DNS**
  - [ ] A record configuré
  - [ ] WWW redirect (si nécessaire)
  - [ ] Propagation complète

### Monitoring

- [ ] **Logs**
  - [ ] Laravel logs configurés
  - [ ] Nginx logs accessibles
  - [ ] Error reporting activé
  - [ ] Log rotation configurée

- [ ] **Backups**
  - [ ] Script backup database
  - [ ] Cron job configuré
  - [ ] Retention policy définie
  - [ ] Test de restauration effectué

- [ ] **Monitoring**
  - [ ] Uptime monitoring
  - [ ] Performance monitoring
  - [ ] Error tracking (Sentry, etc.)
  - [ ] Alertes configurées

---

## 📝 Documentation

- [ ] **Code**
  - [ ] Comments sur fonctions complexes
  - [ ] README.md à jour
  - [ ] API.md complète
  - [ ] DEPLOYMENT.md testée

- [ ] **Utilisateurs**
  - [ ] Guide utilisateur créé
  - [ ] FAQ complète
  - [ ] Vidéos tutoriels (bonus)
  - [ ] Support contact info

---

## ✅ Validation Finale

### Avant Production

- [ ] **Tests complets**
  - [ ] Tous les tests fonctionnels ✅
  - [ ] Tous les tests UI/UX ✅
  - [ ] Tous les tests techniques ✅
  - [ ] Tests utilisateurs réels effectués

- [ ] **Revue de code**
  - [ ] Pas de console.log en production
  - [ ] Pas de TODO critiques
  - [ ] Code formatté (ESLint/Prettier)
  - [ ] Pas de secrets dans le code

- [ ] **Performance**
  - [ ] Lighthouse score > 90
  - [ ] Time to interactive < 3s
  - [ ] Pas de memory leaks

### Go/No-Go Production

**Critères bloquants** (MUST) :
- [ ] Login/Logout fonctionnent
- [ ] Création rapport fonctionne
- [ ] Ajout dépenses fonctionne
- [ ] Workflow complet fonctionne
- [ ] Responsive sur mobile
- [ ] HTTPS configuré
- [ ] Backups configurés

**Critères recommandés** (SHOULD) :
- [ ] Toutes les animations fluides
- [ ] Tous les empty states
- [ ] Tous les messages d'aide
- [ ] Monitoring actif

**Critères bonus** (NICE TO HAVE) :
- [ ] Dark mode
- [ ] PWA
- [ ] Offline mode
- [ ] Push notifications

---

## 📊 Métriques de Succès

### Post-Déploiement (J+7)

- [ ] **Adoption**
  - [ ] X% utilisateurs connectés
  - [ ] Y rapports créés
  - [ ] Pas d'escalade support majeure

- [ ] **Performance**
  - [ ] Uptime > 99.5%
  - [ ] Temps réponse API < 200ms
  - [ ] Pas d'erreurs 500

- [ ] **Satisfaction**
  - [ ] Feedback utilisateurs positif
  - [ ] Pas de bugs critiques
  - [ ] Support tickets < 5/jour

---

**Version**: 2.0.0  
**Date**: Novembre 2025  
**Statut**: ⏳ Tests en cours

**Responsable Tests**: _____________  
**Responsable Déploiement**: _____________  
**Date Go/No-Go**: _____________  
**Date Production**: _____________

---

✅ **Checklist complétée** : __ / 200+  
🚀 **Prêt pour production** : ☐ Oui ☐ Non
