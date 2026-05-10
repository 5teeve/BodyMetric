# Projet S4 — BodyMetric

## QUOI
Application web CodeIgniter permettant à un utilisateur de sélectionner un régime alimentaire personnalisé selon son IMC et ses objectifs, avec gestion d'un portefeuille, option Gold et back-office d'administration complet.

---

## FONCTIONNALITÉS

**Front-office**
- Inscription en 2 étapes (informations personnelles puis données de santé)
- Calcul et affichage de l'IMC en temps réel
- Connexion / déconnexion
- Complétion et édition du profil utilisateur
- Choix d'un objectif : augmenter son poids, réduire son poids, ou atteindre son IMC idéal
- Suggestion de régimes alimentaires et d'activités sportives selon l'objectif et l'IMC
- Export du plan suggéré en PDF
- Portefeuille : recharge via code, historique des transactions
- Option Gold : achat unique, remise de 15% sur tous les régimes

**Back-office**
- Authentification administrateur sécurisée
- Tableau de bord avec statistiques (KPI, graphes, répartitions)
- CRUD des régimes (nom, % viande / % poisson / % volaille, durée, prix, delta poids)
- CRUD des activités sportives
- CRUD des codes portefeuille (génération, validation, invalidation)
- CRUD des paramètres généraux (prix Gold, seuils IMC, etc.)

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
