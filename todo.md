# Projet S4 — BodyMetric

## QUOI
Application web CodeIgniter permettant à un utilisateur de sélectionner un régime alimentaire personnalisé selon son IMC et ses objectifs, avec gestion d'un portefeuille, option Gold et back-office d'administration complet.

---

## ✅ FONCTIONNALITÉS IMPLÉMENTÉES

**Front-office**
- [x] Inscription en 2 étapes (informations personnelles puis données de santé)
- [x] Calcul et affichage de l'IMC en temps réel
- [x] Connexion / déconnexion
- [x] Complétion et édition du profil utilisateur
- [x] Choix d'un objectif : augmenter son poids, réduire son poids, ou atteindre son IMC idéal
- [x] Suggestion de régimes alimentaires et d'activités sportives selon l'objectif et l'IMC
- [x] Export du plan suggéré en PDF
- [x] Portefeuille : recharge via code, historique des transactions
- [x] Option Gold : achat unique via modal profil, remise de 15% sur tous les régimes

**Back-office**
- [x] Authentification administrateur (via `isAdminUser()` check user_id === 1)
- [x] Tableau de bord avec statistiques KPI + graphe Chart.js évolution inscriptions
- [x] CRUD des codes portefeuille (génération, validation, invalidation)
- [x] SuggestionController avec API activités sportives

---

## ✅ CORRECTIONS FLUX DE PAGES APPLIQUÉES

### ✅ Problèmes résolus dans Routes.php
1. **✅ Ligne 8-9**: Routes `'/'` dupliquées - **CORRIGÉ**
   - Route `/` unique dans `Routes.php` → `Home::index`
   - `Home::index()` redirige conditionnellement selon état connexion/profil
   
2. **✅ Flux inscription → connexion cassé** - **CORRIGÉ**
   - `AuthController::handleStep2()` : auto-login après inscription
   - Redirection vers `/objectif` après inscription réussie

3. **✅ Manque page Gold dédiée** - **CORRIGÉ**
   - Route `/gold` créée → `ProfilController::showGold()`
   - Vue `gold/index.php` avec design et bouton d'achat

4. **✅ Redirection connexion** - **CORRIGÉ**
   - `AuthController::handleLogin()` : vérification profil complet
   - Redirection vers `/profil` si incomplet, `/objectif` si pas d'objectif, `/resultats` sinon

---

## ✅ FONCTIONNALITÉS IMPLÉMENTÉES (suite)

### Back-office
- [x] **CRUD Régimes alimentaires** ✅
  - `Bo\RegimeController.php` avec index, form, store, update, delete
  - Vues `bo/regimes/index.php` et `bo/regimes/form.php`
  - Validation somme pourcentages = 100% (JS + PHP)
  - Routes: `/bo/regimes`, `/bo/regimes/form`, etc.

- [x] **CRUD Activités sportives** ✅
  - `Bo\ActiviteController.php` avec CRUD complet
  - Vues `bo/activites/index.php` et `bo/activites/form.php`
  - Gestion des enums: type, intensité, objectif
  - Routes: `/bo/activites`, `/bo/activites/form`, etc.

- [x] **CRUD Paramètres généraux** ✅
  - Table `parametres` (migration `005_11-05-2026_parametres.sql`)
  - `Bo\ParametreController.php` avec index, update
  - Vue `bo/parametres/index.php`
  - Paramètres: prix Gold, seuils IMC, remise Gold
  - Route: `/bo/parametres`

- [x] **Graphe répartition objectifs (camembert)** ✅
  - `DashboardController::getObjectivesDistribution()`
  - Chart.js pie chart dans `bo/dashboard.php`
  - Affichage des % utilisateurs par objectif

- [x] **Sidebar back-office responsive** ✅
  - `partials/sidebar_bo.php` avec navigation
  - Liens: Dashboard, Régimes, Activités, Codes, Paramètres
  - Intégrée dans toutes les vues BO
  - Responsive mobile (toggle menu)

### Front-office
- [x] **Page Gold dédiée** ✅
  - Route `/gold` → `ProfilController::showGold()`
  - Vue `gold/index.php` avec présentation avantages
  - CSS `gold.css` avec design professionnel
  - Bouton d'achat avec vérification solde

- [ ] **Amélioration Export PDF**
  - Ajouter régime choisi et activités suggérées au PDF (actuellement statique)
  - Intégrer données dynamiques depuis la session/DB

### Navigation / Layout
- [x] **Sidebar back-office responsive** ✅
- [x] **Amélioration navbar front-office** ✅
  - Badge GOLD affiché si `is_gold = 1`
  - Lien 'Passer Gold' si non Gold
  - Menu mobile hamburger avec animation
  - Overlay pour fermer le menu
  - Navigation responsive slide-in

---

## 📊 BASE DE DONNÉES - ÉTAT ACTUEL

**Tables existantes:**
- ✅ `users` (id, nom, prenom, email, mdp, genre, taille, poids, imc, wallet, is_gold, objectif, created_at)
- ✅ `regimes` (id, nom, pct_viande, pct_poisson, pct_volaille, duree, prix, delta_poids)
- ✅ `activites` (id, nom, type, intensite, duree_base, calories_min, objectif, description)
- ✅ `codes` (id, code, montant, statut, user_id, date_utilisation)
- ✅ `parametres` (id, cle, valeur, description, created_at, updated_at)

**Tables optionnelles (futures améliorations):**
- ⬜ `historique_transactions` (pour historique portefeuille détaillé)
- ⬜ `regimes_achetes` (pour tracker les régimes achetés par les utilisateurs)

---

## 🎯 RÉCAPITULATIF

### ✅ Toutes les fonctionnalités principales sont implémentées :

**Front-office complet :**
- Inscription 2 étapes avec auto-login
- Connexion avec redirection intelligente
- Profil éditable avec calcul IMC AJAX
- Choix d'objectif (réduire/augmenter/IMC idéal)
- Suggestions de régimes et activités personnalisées
- Portefeuille avec recharge par code
- Option Gold avec page dédiée et remise 15%
- Export PDF

**Back-office complet :**
- Dashboard avec 2 graphes Chart.js (inscriptions + objectifs)
- CRUD Régimes (avec validation composition 100%)
- CRUD Activités sportives
- CRUD Codes portefeuille
- CRUD Paramètres généraux
- Sidebar responsive sur toutes les pages BO

### 📝 Améliorations optionnelles restantes :
1. **Export PDF** - Intégrer régime choisi et activités dynamiques
2. **Navbar front** - Badge Gold, lien vers page Gold, menu mobile
3. **Historique transactions** - Table détaillée des mouvements portefeuille

---

## COMMENT

### Base de données
- Concevoir le schéma complet : tables `users`, `regimes`, `activites`, `codes`, `parametres`
  - Définir les colonnes, types, contraintes et clés étrangères
  - Rédiger le script SQL `migration.sql` avec toutes les instructions `CREATE TABLE`
  - Insérer les données minimales de test : 5 utilisateurs, 5 régimes, 5 activités, 15 codes

---

### Inscription (front-office)
- Créer la page étape 1 : formulaire nom, prénom, email, genre
  - Design HTML/CSS de la page avec indicateur de progression (étape 1/2)
  - Validation JavaScript côté client : champs obligatoires, format email
- Créer la page étape 2 : formulaire taille et poids
  - Design HTML/CSS avec affichage IMC calculé en temps réel via AJAX
  - Affichage visuel de la barre IMC avec catégorie (normal, surpoids, etc.)
- Implémenter la logique PHP/CodeIgniter d'inscription
  - Stocker les données entre les deux étapes via session CI4
  - Hasher le mot de passe et insérer l'utilisateur en base

---

### Connexion (front-office)
- Créer la page login : email + mot de passe
  - Design HTML/CSS simple et centré
- Implémenter la logique PHP/CodeIgniter
  - Vérification du hash, création de session, logout
  - Filtre d'authentification (redirection si non connecté)

---

### Profil (front-office)
- Créer la page profil : affichage et édition des informations
  - Design HTML/CSS avec affichage de l'IMC actuel et du badge Gold si applicable
  - Formulaire d'édition des informations personnelles et de santé
- Implémenter la mise à jour du profil via AJAX avec recalcul de l'IMC

---

### Objectif (front-office)
- Créer la page de sélection d'objectif
  - Design en 3 cartes cliquables : augmenter, réduire, IMC idéal
  - Mise en évidence visuelle de la carte sélectionnée
- Sauvegarder l'objectif en session et en base, rediriger vers les suggestions

---

### Suggestion de régimes (front-office)
- Créer la page de résultats
  - Design HTML/CSS : liste de régimes en cartes avec prix, durée, composition
  - Affichage des barres de composition (% viande / poisson / volaille)
  - Affichage du prix avec remise 15% si Gold
- Implémenter l'algorithme de suggestion PHP
  - Sélection des régimes compatibles avec l'objectif et le delta de poids
  - Sélection des activités sportives associées avec durée estimée
- Générer l'export PDF du plan (TCPDF ou DomPDF)
  - Résumé IMC + objectif + régime choisi + activités

---

### Portefeuille (front-office)
- Créer la page portefeuille
  - Design HTML/CSS : affichage du solde, champ de saisie de code, historique
- Implémenter la validation de code via AJAX
  - Vérifier que le code existe, est actif et non encore utilisé
  - Créditer le solde et marquer le code comme utilisé

---

### Option Gold (front-office)
- Créer la page Gold
  - Design HTML/CSS : présentation des avantages, prix unique, bouton d'achat
- Implémenter la logique d'achat
  - Vérifier que le solde est suffisant, débiter le portefeuille, activer le flag `is_gold`
  - Appliquer automatiquement la remise de 15% sur les régimes si `is_gold = 1`

---

### Navigation / layout (front-office)
- Créer la navbar responsive
  - Liens vers accueil, mes régimes, profil
  - État connecté (avatar + badge Gold) vs déconnecté
- Créer la sidebar back-office responsive
  - Liens vers toutes les sections d'administration

---

### Authentification back-office
- Créer la page de login administrateur
  - Design sobre sur fond sombre, formulaire identifiant + mot de passe
- Implémenter la logique : session admin séparée, vérification du rôle
- Middleware de protection : redirection si non admin

---

### Tableau de bord back-office
- Créer la page dashboard
  - Cards KPI : nombre d'utilisateurs, régimes vendus, codes validés, membres Gold
  - Graphe Chart.js : évolution des inscriptions par mois (courbe)
  - Graphe Chart.js : répartition des objectifs utilisateurs (camembert)
- Implémenter les requêtes SQL d'agrégation pour alimenter les graphes et KPI

---

### CRUD Régimes (back-office)
- Créer la liste des régimes
  - Tableau paginé avec colonnes : nom, composition, durée, prix, delta poids
  - Boutons éditer et supprimer par ligne
- Créer le formulaire de création / édition
  - Champs : nom, % viande, % poisson, % volaille (somme doit = 100%), durée, prix, delta poids min/max
- Implémenter le CRUD complet PHP/CodeIgniter avec validation

---

### CRUD Activités sportives (back-office)
- Créer la liste et le formulaire de création / édition
  - Champs : nom, description, durée minimale, calories/heure, niveau
- Implémenter le CRUD complet PHP/CodeIgniter

---

### CRUD Codes portefeuille (back-office)
- Créer la liste des codes
  - Tableau avec colonnes : code, montant, statut (actif/utilisé), utilisé par, date
  - Bouton d'invalidation par code
- Créer le formulaire de génération
  - Champs : montant, quantité à générer
  - Génération de codes aléatoires uniques
- Implémenter le CRUD complet PHP/CodeIgniter

---

### CRUD Paramètres (back-office)
- Créer la page de gestion des paramètres généraux
  - Prix Gold, seuils IMC (maigreur, surpoids, obésité)
- Implémenter le CRUD des entrées clé/valeur en base
