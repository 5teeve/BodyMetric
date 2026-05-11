# BodyMetric — Application de Suggestion de Régimes Alimentaires

## 📋 Description du Projet

**BodyMetric** est une application web CodeIgniter 4 permettant aux utilisateurs de :
- Se créer un compte en 2 étapes (informations personnelles + données de santé)
- Calculer automatiquement leur IMC (Indice de Masse Corporelle)
- Sélectionner un objectif (augmenter, réduire, ou atteindre l'IMC idéal)
- Recevoir des suggestions personnalisées de régimes alimentaires et d'activités sportives
- Gérer un portefeuille virtuel avec recharge par code
- Accéder à une option Gold avec réductions exclusives (15%)
- Admin : gérer complètement la plateforme via un back-office complet

---

## 🛠️ Prérequis

- **PHP** : 8.0 minimum
- **MySQL/MariaDB** : 5.7 minimum
- **Composer** : pour les dépendances PHP

---

## 📦 Installation

### 1. Cloner le projet

```bash
git clone https://github.com/yourname/bodymetric.git
cd bodymetric
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Configurer le fichier `.env`

Copier `.env.example` en `.env` et ajuster les paramètres :

```bash
cp .env.example .env
```

Éditer `.env` :

```env
app.baseURL = 'http://localhost:8080/'
database.default.hostname = localhost
database.default.database = bodymetric
database.default.username = root
database.default.password = ''
database.default.DBDriver = MySQLi
```

### 4. Créer la base de données

```bash
mysql -u root -p < database/migration.sql
```

### 5. Générer la clé d'application

```bash
php spark key:generate
```

### 6. Lancer le serveur de développement

```bash
php spark serve
```

L'application sera accessible à : **http://localhost:8080**

---

## 🔐 Identifiants de Test

### Front-office — Utilisateur classique
- **Email** : `user@test.com`
- **Mot de passe** : `Password123`

### Back-office — Administrateur
- **ID Admin** : `1` (user_id = 1 = administrateur)
- **Email** : `admin@test.com`
- **Mot de passe** : `Admin@123`

---

## ✨ Fonctionnalités Implémentées

### Front-office

✅ **Authentification**
- Inscription en 2 étapes avec validation
- Connexion sécurisée
- Gestion de session
- Déconnexion

✅ **Profil Utilisateur**
- Affichage IMC avec catégorie
- Édition des données personnelles
- Recalcul IMC en temps réel
- Badge Gold

✅ **Objectif**
- Sélection d'objectif (augmenter/réduire/IMC idéal)
- Sauvegarde en session et base de données
- Interface visuelle avec cartes

✅ **Suggestion de Régimes**
- Algorithme de filtrage par objectif
- Affichage des régimes avec composition
- Remise 15% pour utilisateurs Gold
- Activités sportives associées
- Export PDF du plan

✅ **Portefeuille**
- Affichage du solde
- Recharge par code
- Historique des transactions
- Validation AJAX des codes

✅ **Option Gold**
- Page de présentation des avantages
- Achat via portefeuille
- Remise automatique sur les régimes

### Back-office

✅ **Authentification Admin**
- Login admin sécurisé
- Vérification des droits (user_id = 1)
- Session admin séparée

✅ **Tableau de Bord**
- KPI : utilisateurs, régimes, codes, Gold
- Graphe Chart.js des inscriptions par mois
- Graphe camembert des objectifs
- Responsive design

✅ **CRUD Régimes**
- Création/Édition/Suppression
- Validation composition (% = 100%)
- Association avec activités
- Pagination

✅ **CRUD Activités**
- Création/Édition/Suppression
- Niveaux : Débutant/Intermédiaire/Avancé
- Pagination

✅ **CRUD Codes Portefeuille**
- Génération en masse
- Invalidation manuelle
- Filtre par statut
- Pagination

✅ **CRUD Paramètres**
- Gestion centralisée des paramètres
- Prix Gold
- Seuils IMC
- Remise Gold

---

## 🗄️ Base de Données

### Tables principales

- **users** : utilisateurs, IMC, wallet, Gold
- **regimes** : régimes alimentaires avec composition
- **activites** : activités sportives
- **codes** : codes de recharge portefeuille
- **parametres** : configuration de l'app
- **regime_activite** : liaison régimes/activités (pivot)

### Données de test

La migration SQL insère automatiquement :
- 5 utilisateurs de test
- 5 régimes
- 5 activités
- 15 codes portefeuille
- Paramètres par défaut

---

## 🎨 Technologies Utilisées

- **Backend** : CodeIgniter 4
- **Base de données** : MySQL/MariaDB
- **Frontend** : HTML5, CSS3, JavaScript Vanilla
- **Graphes** : Chart.js
- **PDF** : DomPDF
- **Authentification** : Session CI4 + password_hash

---

## 📚 Routes Principales

### Front-office
- `GET /` → Accueil (redirection selon état)
- `GET /connexion` → Page de connexion
- `POST /connexion` → Traitement connexion
- `GET /inscription/step1` → Étape 1 inscription
- `POST /inscription/step1` → Traitement étape 1
- `GET /inscription/step2` → Étape 2 inscription
- `POST /inscription/step2` → Traitement inscription
- `GET /profil` → Profil utilisateur
- `POST /profil/perso-ajax` → Mise à jour profil
- `GET /objectif` → Sélection objectif
- `POST /objectif/store` → Sauvegarde objectif
- `GET /resultats` → Suggestion de régimes
- `GET /portefeuille` → Portefeuille
- `POST /ajax/portefeuille/valider-code` → Validation code
- `GET /gold` → Page Gold
- `GET /export-pdf` → Export PDF du plan

### Back-office
- `GET /bo` → Dashboard
- `GET /bo/regimes` → Gestion régimes
- `GET /bo/activites` → Gestion activités
- `GET /bo/codes` → Gestion codes
- `GET /bo/parametres` → Gestion paramètres

---

## 🐛 Débogage

### Logs
Les logs d'erreurs sont disponibles dans :
```
writable/logs/log-*.log
```

### Toolbar de débogage
Activée en développement : accessible en bas de chaque page

---

## 📝 Notes de développement

### Calcul IMC
```
IMC = poids (kg) / (taille (m))²
```

### Catégories IMC
- **Maigreur** : < 18.5
- **Normal** : 18.5 - 25
- **Surpoids** : 25 - 30
- **Obésité** : > 30

### Remise Gold
Appliquée automatiquement si `is_gold = 1` :
```
prix_affiche = prix_base * 0.85
```

---

## ✅ Checklist de Livraison

- [x] Base de données créée et testée
- [x] Front-office complet (inscription, objectif, suggestions)
- [x] Profil et édition utilisateur
- [x] Portefeuille et recharge par code
- [x] Option Gold avec remise
- [x] Back-office avec 4 CRUD + dashboard
- [x] Tests manuels complets
- [x] Déploiement en ligne

---

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

---

**Dernière mise à jour** : 11 mai 2026
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - The end of life date for PHP 8.1 was December 31, 2025.
> - If you are still using below PHP 8.2, you should upgrade immediately.
> - The end of life date for PHP 8.2 will be December 31, 2026.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library
