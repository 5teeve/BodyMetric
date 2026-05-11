# 🎯 Fonctionnalité "Mes Régimes" - Documentation Technique

## Vue d'ensemble

La fonctionnalité "Mes Régimes" permet aux utilisateurs de :
- **Voir les suggestions** de régimes et activités sur `/resultats`
- **Choisir/Acheter un régime** depuis la page de suggestions
- **Gérer leurs régimes** sur la page dédiée `/mes-regimes`
- **Annuler un régime** s'ils changent d'avis

## Architecture

### 1. Base de Données

#### Table: `regimes_achetes`
```sql
CREATE TABLE regimes_achetes (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  regime_id INT NOT NULL,
  prix_paye DECIMAL(10,2) NOT NULL,
  date_achat DATETIME DEFAULT CURRENT_TIMESTAMP,
  duree_jours INT DEFAULT 30,
  date_fin DATE,
  status ENUM('actif', 'termine', 'annule') DEFAULT 'actif',
  
  UNIQUE KEY (user_id, regime_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (regime_id) REFERENCES regimes(id) ON DELETE CASCADE
);
```

**Colonnes:**
- `id`: Identifiant unique
- `user_id`: Référence à l'utilisateur qui a choisi le régime
- `regime_id`: Référence au régime choisi
- `prix_paye`: Prix payé par l'utilisateur (après application remise Gold si applicable)
- `date_achat`: Date/heure d'achat
- `duree_jours`: Durée du régime en jours (default: 30)
- `date_fin`: Date de fin du régime (calculée: date_achat + duree_jours)
- `status`: État du régime (actif/termine/annule)

**Indices:**
- Primary key sur `id`
- Index sur `user_id`
- Index sur `regime_id`
- Unique constraint sur `(user_id, regime_id)` - empêche l'achat en double

### 2. Modèle: RegimesAchetesModel

**Fichier:** `app/Models/RegimesAchetesModel.php`

**Méthodes principales:**

```php
// Ajouter un régime acheté
addRegime($userId, $regimeId, $prixPaye, $dureeJours = 30)

// Récupérer les régimes actifs d'un utilisateur
getActiveByUser($userId) : array

// Récupérer tous les régimes d'un utilisateur
getAllByUser($userId) : array

// Récupérer les détails complets (avec données du régime)
getDetailsByUser($userId) : array

// Vérifier si un utilisateur a déjà acheté un régime
hasUserBought($userId, $regimeId) : bool

// Récupérer un régime acheté par ID
getById($id) : array

// Mettre à jour le statut d'un régime
updateStatus($id, $status) : bool

// Compter les régimes actifs d'un utilisateur
countActiveByUser($userId) : int
```

### 3. Contrôleur: RegimesController

**Fichier:** `app/Controllers/RegimesController.php`

#### Endpoint: `GET /mes-regimes`
**Méthode:** `myRegimes()`

Affiche la page des régimes achetés de l'utilisateur.

**Processus:**
1. Vérifie que l'utilisateur est connecté
2. Récupère l'ID utilisateur de la session
3. Récupère les régimes achetés avec détails via `RegimesAchetesModel::getDetailsByUser()`
4. Calcule les statistiques (total, actifs)
5. Formate les données pour l'affichage
6. Retourne la vue `regimes/my_regimes`

**Données retournées:**
```php
[
    'user' => User object,
    'regimes' => [
        [
            'id' => (int),
            'regime_id' => (int),
            'nom' => (string),
            'pct_viande' => (float),
            'pct_poisson' => (float),
            'pct_volaille' => (float),
            'duree' => (int),
            'prix_paye' => (float),
            'prix_original' => (float),
            'delta_poids' => (string),
            'date_achat' => (datetime),
            'date_fin' => (datetime),
            'status' => (string),
            'remise_appliquee' => (bool)
        ]
    ],
    'totalRegimes' => (int),
    'regimesActifs' => (int)
]
```

#### Endpoint: `POST /regimes/choisir`
**Méthode:** `choisir()`

Achète/choisit un régime pour l'utilisateur.

**Processus:**
1. Vérifie que c'est une requête POST
2. Vérifie que l'utilisateur est connecté
3. Valide l'ID du régime
4. Vérifie que le régime existe
5. Vérifie que l'utilisateur n'a pas déjà ce régime
6. Récupère les données utilisateur (wallet, is_gold)
7. Calcule le prix final (avec remise Gold si applicable):
   - Prix Gold: `prix * 0.85` (15% de réduction)
   - Prix normal: `prix`
8. Vérifie le solde du portefeuille
9. Effectue l'achat:
   - Insère dans `regimes_achetes`
   - Débite le wallet de l'utilisateur
   - Met à jour la session
10. Retourne la réponse JSON

**Validation/Erreurs:**
- `405`: Méthode non autorisée
- `400`: ID régime invalide
- `404`: Régime/Utilisateur non trouvé
- `409`: Régime déjà acheté
- `402`: Solde insuffisant
- `500`: Erreur serveur

**Réponse succès:**
```json
{
    "success": true,
    "message": "Régime choisi avec succès!",
    "prix_paye": 1000,
    "nouveau_solde": 5000
}
```

#### Endpoint: `POST /regimes/cancel/:id`
**Méthode:** `cancel($id)`

Annule un régime acheté.

**Processus:**
1. Vérifie que c'est une requête POST
2. Vérifie que l'utilisateur est connecté
3. Récupère le régime acheté
4. Vérifie que le régime appartient à l'utilisateur
5. Met à jour le statut en "annule"
6. Retourne la réponse JSON

**Validation/Erreurs:**
- `405`: Méthode non autorisée
- `404`: Régime non trouvé ou ne appartient pas à l'utilisateur
- `500`: Erreur serveur

**Réponse succès:**
```json
{
    "success": true,
    "message": "Régime annulé"
}
```

#### Endpoint: `GET /regimes/detail/:id`
**Méthode:** `detail($id)`

Récupère les détails d'un régime acheté (AJAX).

**Processus:**
1. Vérifie que l'utilisateur est connecté
2. Récupère le régime acheté
3. Vérifie que le régime appartient à l'utilisateur
4. Retourne les données en JSON

**Réponse succès:**
```json
{
    "id": 1,
    "user_id": 5,
    "regime_id": 10,
    "prix_paye": 1000,
    "date_achat": "2026-05-11 13:30:00",
    "duree_jours": 30,
    "date_fin": "2026-06-10",
    "status": "actif"
}
```

### 4. Vues

#### Vue: `app/Views/regimes/my_regimes.php`

Affiche la liste des régimes achetés de l'utilisateur.

**Éléments:**

**En-tête:**
- Titre "📋 Mes Régimes"
- Description

**Statistiques:**
- Nombre total de régimes
- Nombre de régimes en cours (actifs)

**État vide:**
- Affiche un message si aucun régime n'a été choisi
- Lien vers la page de suggestions

**Grille des régimes:**
- Cartes avec les informations du régime:
  - Nom du régime
  - Badge de statut (Actif/Terminé/Annulé)
  - Durée
  - Delta poids
  - Date d'achat et de fin (si actif)
  - Graphique de composition (viande/poisson/volaille)
  - Légende des pourcentages
  - Prix payé et remise appliquée

**Actions:**
- Bouton "📥 PDF": Exporte le plan en PDF
- Bouton "✕ Annuler": Annule le régime (uniquement si actif)

**Styles:**
- Dégradés verts pour le thème principal
- Cartes avec effets hover
- Responsive design (grid auto-fill)
- Codes couleur pour les statuts

#### Vue: `app/Views/resultats/index.php` (mise à jour)

Ajout du bouton "Choisir ce régime" pour chaque régime affiché.

**Ajout:**
```html
<button class="choose-regime-btn" onclick="chooseRegime(regimeId, this)">
    Choisir ce régime
</button>
```

**Styles:**
- Bouton vert dégradé
- Effet hover avec ombre
- État désactivé (loading)
- Spinner lors du chargement

**AJAX Handler:**
```javascript
async function chooseRegime(regimeId, button) {
    // Envoie une requête POST à /regimes/choisir
    // Gère les erreurs (402 solde insuffisant, 409 déjà acheté, etc.)
    // Redirige vers /mes-regimes en cas de succès
}
```

### 5. Routes

**Fichier:** `app/Config/Routes.php`

```php
// Afficher les régimes achetés
$routes->get('/mes-regimes', 'RegimesController::myRegimes');

// Choisir/acheter un régime
$routes->post('/regimes/choisir', 'RegimesController::choisir');

// Annuler un régime
$routes->post('/regimes/cancel/(:num)', 'RegimesController::cancel/$1');

// Détail d'un régime (AJAX)
$routes->get('/regimes/detail/(:num)', 'RegimesController::detail/$1');
```

## Flux d'utilisation

### 1. Utilisateur découvre les suggestions
```
Utilisateur → GET /resultats → Voir les régimes suggérés
```

### 2. Utilisateur choisit un régime
```
Utilisateur → Clique "Choisir ce régime" 
    → POST /regimes/choisir (AJAX)
    → Système valide: solde, prix, doublon
    → Insérer dans regimes_achetes
    → Débiter wallet
    → Mettre à jour session
    → Rediriger vers /mes-regimes
```

### 3. Utilisateur visualise ses régimes
```
Utilisateur → GET /mes-regimes 
    → RegimesController::myRegimes()
    → getDetailsByUser() JOIN avec régimes
    → Afficher les régimes avec statistiques
```

### 4. Utilisateur annule un régime
```
Utilisateur → Clique "Annuler"
    → POST /regimes/cancel/:id (AJAX)
    → Mettre à jour status en 'annule'
    → Rafraîchir la page
```

## Logique commerciale

### Calcul du prix
```php
$prix = $regime['prix'];  // Ex: 1000 Ar

if ($user['is_gold'] === 1) {
    $prixFinal = $prix * 0.85;  // 850 Ar avec remise
} else {
    $prixFinal = $prix;  // 1000 Ar sans remise
}
```

### Vérification du solde
```php
if ($wallet < $prixFinal) {
    // Erreur 402: Insufficient Funds
    return error_response(402, "Solde insuffisant");
}
```

### Prévention des doublons
```php
if ($this->regimesAchetesModel->hasUserBought($userId, $regimeId)) {
    // Erreur 409: Conflict
    return error_response(409, "Vous avez déjà ce régime");
}
```

### Calcul de la date de fin
```php
$dateFin = date('Y-m-d', strtotime('+' . $dureeJours . ' days'));
// Ex: 2026-05-11 + 30 days → 2026-06-10
```

## Gestion d'erreurs

### Codes HTTP utilisés
- `200`: Succès
- `400`: Requête invalide (ID régime manquant)
- `402`: Solde insuffisant
- `404`: Ressource non trouvée
- `405`: Méthode non autorisée
- `409`: Conflit (régime déjà acheté)
- `500`: Erreur serveur

### Messages d'erreur

| Erreur | Message | Cause |
|--------|---------|-------|
| 402 | Solde insuffisant | Le wallet de l'utilisateur est insuffisant |
| 404 | Régime non trouvé | Le régime_id n'existe pas |
| 404 | Utilisateur non trouvé | L'utilisateur n'existe pas |
| 409 | Vous avez déjà ce régime | Unique constraint violation |
| 400 | ID régime invalide | Le regime_id n'est pas un entier valide |
| 405 | Méthode non autorisée | La requête n'est pas POST pour les endpoints POST |

## Sécurité

### Vérifications appliquées

1. **Authentication**: Vérifier que l'utilisateur est connecté (session)
2. **Authorization**: Vérifier que l'utilisateur ne peut voir/modifier que ses régimes
3. **Validation des entrées**: Valider regime_id, duree_jours
4. **Validation des données**: Vérifier que le régime existe
5. **Validation métier**: Vérifier le solde, prévenir les doublons
6. **Sécurité AJAX**: Header `X-Requested-With: XMLHttpRequest`

### Protections

- Les IDs utilisateur sont extraits de la session (non des paramètres)
- Les modifications ne s'opèrent que via POST
- Les données sensibles (solde) ne sont jamais affichées en HTML brut
- Les requêtes AJAX valident le header de requête

## Configuration requise

### Base de données
- MySQL 5.7+ ou MariaDB 10.1+
- Collation: `utf8mb4_general_ci`

### Tables dépendantes
- `users`: Utilisateurs (id, wallet, is_gold)
- `regimes`: Régimes (id, nom, prix, duree, pct_viande, pct_poisson, pct_volaille, delta_poids)
- `regimes_activites`: Lien régime-activités (optionnel)
- `activites`: Activités recommandées (optionnel)

### Models requis
- `User`: Gestion des utilisateurs
- `RegimeModel`: Gestion des régimes
- `RegimesAchetesModel`: Gestion des régimes achetés

### Controllers requis
- `BaseController`: Contrôleur de base
- `ResultatsController`: Page des suggestions

### Vues requises
- `partials/header`: En-tête commune
- `regimes/my_regimes`: Page des régimes achetés
- `resultats/index`: Page des suggestions

## Performance

### Optimisations appliquées

1. **Indexes**: Index sur user_id, regime_id
2. **Foreign keys**: CASCADE pour intégrité référentielle
3. **Unique constraint**: Sur (user_id, regime_id) pour prévenir les doublons
4. **JOIN eager loading**: `getDetailsByUser()` récupère les données du régime en une seule requête
5. **Pagination**: Peut être ajoutée si besoin

### Requêtes typiques

```sql
-- Récupérer les régimes d'un utilisateur avec détails (1 requête)
SELECT regimes_achetes.*, regimes.nom, regimes.prix, ...
FROM regimes_achetes
JOIN regimes ON regimes.id = regimes_achetes.regime_id
WHERE regimes_achetes.user_id = ?
ORDER BY regimes_achetes.date_achat DESC

-- Vérifier un doublon (1 requête avec index)
SELECT id FROM regimes_achetes
WHERE user_id = ? AND regime_id = ? AND status = 'actif'

-- Ajouter un achat (1 requête INSERT)
INSERT INTO regimes_achetes (user_id, regime_id, prix_paye, ...)
VALUES (?, ?, ?, ...)

-- Débiter le wallet (1 requête UPDATE)
UPDATE users SET wallet = wallet - ?
WHERE id = ?
```

## Tests recommandés

### Tests fonctionnels
- [ ] Utilisateur non connecté → Redirection vers /connexion
- [ ] Page /mes-regimes vide → Affichage du message vide
- [ ] Choix d'un régime → Insertion dans regimes_achetes + débit wallet
- [ ] Choix du même régime 2x → Erreur 409
- [ ] Solde insuffisant → Erreur 402
- [ ] Annulation d'un régime → Changement de statut
- [ ] Remise Gold appliquée → Prix réduit de 15%

### Tests de sécurité
- [ ] Un utilisateur ne peut pas voir les régimes d'un autre
- [ ] Un utilisateur ne peut pas annuler le régime d'un autre
- [ ] Les requêtes POST valident CSRF (si activé)
- [ ] Les données en AJAX valident le header

### Tests de performance
- [ ] Page /mes-regimes avec 100 régimes → < 1s
- [ ] Achat d'un régime → < 500ms

## Fichiers créés/modifiés

### Créés
- ✅ `app/Models/RegimesAchetesModel.php` - Modèle pour les régimes achetés
- ✅ `app/Controllers/RegimesController.php` - Contrôleur principal
- ✅ `app/Views/regimes/my_regimes.php` - Page des régimes achetés
- ✅ `app/Database/Migrations/2026-05-11-130000_CreateRegimesAchetesTable.php` - Migration DB
- ✅ `database/006_regimes_achetes.sql` - Script SQL alternatif

### Modifiés
- ✅ `app/Views/resultats/index.php` - Ajout du bouton "Choisir"
- ✅ `app/Config/Routes.php` - Ajout des 4 routes

### Total: 8 fichiers (6 créés, 2 modifiés)

## Déploiement

1. **Préparation:**
   ```bash
   # Copier les fichiers
   git add -A
   git commit -m "feat: regime purchase system"
   ```

2. **Base de données:**
   ```bash
   # Option 1: Utiliser la migration CodeIgniter
   php spark migrate

   # Option 2: Script SQL direct
   mysql -u root -p body_metric_db < database/006_regimes_achetes.sql
   ```

3. **Vérification:**
   ```bash
   # Tester les routes
   curl http://localhost:8080/mes-regimes
   
   # Vérifier la table
   mysql -u root -p body_metric_db -e "DESCRIBE regimes_achetes;"
   ```

4. **Tests:**
   - Accéder à /resultats et cliquer sur "Choisir ce régime"
   - Vérifier dans /mes-regimes
   - Tester l'annulation

## Améliorations futures

- [ ] Historique d'achat avec détails
- [ ] Export PDF du plan du régime
- [ ] Notifications lors du changement de statut
- [ ] Recommandations d'activités par régime
- [ ] Suivi de progression (poids, etc.)
- [ ] Système de feedback/notation des régimes
- [ ] Renouvellement automatique des régimes
- [ ] Historique des prix payés
- [ ] Panier d'achat (plusieurs régimes à la fois)
- [ ] Coupons de réduction
- [ ] Statistiques d'utilisation pour le BO
