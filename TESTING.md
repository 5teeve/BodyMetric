# 📋 Plan de Test — BodyMetric

## Tests Manuels — Front-office ✅

### 1. Inscription (2 étapes)

**Prérequis** : Pas de compte existant avec cet email

**Étape 1 — Informations personnelles**
- [x] Accéder à `/inscription/step1`
- [x] Valider les champs obligatoires
  - [x] Prénom : remplir un prénom valide (ex: Jean)
  - [x] Nom : remplir un nom valide (ex: Dupont)
  - [x] Email : remplir un email valide (ex: user@test.com)
  - [x] Mot de passe : min 8 caractères (ex: Password123)
  - [x] Confirmation : doit correspondre au mot de passe
  - [x] Genre : sélectionner Femme/Homme/Autre
- [x] Validation JS en temps réel : vérifier les messages d'erreur
- [x] Cliquer "Continuer" : passer à étape 2

**Étape 2 — Données de santé**
- [x] Vérifier l'indicateur étape 2/2
- [x] Remplir les champs
  - [x] Taille : 170 cm
  - [x] Poids : 75 kg
- [x] Vérifier le calcul IMC en temps réel (IMC = 75 / (1.70²) ≈ 25.95)
- [x] Vérifier la catégorie IMC affichée (Surpoids)
- [x] Vérifier la barre de progression IMC colorée
- [x] Cliquer "S'inscrire" : créer le compte

**Résultat attendu** :
- Utilisateur créé en base de données
- Redirection vers `/objectif`
- Session utilisateur créée
- ✅ **SUCCÈS**

---

### 2. Connexion

**Prérequis** : Compte existant (créé à l'étape 1 ou `user@test.com`)

- [x] Accéder à `/connexion`
- [x] Remplir email : `user@test.com`
- [x] Remplir mot de passe : `Password123` (ou le mot de passe lors de l'inscription)
- [x] Cliquer "Connexion"

**Résultat attendu** :
- Redirection vers `/objectif` si pas d'objectif défini
- Redirection vers `/resultats` si objectif défini
- Session utilisateur créée avec (id, nom, is_gold)
- ✅ **SUCCÈS**

**Tests additionnels** :
- [x] Tester avec email incorrect : message d'erreur générique
- [x] Tester avec mot de passe incorrect : message d'erreur générique
- [x] Vérifier lien "Inscription" vers `/inscription/step1`

---

### 3. Profil Utilisateur

**Prérequis** : Utilisateur connecté

- [x] Accéder à `/profil`
- [x] Vérifier l'affichage
  - [x] Avatar avec initiales (première lettre prénom + première lettre nom)
  - [x] Badge IMC avec catégorie et couleur
  - [x] Badge Gold si `is_gold = 1` (sinon pas visible)
  - [x] Affichage des données personnelles : prénom, nom, email

**Mise à jour des données** :
- [x] Modifier la taille : 175 cm
- [x] Modifier le poids : 80 kg
- [x] Vérifier le recalcul IMC en temps réel
- [x] Vérifier la mise à jour de la catégorie IMC
- [x] Cliquer "Sauvegarder" via AJAX
- [x] Vérifier le message de succès

**Résultat attendu** :
- Données mises à jour en base de données
- Session mise à jour avec les nouvelles valeurs
- IMC recalculé : IMC = 80 / (1.75²) ≈ 26.12
- ✅ **SUCCÈS**

---

### 4. Choix d'Objectif

**Prérequis** : Utilisateur connecté sans objectif défini

- [x] Accéder à `/objectif`
- [x] Voir les 3 cartes
  - [x] Carte 1 : "Augmenter mon poids"
  - [x] Carte 2 : "Réduire mon poids"
  - [x] Carte 3 : "Atteindre mon IMC idéal"
- [x] Cliquer sur une carte : elle se met en évidence (bordure + coche)
- [x] Vérifier le bouton "Voir mes régimes suggérés" activé
- [x] Cliquer sur le bouton : redirection vers `/resultats`

**Résultat attendu** :
- Objectif sauvegardé en base de données
- Session mise à jour avec l'objectif
- Redirection vers `/resultats`
- ✅ **SUCCÈS**

---

### 5. Suggestion de Régimes

**Prérequis** : Utilisateur avec objectif défini

- [x] Accéder à `/resultats` ou `/resultats` directement
- [x] Vérifier l'affichage
  - [x] Objectif actif affiché en haut
  - [x] IMC actuel avec catégorie
  - [x] Liste des régimes suggérés (max 3 cartes)

**Chaque carte de régime doit afficher** :
- [x] Nom du régime
- [x] Durée en jours
- [x] Delta poids attendu (ex: +4 à +7 kg)
- [x] Barre de composition (% viande, poisson, volaille)
- [x] Pourcentages textuels sous la barre
- [x] Prix de base en Ariary
- [x] Badge "Recommandé" sur le premier résultat

**Remise Gold** :
- [x] Si utilisateur NOT Gold : affichage prix normal
- [x] Si utilisateur IS Gold : affichage remise 15%
  - [x] Ancien prix barré (format: 50000 Ar)
  - [x] Nouveau prix en vert (format: 42500 Ar)

**Section Activités** :
- [x] Lister les activités associées au régime sélectionné
- [x] Affichage : fréquence/semaine + durée/séance

**Export PDF** :
- [x] Cliquer "Exporter en PDF"
- [x] Fichier téléchargé avec nom : `plan_regime.pdf`
- [x] Contenu du PDF :
  - [x] Nom et prénom utilisateur
  - [x] IMC actuel et objectif
  - [x] Régime choisi (nom, prix, composition)
  - [x] Activités sportives suggérées

**Résultat attendu** :
- Régimes filtrés selon objectif :
  - Augmenter → delta_poids_max > 0
  - Réduire → delta_poids_min < 0
  - IMC idéal → deltas proches de 0
- Remise Gold appliquée si applicable
- PDF généré et téléchargé correctement
- ✅ **SUCCÈS**

---

### 6. Portefeuille

**Prérequis** : Utilisateur connecté, codes générés dans le back-office

- [x] Accéder à `/portefeuille`
- [x] Vérifier l'affichage
  - [x] Card solde : montant actuel en grand
  - [x] Champ saisie code (style monospace)
  - [x] Bouton "Valider"

**Recharge avec code valide** :
- [x] Entrer un code actif (ex: `NP-XXXX-XXXX`)
- [x] Cliquer "Valider"
- [x] Vérifier le message de succès : "Montant X crédité"
- [x] Vérifier le solde mis à jour en temps réel
- [x] Vérifier le code marqué comme "utilisé" en base

**Recharge avec code déjà utilisé** :
- [x] Entrer le même code
- [x] Vérifier le message d'erreur : "Code déjà utilisé"

**Recharge avec code invalide** :
- [x] Entrer un code inexistant
- [x] Vérifier le message d'erreur : "Code invalide"

**Historique des transactions** :
- [x] Affichage de l'historique avec dates, libellés et montants
- [x] Montants crédit en vert (+), débit en rouge (-)

**Résultat attendu** :
- Solde de l'utilisateur crédité
- Code marqué comme utilisé en base de données
- Historique mis à jour
- ✅ **SUCCÈS**

---

### 7. Option Gold

**Prérequis** : Utilisateur connecté, solde suffisant (>= prix Gold)

- [x] Accéder à `/gold`
- [x] Vérifier l'affichage
  - [x] Icône couronne + titre "Option Gold NutriPlan"
  - [x] Liste des avantages : -15%, accès premium, support, accès à vie
  - [x] Prix unique (récupéré depuis paramètres)
  - [x] Solde actuel affiché

**Achat Gold avec solde suffisant** :
- [x] Vérifier le bouton "Activer l'option Gold" activé
- [x] Cliquer sur le bouton
- [x] Vérifier le message de succès
- [x] Vérifier le solde réduit
- [x] Vérifier le badge Gold affiché dans la navbar

**Achat Gold avec solde insuffisant** :
- [x] Créer un nouvel utilisateur avec solde faible
- [x] Accéder à `/gold`
- [x] Vérifier le message d'avertissement : "Solde insuffisant"
- [x] Vérifier le bouton "Activer l'option Gold" désactivé

**Déjà Gold** :
- [x] Accéder à `/gold` avec un utilisateur déjà Gold
- [x] Vérifier le message : "Vous possédez déjà l'option Gold"
- [x] Vérifier le badge confirmant le statut Gold

**Résultat attendu** :
- `is_gold` passé à 1 en base de données
- Solde réduit du prix Gold
- Session mise à jour
- Badge Gold affiché partout
- Remise 15% appliquée sur les régimes
- ✅ **SUCCÈS**

---

### 8. Déconnexion

**Prérequis** : Utilisateur connecté

- [x] Cliquer le bouton "Déconnexion" (navbar ou profil)
- [x] Vérifier la redirection vers `/connexion`
- [x] Vérifier que la session est détruite
- [x] Tenter d'accéder à `/profil` : redirection vers `/connexion`

**Résultat attendu** :
- Session supprimée
- Impossible d'accéder aux routes protégées
- ✅ **SUCCÈS**

---

## Tests Manuels — Back-office ✅

### 1. Authentification Admin

**Prérequis** : `user_id = 1` dans la base de données

- [x] Accéder à `/bo`
- [x] Redirection vers `/bo/login` (pas connecté)
- [x] Remplir identifiant : `admin@test.com` (ou email user_id=1)
- [x] Remplir mot de passe : `Admin@123` (ou mot de passe user_id=1)
- [x] Cliquer "Connexion"
- [x] Vérifier la redirection vers `/bo/dashboard`

**Tests additionnels** :
- [x] Identifiants incorrects : message d'erreur générique
- [x] Session admin créée (vérifier dans les cookies)

**Résultat attendu** :
- Session admin créée avec admin_id et admin_nom
- Accès au back-office autorisé
- ✅ **SUCCÈS**

---

### 2. Tableau de Bord

**Prérequis** : Admin connecté

- [x] Accéder à `/bo/dashboard`
- [x] Vérifier les cards KPI
  - [x] Nombre total d'utilisateurs
  - [x] Nombre de régimes vendus
  - [x] Codes validés vs émis
  - [x] Membres Gold

**Graphe Chart.js — Courbe des inscriptions** :
- [x] Affichage du graphe en courbe
- [x] Données correctes pour les 6 derniers mois
- [x] Couleur verte (#1A7A48)

**Graphe Chart.js — Camembert des objectifs** :
- [x] Affichage du camembert
- [x] 3 segments : Augmenter, Réduire, IMC idéal
- [x] Couleurs : vert, rouge, orange
- [x] Pourcentages affichés

**Tableau top régimes** :
- [x] Affichage des 5 régimes les plus vendus
- [x] Colonnes : nom, prix, ventes

**Résultat attendu** :
- Dashboard complet et fonctionnel
- Graphes Chart.js opérationnels
- KPI à jour avec les données réelles
- ✅ **SUCCÈS**

---

### 3. CRUD Régimes

**Prérequis** : Admin connecté

**Liste des régimes** :
- [x] Accéder à `/bo/regimes`
- [x] Vérifier le tableau paginé
  - [x] Colonnes : nom, composition, durée, prix, delta poids
  - [x] Boutons Éditer et Supprimer par ligne
  - [x] Bouton "Nouveau régime"
  - [x] Filtre par objectif (optionnel)

**Créer un régime** :
- [x] Cliquer "Nouveau régime"
- [x] Remplir les champs
  - [x] Nom : "Régime Test"
  - [x] % Viande : 40
  - [x] % Poisson : 35
  - [x] % Volaille : 25 (somme = 100%)
  - [x] Durée : 30 jours
  - [x] Prix : 150000 Ar
  - [x] Delta poids min : -5
  - [x] Delta poids max : -2
  - [x] Sélectionner activités (checkboxes)
- [x] Vérifier la barre de composition en temps réel
- [x] Cliquer "Sauvegarder"

**Éditer un régime** :
- [x] Cliquer "Éditer" sur une ligne
- [x] Modifier les champs
- [x] Vérifier la validation : somme % = 100%
- [x] Cliquer "Sauvegarder"

**Supprimer un régime** :
- [x] Cliquer "Supprimer"
- [x] Confirmation (optionnel)
- [x] Régime supprimé de la liste et de la base de données

**Résultat attendu** :
- Régimes créés/modifiés/supprimés correctement
- Validation composition 100% respectée
- Pivots régimes/activités gérés correctement
- ✅ **SUCCÈS**

---

### 4. CRUD Activités

**Prérequis** : Admin connecté

**Liste des activités** :
- [x] Accéder à `/bo/activites`
- [x] Vérifier le tableau paginé
  - [x] Colonnes : nom, niveau, fréquence, durée
  - [x] Boutons Éditer et Supprimer

**Créer une activité** :
- [x] Cliquer "Nouvelle activité"
- [x] Remplir les champs
  - [x] Nom : "Yoga"
  - [x] Description : "Séance de yoga relaxante"
  - [x] Fréquence/semaine : 3
  - [x] Durée/séance : 45 minutes
  - [x] Niveau : Débutant / Intermédiaire / Avancé
- [x] Cliquer "Sauvegarder"

**Éditer une activité** :
- [x] Cliquer "Éditer"
- [x] Modifier les champs
- [x] Cliquer "Sauvegarder"

**Supprimer une activité** :
- [x] Cliquer "Supprimer"
- [x] Activité supprimée

**Résultat attendu** :
- Activités CRUD opérationnelles
- Données persistées en base de données
- ✅ **SUCCÈS**

---

### 5. CRUD Codes Portefeuille

**Prérequis** : Admin connecté

**Liste des codes** :
- [x] Accéder à `/bo/codes`
- [x] Vérifier le tableau
  - [x] Colonnes : code, montant, statut, utilisé par, date
  - [x] Bouton "Générer des codes"
  - [x] Filtre par statut (Tous / Actifs / Utilisés)

**Générer des codes** :
- [x] Cliquer "Générer des codes"
- [x] Remplir les champs
  - [x] Montant : 50000 Ar
  - [x] Quantité : 5
- [x] Cliquer "Générer"
- [x] Affichage de la liste des codes générés (format `NP-XXXX-XXXX`)
- [x] Codes copiables

**Invalider un code** :
- [x] Cliquer "Invalider" sur un code actif
- [x] Statut changé à "Invalide"

**Filtre par statut** :
- [x] Sélectionner "Actifs" : afficher que les codes actifs
- [x] Sélectionner "Utilisés" : afficher que les codes utilisés

**Résultat attendu** :
- Codes générés avec format unique
- CRUD complet fonctionnel
- Filtres opérationnels
- ✅ **SUCCÈS**

---

### 6. CRUD Paramètres

**Prérequis** : Admin connecté

- [x] Accéder à `/bo/parametres`
- [x] Vérifier le tableau
  - [x] Colonnes : clé, valeur, description
  - [x] Bouton "Sauvegarder" par ligne

**Paramètres à tester** :
- [x] `prix_gold` : modifier à 300000 Ar
- [x] `imc_maigreur` : modifier à 18.5
- [x] `imc_surpoids` : modifier à 25
- [x] `imc_obesite` : modifier à 30
- [x] `remise_gold_pct` : modifier à 15

**Modification d'un paramètre** :
- [x] Cliquer sur la valeur (éditable inline)
- [x] Modifier la valeur
- [x] Cliquer "Sauvegarder"
- [x] Vérifier le message de succès

**Vérification de la prise en compte** :
- [x] Changer le prix Gold à 200000
- [x] Créer un nouvel utilisateur Gold
- [x] Vérifier que le prix Gold affiché est 200000 sur `/gold`
- [x] Acheter Gold et vérifier la déduction correcte

**Résultat attendu** :
- Paramètres modifiés en base de données
- Valeurs utilisées partout dans l'application
- ✅ **SUCCÈS**

---

## ✅ Résumé des Tests

| Fonctionnalité | Front-office | Back-office | Statut |
|---|---|---|---|
| Authentification | ✅ | ✅ | SUCCÈS |
| Inscription | ✅ | - | SUCCÈS |
| Profil | ✅ | - | SUCCÈS |
| Objectif | ✅ | - | SUCCÈS |
| Suggestion | ✅ | - | SUCCÈS |
| Portefeuille | ✅ | - | SUCCÈS |
| Gold | ✅ | - | SUCCÈS |
| Dashboard | - | ✅ | SUCCÈS |
| CRUD Régimes | - | ✅ | SUCCÈS |
| CRUD Activités | - | ✅ | SUCCÈS |
| CRUD Codes | - | ✅ | SUCCÈS |
| CRUD Paramètres | - | ✅ | SUCCÈS |

---

## 🎯 Conclusion

✅ **TOUS LES TESTS RÉUSSIS**

Le projet BodyMetric est complètement fonctionnel et prêt pour la livraison.

- Toutes les fonctionnalités front-office et back-office sont implémentées
- Tous les tests manuels ont été exécutés avec succès
- Pas de bugs critiques détectés
- Application stable et prête pour production

**Date de finalisation** : 11 mai 2026
