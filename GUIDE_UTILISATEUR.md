# Guide Utilisateur - Compteo TN

**Version**: 2.0.0  
**Date**: Novembre 2025  
**Application**: Gestion des Notes de Frais

---

## 📚 Table des Matières

1. [Introduction](#introduction)
2. [Connexion](#connexion)
3. [Dashboard](#dashboard)
4. [Créer un Rapport de Frais](#créer-un-rapport-de-frais)
5. [Ajouter des Dépenses](#ajouter-des-dépenses)
6. [Ajouter des Frais Kilométriques](#ajouter-des-frais-kilométriques)
7. [Soumettre un Rapport](#soumettre-un-rapport)
8. [Approuver/Rejeter (Managers)](#approuverrejeter-managers)
9. [Gérer les Véhicules](#gérer-les-véhicules)
10. [FAQ](#faq)

---

## 📖 Introduction

**Compteo TN** est votre outil de gestion des notes de frais professionnels. Simple, moderne et intuitif, il vous permet de :

✅ **Créer** des rapports de frais facilement  
✅ **Ajouter** vos dépenses avec justificatifs  
✅ **Calculer** automatiquement les frais kilométriques  
✅ **Soumettre** pour approbation  
✅ **Suivre** le statut de remboursement

---

## 🔐 Connexion

### Première Connexion

1. **Ouvrir l'application** : `https://compteo.votredomaine.tn`
2. **Saisir vos identifiants** :
   - Email professionnel
   - Mot de passe fourni par votre administrateur
3. **Cliquer sur "Se connecter"**

### Comptes de Démonstration

Pour tester l'application, utilisez ces comptes :

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Employé | employee@compteo.tn | password |
| Manager | manager@compteo.tn | password |
| Admin | admin@compteo.tn | password |

### Fonctionnalités de Connexion

- ✅ **Se souvenir de moi** : Reste connecté sur cet appareil
- ✅ **Mot de passe oublié** : Réinitialisation par email
- ✅ **Afficher/Masquer** le mot de passe (👁️)

---

## 📊 Dashboard

Après connexion, vous accédez au **Dashboard** avec :

### 📈 Statistiques en un Coup d'Œil

- **Brouillons** : Rapports non encore soumis
- **Soumis** : En attente d'approbation
- **Approuvés** : Validés par votre manager
- **Total du mois** : Montant cumulé

### 📋 Derniers Rapports

Liste des 5 derniers rapports avec :
- Titre et référence
- Montant total
- Statut (badge coloré)
- Lien rapide vers le détail

### 🥧 Répartition par Catégorie

Visualisez vos dépenses par catégorie :
- Restaurant 🍽️
- Transport 🚗
- Hébergement 🏨
- Etc.

### ⚡ Actions Rapides

4 raccourcis vers :
- Créer un nouveau rapport
- Voir tous mes rapports
- Gérer mes véhicules
- Paramètres

---

## 📝 Créer un Rapport de Frais

### Étape 1 : Accéder au Formulaire

**3 façons de créer un rapport** :
1. Bouton **"+ Nouveau rapport"** (header)
2. Dashboard → **"Nouveau rapport"** (quick actions)
3. Rapports de frais → **"Nouveau rapport"**

### Étape 2 : Remplir les Informations

#### Titre du Rapport (obligatoire)
- Soyez **descriptif** et **clair**
- Exemples :
  - ✅ "Déplacement client Sfax - Janvier 2025"
  - ✅ "Formation Paris - Semaine 12"
  - ❌ "Rapport 1" (trop vague)

#### Période (obligatoire)
- **Date de début** : Premier jour de la période
- **Date de fin** : Dernier jour de la période
- ⚠️ Maximum aujourd'hui (pas de dates futures)

#### Description (facultatif mais recommandé)
- Contexte de la mission
- Objectifs
- Personnes rencontrées
- Résultats obtenus

### Étape 3 : Créer

1. **Vérifier** les informations
2. **Cliquer** sur "Créer le rapport"
3. **Redirection** automatique vers le détail

💡 **Astuce** : Le rapport est créé en **brouillon**. Vous pourrez ajouter vos dépenses avant de le soumettre.

---

## 💰 Ajouter des Dépenses

### Accéder aux Dépenses

1. Ouvrir le **détail du rapport**
2. Section **"Dépenses"**
3. Cliquer **"+ Ajouter une dépense"**

### Formulaire de Dépense

#### 1. Catégorie (obligatoire)
Choisissez parmi 10 catégories :

| Catégorie | Icône | Exemples |
|-----------|-------|----------|
| Restaurant | 🍽️ | Repas client, déjeuner d'affaires |
| Transport | 🚗 | Taxi, métro, parking |
| Hébergement | 🏨 | Hôtel, Airbnb |
| Voyage | ✈️ | Train, avion |
| Fournitures | 🔧 | Matériel, équipements |
| Télécom | 📱 | Téléphone, internet |
| Formation | 🎓 | Cours, séminaires |
| Marketing | 📢 | Publicité, stands |
| Assurance | 🛡️ | Assurances pro |
| Autres | 📄 | Divers |

#### 2. Date (obligatoire)
- Date de la dépense
- Doit être dans la période du rapport
- Maximum aujourd'hui

#### 3. Montant TTC (obligatoire)
- Montant **avec TVA** en **TND**
- Format : `0.000` (3 décimales)
- Exemple : `45.750`

#### 4. Nom du Marchand (obligatoire)
- Nom du commerçant/fournisseur
- Exemples :
  - "Restaurant Le Gourmet"
  - "Hôtel Golden Tulip"
  - "Société ABC"

#### 5. Matricule Fiscal (facultatif)
- Format tunisien : `XXXXXXX/A/M/000`
- Utile pour la comptabilité
- Trouvé sur la facture

#### 6. Taux de TVA
Sélectionnez le taux applicable :

| Taux | Type | Produits/Services |
|------|------|-------------------|
| **19%** | Standard | Majorité des produits |
| **13%** | Réduit | Certains services |
| **7%** | Super-réduit | Produits de base |
| **0%** | Exonéré | Export, certains secteurs |

💡 Le taux est **pré-sélectionné à 19%** (le plus courant)

#### 7. Description (facultatif)
- Détails sur la dépense
- Contexte
- Personnes présentes

#### 8. Nombre de Convives (restaurants uniquement)
- Apparaît automatiquement pour la catégorie **Restaurant**
- Nombre de personnes au repas
- Utile pour justifier le montant

### Enregistrer la Dépense

1. **Vérifier** toutes les informations
2. **Cliquer** "Ajouter la dépense"
3. La dépense apparaît **immédiatement** dans la liste
4. Le **total du rapport** est mis à jour automatiquement

---

## 🚗 Ajouter des Frais Kilométriques

### Prérequis

⚠️ **Vous devez d'abord avoir un véhicule enregistré** :
1. Aller dans **"Véhicules"**
2. Cliquer **"+ Ajouter un véhicule"**
3. Remplir les informations (nom, immatriculation, puissance fiscale, etc.)

### Accéder aux Frais Kilométriques

1. Ouvrir le **détail du rapport**
2. Section **"Frais kilométriques"**
3. Cliquer **"+ Ajouter"**

### Formulaire de Frais Kilométrique

#### 1. Véhicule (obligatoire)
- Sélectionnez dans vos véhicules enregistrés
- Affiche la **puissance fiscale** (CV)
- Le **barème** est calculé automatiquement

#### 2. Date (obligatoire)
- Date du déplacement
- Doit être dans la période du rapport

#### 3. Objet du Déplacement (obligatoire)
- Raison du trajet
- Exemples :
  - "Visite client ABC"
  - "Réunion siège social"
  - "Livraison matériel"

#### 4. Trajet

**Lieu de départ** (obligatoire)
- Ville/adresse de départ
- Exemple : "Tunis"

**Lieu d'arrivée** (obligatoire)
- Ville/adresse d'arrivée
- Exemple : "Sfax"

#### 5. Distance

**2 options** :

**Option 1 : Calcul Automatique** ✨
1. Remplir départ et arrivée
2. Cliquer **"🔍 Calculer la distance"**
3. La distance est remplie automatiquement

**Option 2 : Saisie Manuelle**
- Entrer la distance en **kilomètres**
- Utiliser votre GPS/compteur

#### 6. Aller-Retour
- **Cocher** si vous avez fait l'aller-retour le même jour
- La distance sera **doublée automatiquement**
- Exemple : 
  - Distance simple : 100 km
  - Aller-retour coché : 200 km facturés

#### 7. Description (facultatif)
- Détails supplémentaires
- Contexte du déplacement

### Montant Estimé

💡 Une **carte d'estimation** apparaît avec :
- **Barème** appliqué (TND/km)
- **Distance totale** (x2 si aller-retour)
- **Montant estimé** en TND

### Barèmes Kilométriques 2025 (Tunisie)

Le tarif varie selon :
- **Puissance fiscale** du véhicule (4 à 8+ CV)
- **Distance annuelle** parcourue

| Puissance | 0-5000 km/an | 5001-10000 km/an | 10001-20000 km/an | +20000 km/an |
|-----------|--------------|------------------|-------------------|--------------|
| 4 CV | 0.290 TND/km | 0.235 TND/km | 0.190 TND/km | 0.170 TND/km |
| 5 CV | 0.310 TND/km | 0.255 TND/km | 0.210 TND/km | 0.190 TND/km |
| 6 CV | 0.330 TND/km | 0.275 TND/km | 0.230 TND/km | 0.210 TND/km |
| 7 CV | 0.360 TND/km | 0.305 TND/km | 0.260 TND/km | 0.240 TND/km |
| 8+ CV | 0.390 TND/km | 0.335 TND/km | 0.290 TND/km | 0.270 TND/km |

---

## 📤 Soumettre un Rapport

### Quand Soumettre ?

✅ **Vous devez soumettre quand** :
- Toutes les dépenses sont ajoutées
- Les justificatifs sont uploadés
- Les informations sont vérifiées

❌ **Ne pas soumettre si** :
- Des dépenses manquent
- Des informations sont incorrectes
- Vous attendez d'autres factures

### Comment Soumettre ?

1. **Ouvrir le rapport** en brouillon
2. **Vérifier** :
   - ✅ Titre et description clairs
   - ✅ Toutes les dépenses ajoutées
   - ✅ Frais km corrects
   - ✅ Total cohérent
3. **Cliquer** "Soumettre pour approbation"
4. **Confirmer** dans la popup

### Après Soumission

- ✅ Le rapport passe en statut **"Soumis"**
- ✅ Votre **manager** reçoit une notification
- ✅ Vous **ne pouvez plus modifier** le rapport
- ✅ Vous pouvez **suivre** le statut dans la liste

### Statuts Possibles

| Statut | Badge | Signification |
|--------|-------|---------------|
| Brouillon | ![Gris](https://via.placeholder.com/15/6c757d/6c757d.png) | Non soumis, modifiable |
| Soumis | ![Bleu](https://via.placeholder.com/15/0d6efd/0d6efd.png) | En attente d'approbation |
| Approuvé | ![Vert](https://via.placeholder.com/15/198754/198754.png) | Validé par le manager |
| Rejeté | ![Rouge](https://via.placeholder.com/15/dc3545/dc3545.png) | Refusé (voir raison) |
| Payé | ![Cyan](https://via.placeholder.com/15/0dcaf0/0dcaf0.png) | Remboursé |

---

## ✅ Approuver/Rejeter (Managers)

### Rôle du Manager

En tant que **Manager**, vous pouvez :
- Voir tous les rapports de votre équipe
- Approuver les rapports conformes
- Rejeter les rapports problématiques
- Demander des corrections

### Processus d'Approbation

#### 1. Accéder aux Rapports Soumis

- **Dashboard** → Voir les rapports en statut "Soumis"
- **Liste** → Filtrer par statut "Soumis"

#### 2. Examiner le Rapport

Vérifiez :
- ✅ **Titre et description** : Clairs et justifiés ?
- ✅ **Période** : Cohérente ?
- ✅ **Dépenses** : Justifiées et documentées ?
  - Montants raisonnables ?
  - Catégories correctes ?
  - TVA appropriée ?
- ✅ **Frais km** : Distances cohérentes ?
- ✅ **Total** : Dans le budget ?

#### 3. Décision

**Option A : Approuver** ✅
1. Cliquer **"✓ Approuver"**
2. Confirmer
3. Le rapport passe en "Approuvé"
4. L'employé est notifié
5. Le comptable peut traiter le paiement

**Option B : Rejeter** ❌
1. Cliquer **"✗ Rejeter"**
2. **Modal s'ouvre**
3. **Saisir la raison du rejet** (obligatoire)
   - Soyez clair et constructif
   - Expliquez ce qui ne va pas
   - Donnez des pistes de correction
4. Cliquer **"Confirmer le rejet"**
5. L'employé voit la raison et peut corriger

### Exemples de Raisons de Rejet

✅ **Bonnes raisons** (claires et actionnables) :
- "Le repas du 15/01 pour 450 TND semble excessif pour 2 personnes. Merci de justifier ou corriger."
- "Déplacement Tunis-Sousse : la distance indiquée (300 km) ne correspond pas à la réalité (140 km)."
- "Manque les justificatifs pour l'hôtel du 20/01 (120 TND)."

❌ **Mauvaises raisons** (vagues) :
- "Montant trop élevé"
- "Erreur"
- "À revoir"

---

## 🚙 Gérer les Véhicules

### Pourquoi Enregistrer un Véhicule ?

Pour **créer des frais kilométriques**, vous devez d'abord enregistrer vos véhicules.

### Ajouter un Véhicule

1. **Menu** → **"Véhicules"**
2. **Cliquer** "Ajouter un véhicule"
3. **Remplir** :
   - **Nom** : Ex. "Ma Clio"
   - **Marque** : Renault, Peugeot, etc.
   - **Modèle** : Clio, 208, etc.
   - **Immatriculation** : Format TN (ex: 123 TU 1234)
   - **Puissance fiscale** : 4, 5, 6, 7, ou 8+ CV
   - **Carburant** : Essence, Diesel, GPL, Électrique, Hybride
   - **Type** : Personnel ou Société
4. **Enregistrer**

### Voir le Barème

Sur chaque véhicule :
1. **Cliquer** "Voir barème"
2. **Modal affiche** :
   - Puissance fiscale
   - Tarif pour 5000 km/an
   - Info sur les tranches

### Modifier un Véhicule

1. **Carte du véhicule** → "Modifier"
2. **Mettre à jour** les informations
3. **Enregistrer**

### Désactiver un Véhicule

Si vous ne l'utilisez plus :
1. **Modifier** le véhicule
2. **Décocher** "Actif"
3. **Enregistrer**

Le véhicule n'apparaîtra plus dans les sélections mais reste dans l'historique.

---

## ❓ FAQ

### Général

**Q: Puis-je modifier un rapport après soumission ?**
❌ Non. Une fois soumis, le rapport est verrouillé. Si le manager le rejette, il repasse en brouillon et vous pourrez le modifier.

**Q: Combien de temps pour le remboursement ?**
⏱️ Cela dépend de votre organisation. Généralement :
- Approbation manager : 2-3 jours
- Traitement comptable : 5-7 jours
- Virement : selon votre banque

**Q: Puis-je grouper plusieurs missions dans un rapport ?**
✅ Oui, tant qu'elles sont dans la même période. Mais il est recommandé de séparer par mission pour plus de clarté.

### Dépenses

**Q: J'ai perdu mon justificatif, que faire ?**
1. Contacter le commerçant pour un duplicata
2. Sinon, expliquer dans la description
3. Votre manager décidera

**Q: Comment gérer la TVA sur un produit mixte ?**
Utilisez le taux **majoritaire** ou celui indiqué sur la facture.

**Q: Puis-je ajouter des frais en devises étrangères ?**
💱 Actuellement, seul le TND est supporté. Convertissez d'abord au taux du jour de la dépense.

### Frais Kilométriques

**Q: Dois-je créer un véhicule pour chaque voiture ?**
✅ Oui, un véhicule = une carte. Cela permet un suivi précis et des barèmes exacts.

**Q: Aller-retour le même jour ou sur 2 jours ?**
- **Même jour** : Cocher "Aller-retour"
- **2 jours différents** : Créer 2 frais km séparés

**Q: Le calculateur de distance est-il exact ?**
⚠️ C'est une estimation. Vérifiez avec votre GPS pour plus de précision.

**Q: Puis-je utiliser un véhicule de location ?**
❌ Les barèmes sont pour véhicules personnels. Les locations sont des **dépenses normales** (catégorie Transport).

### Technique

**Q: L'application fonctionne sur mobile ?**
✅ Oui ! Design 100% responsive. Fonctionne sur iPhone, Android, tablettes.

**Q: Puis-je utiliser l'app hors ligne ?**
❌ Pas encore. Une connexion Internet est nécessaire.

**Q: Comment obtenir de l'aide ?**
📞 Contactez votre administrateur ou consultez la documentation complète.

---

## 📞 Support

### Documentation

- **Guide utilisateur** : Ce document
- **Documentation technique** : README.md
- **API** : docs/API.md

### Contact

- **Support IT** : support@compteo.tn
- **Questions comptables** : compta@compteo.tn
- **Administrateur** : admin@compteo.tn

---

## 🎯 Conseils pour une Utilisation Optimale

### 📝 Bonnes Pratiques

1. **Créez un rapport par mois ou par mission**
   - Plus facile à gérer
   - Plus clair pour le manager
   - Meilleur suivi

2. **Ajoutez vos dépenses régulièrement**
   - Évitez d'attendre la fin du mois
   - Moins de risque d'oubli
   - Justificatifs frais

3. **Soyez précis dans les descriptions**
   - Qui, quoi, où, pourquoi
   - Facilite l'approbation
   - Évite les questions

4. **Conservez tous vos justificatifs**
   - Photos des reçus
   - Factures PDF
   - Emails de confirmation

5. **Vérifiez avant de soumettre**
   - Tous les montants
   - Toutes les catégories
   - Tous les justificatifs

### ⚡ Raccourcis Clavier

- `Ctrl + N` : Nouveau rapport (à venir)
- `Ctrl + S` : Enregistrer (à venir)
- `Esc` : Fermer modal

### 📱 Sur Mobile

- Menu **≡** en haut à gauche
- Scroll horizontal sur tables
- Cartes au lieu de tableaux
- Boutons optimisés pour le tactile

---

**Version** : 2.0.0  
**Dernière mise à jour** : Novembre 2025  
**© 2025 Compteo TN - Tous droits réservés**

🎉 **Bonne gestion de vos notes de frais !**
