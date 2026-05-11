# NutriPlan — Todo liste détaillée

---

## Base de données ✅

- [x] Créer le script `base.sql`
  - [x] Table `users` : `id`, `nom`, `prenom`, `email`, `password`, `genre`, `taille`, `poids`, `imc`, `objectif`, `wallet`, `is_gold`, `created_at`
  - [x] Table `regimes` : `id`, `nom`, `pct_viande`, `pct_poisson`, `pct_volaille`, `duree_jours`, `prix`, `delta_poids_min`, `delta_poids_max`
  - [x] Table `activites` : `id`, `nom`, `description`, `frequence_semaine`, `duree_minutes`, `niveau`
  - [x] Table `codes` : `id`, `code`, `montant`, `statut` (actif/utilisé), `user_id`, `used_at`
  - [x] Table `parametres` : `id`, `cle`, `valeur`, `description`
  - [x] Table `regime_activite` (pivot) : `id`, `regime_id`, `activite_id`
  - [x] Ajouter les contraintes : clés étrangères, `NOT NULL`, `UNIQUE` sur `email` et `code`
  - [x] Rédiger le script d'insertion des données minimales
    - [x] 5 utilisateurs (genres variés, IMC variés)
    - [x] 5 régimes (compositions et objectifs différents)
    - [x] 5 activités sportives (niveaux variés)
    - [x] 15 codes portefeuille (montants variés, statuts mixtes)
    - [x] Paramètres : prix Gold, seuils IMC (< 18.5 maigreur, 18.5–25 normal, 25–30 surpoids, > 30 obésité)

---

## Layout & navigation ✅

- [x] Créer le layout front-office `layout/main.php`
  - [x] Navbar : logo NutriPlan, liens Accueil / Mes régimes / Profil / Portefeuille
  - [x] Affichage conditionnel si connecté : avatar initiales + badge Gold si `is_gold = 1`
  - [x] Affichage si non connecté : boutons Connexion / Inscription
  - [x] Responsive mobile : menu hamburger
  - [x] Intégrer le footer : copyright, liens légaux
- [x] Créer le layout back-office `layout/bo.php`
  - [x] Sidebar fixe à gauche : logo + section "Principal" (Dashboard, Régimes, Activités) + section "Gestion" (Codes, Utilisateurs, Paramètres)
  - [x] Lien actif mis en évidence selon la page courante
  - [x] Header top : nom de l'admin connecté + bouton déconnexion
  - [x] Responsive : sidebar rétractable sur petits écrans

---

## Inscription ✅

- [x] Créer `register_step1.php` — informations personnelles
  - [x] Champ prénom (obligatoire)
  - [x] Champ nom (obligatoire)
  - [x] Champ email (obligatoire, format valide)
  - [x] Champ mot de passe (obligatoire, min 8 caractères)
  - [x] Champ confirmation mot de passe (doit correspondre)
  - [x] Sélecteur genre : Femme / Homme / Autre (boutons radio stylisés)
  - [x] Indicateur de progression : étape 1 sur 2
  - [x] Validation JavaScript en temps réel : messages d'erreur sous chaque champ
  - [x] Stockage des données en session CI4 au clic "Continuer"
  - [x] Redirection vers étape 2
- [x] Créer `register_step2.php` — données de santé
  - [x] Champ taille en cm (obligatoire, numérique, entre 50 et 250)
  - [x] Champ poids en kg (obligatoire, numérique, entre 20 et 300)
  - [x] Champ date de naissance (facultatif)
  - [x] Calcul IMC en temps réel via AJAX à chaque saisie : `IMC = poids / (taille/100)²`
  - [x] Affichage de la valeur IMC calculée
  - [x] Barre de progression IMC colorée avec marqueur positionné dynamiquement
  - [x] Affichage de la catégorie IMC : Maigreur / Normal / Surpoids / Obésité
  - [x] Indicateur de progression : étape 2 sur 2
  - [x] Bouton retour vers étape 1
- [x] Implémenter `AuthController::register()`
  - [x] Récupérer les données session (étape 1) + données POST (étape 2)
  - [x] Vérifier que l'email n'existe pas déjà en base
  - [x] Calculer l'IMC et le stocker
  - [x] Hasher le mot de passe avec `password_hash()`
  - [x] Insérer l'utilisateur en base
  - [x] Créer la session utilisateur
  - [x] Rediriger vers la page de choix d'objectif

---

## Connexion & déconnexion ✅

- [x] Créer `login.php`
  - [x] Champ email
  - [x] Champ mot de passe
  - [x] Lien "Mot de passe oublié ?" (page statique suffisante)
  - [x] Message d'erreur générique si identifiants incorrects
  - [x] Lien vers la page d'inscription
- [x] Implémenter `AuthController::login()`
  - [x] Chercher l'utilisateur par email
  - [x] Vérifier le mot de passe avec `password_verify()`
  - [x] Créer la session CI4 avec `id`, `nom`, `is_gold`
  - [x] Rediriger vers la page d'objectif si pas encore défini, sinon vers les suggestions
- [x] Implémenter `AuthController::logout()`
  - [x] Détruire la session
  - [x] Rediriger vers la page de connexion
- [x] Créer le filtre d'authentification CI4
  - [x] Rediriger vers `/login` si la session est absente sur les routes protégées
  - [x] Appliquer le filtre à toutes les routes front-office sauf login et register

---

## Profil utilisateur ✅

- [x] Créer `profile.php`
  - [x] Section avatar : cercle avec initiales du prénom + nom
  - [x] Badge IMC actuel avec catégorie et couleur correspondante
  - [x] Badge Gold si `is_gold = 1`
  - [x] Formulaire informations personnelles : prénom, nom, email
  - [x] Formulaire données de santé : taille, poids
  - [x] Recalcul et affichage de l'IMC mis à jour en temps réel à la saisie
  - [x] Bouton "Sauvegarder les modifications"
  - [x] Message de confirmation ou d'erreur après sauvegarde
- [x] Implémenter `ProfileController::update()` via AJAX
  - [x] Valider les champs reçus
  - [x] Mettre à jour les données en base
  - [x] Recalculer et mettre à jour l'IMC
  - [x] Mettre à jour la session avec les nouvelles valeurs
  - [x] Retourner une réponse JSON `{ success: true }` ou `{ error: "..." }`

---

## Choix de l'objectif ✅

- [x] Créer `objectif.php`
  - [x] Titre et sous-titre explicatif
  - [x] Carte "Augmenter mon poids" : icône ↑, description, sélectable
  - [x] Carte "Réduire mon poids" : icône ↓, description, sélectable
  - [x] Carte "Atteindre mon IMC idéal" : icône ◎, description, sélectable
  - [x] Mise en évidence visuelle de la carte sélectionnée (bordure colorée + coche)
  - [x] Bouton "Voir mes régimes suggérés" (désactivé si aucun objectif sélectionné)
- [x] Implémenter `ObjectifController::save()`
  - [x] Sauvegarder l'objectif choisi dans la colonne `objectif` de la table `users`
  - [x] Mettre à jour la session
  - [x] Rediriger vers `/suggestion`

---

## Suggestion de régimes ✅

- [x] Créer `suggestion.php`
  - [x] Afficher l'objectif actif de l'utilisateur en haut de page
  - [x] Afficher l'IMC actuel avec catégorie
  - [x] Lister les régimes suggérés en cartes (3 maximum)
    - [x] Nom du régime
    - [x] Durée en jours
    - [x] Delta poids attendu (ex : +4 à +7 kg)
    - [x] Barre de composition : % viande (vert) / % poisson (bleu) / % volaille (orange)
    - [x] Affichage des pourcentages textuels sous la barre
    - [x] Prix de base
    - [x] Prix avec remise 15% si `is_gold = 1` (barré + nouveau prix en vert)
    - [x] Badge "Recommandé" sur le premier résultat
    - [x] Bouton "Choisir" par régime
  - [x] Section activités sportives recommandées
    - [x] Liste des activités associées au régime sélectionné
    - [x] Fréquence par semaine et durée par séance
  - [x] Bouton "Exporter en PDF"
- [x] Implémenter `SuggestionController::index()`
  - [x] Récupérer l'objectif et l'IMC de l'utilisateur connecté
  - [x] Filtrer les régimes selon l'objectif :
    - [x] Augmenter poids → `delta_poids_max > 0`
    - [x] Réduire poids → `delta_poids_min < 0`
    - [x] IMC idéal → régimes équilibrés (delta proche de 0)
  - [x] Trier par pertinence (delta le plus proche de l'objectif en premier)
  - [x] Appliquer la remise 15% si `is_gold = 1`
  - [x] Charger les activités associées via la table pivot `regime_activite`
- [x] Implémenter l'export PDF `ExportController::pdf()`
  - [x] Intégrer TCPDF ou DomPDF via Composer
  - [x] Générer un document avec : nom utilisateur, IMC, objectif, régime choisi, composition, prix, activités
  - [x] Forcer le téléchargement avec le bon header HTTP

---

## Portefeuille ✅

- [x] Créer `wallet.php`
  - [x] Card solde disponible : montant en grand, nom utilisateur
  - [x] Formulaire de recharge : champ code (style monospace), bouton Valider
  - [x] Message de succès (montant crédité) ou d'erreur (code invalide / déjà utilisé)
  - [x] Historique des transactions : date, libellé, montant (+ vert ou - rouge)
- [x] Implémenter `WalletController::validate()` via AJAX
  - [x] Vérifier que le code existe dans la table `codes`
  - [x] Vérifier que le statut est "actif"
  - [x] Créditer le montant dans `users.wallet`
  - [x] Marquer le code comme "utilisé" avec `user_id` et `used_at`
  - [x] Retourner une réponse JSON avec le nouveau solde
- [x] Implémenter la déduction lors d'un achat de régime
  - [x] Vérifier que `users.wallet >= prix_regime`
  - [x] Déduire le montant et enregistrer la transaction

---

## Option Gold ✅

- [x] Créer `gold.php`
  - [x] Icône couronne + titre "Option Gold NutriPlan"
  - [x] Liste des avantages : -15% sur les régimes, accès premium, support prioritaire, accès à vie
  - [x] Affichage du prix unique (récupéré depuis la table `parametres`)
  - [x] Affichage du solde actuel de l'utilisateur
  - [x] Message d'avertissement si solde insuffisant
  - [x] Bouton "Activer l'option Gold" (désactivé si solde insuffisant)
  - [x] Confirmation visuelle si déjà Gold (badge + message)
- [x] Implémenter `GoldController::activate()`
  - [x] Vérifier que `is_gold = 0` (ne pas débiter deux fois)
  - [x] Vérifier que le solde est suffisant
  - [x] Déduire le prix Gold du wallet
  - [x] Passer `is_gold = 1` en base
  - [x] Mettre à jour la session
  - [x] Rediriger avec message de succès
- [x] Appliquer la remise automatiquement dans `SuggestionController`
  - [x] Si `is_gold = 1` : `prix_affiche = prix_base * 0.85`
  - [x] Afficher l'ancien prix barré et le nouveau prix

---

## Back-office — Authentification ✅

- [x] Créer `bo/login.php`
  - [x] Design sobre sur fond sombre (#162130)
  - [x] Champs identifiant + mot de passe
  - [x] Message d'erreur si identifiants incorrects
- [x] Implémenter `AdminController::login()`
  - [x] Vérifier les identifiants dans une table `admins` ou via un champ `role` dans `users`
  - [x] Créer une session admin séparée (`admin_id`, `admin_nom`)
  - [x] Rediriger vers le dashboard
- [x] Créer le filtre admin CI4
  - [x] Protéger toutes les routes `/bo/*`
  - [x] Rediriger vers `/bo/login` si session admin absente

---

## Back-office — Tableau de bord ✅

- [x] Créer `bo/dashboard.php`
  - [x] Cards KPI en haut de page
    - [x] Nombre total d'utilisateurs inscrits
    - [x] Nombre de régimes vendus (total transactions)
    - [x] Nombre de codes validés vs émis
    - [x] Nombre de membres Gold
  - [x] Graphe Chart.js — courbe des inscriptions par mois (6 derniers mois)
    - [x] Requête SQL : `COUNT(id) GROUP BY MONTH(created_at)`
    - [x] Couleur : vert primaire `#1A7A48`
  - [x] Graphe Chart.js — camembert répartition des objectifs
    - [x] 3 segments : Augmenter / Réduire / IMC idéal
    - [x] Couleurs : vert / rouge / orange
  - [x] Tableau des régimes les plus vendus (top 5)
- [x] Implémenter `DashboardController::index()`
  - [x] Requête SQL pour chaque KPI
  - [x] Requête pour les données des graphes (retournées en JSON pour Chart.js)
  - [x] Passer toutes les données à la vue

---

## Back-office — CRUD Régimes ✅

- [x] Créer `bo/regimes.php` — liste
  - [x] Tableau paginé : nom, composition (mini-barres colorées), durée, prix, delta poids
  - [x] Bouton éditer (icône crayon) et supprimer (icône corbeille, confirmation)
  - [x] Bouton "Nouveau régime" en haut à droite
  - [x] Filtre par objectif (augmenter / réduire / équilibre)
- [x] Créer `bo/regimes_form.php` — formulaire création/édition
  - [x] Champ nom du régime
  - [x] Champs % viande, % poisson, % volaille (validation : somme doit = 100%)
  - [x] Affichage visuel de la barre de composition en temps réel
  - [x] Champ durée en jours
  - [x] Champ prix en Ariary
  - [x] Champs delta poids minimum et maximum
  - [x] Sélection des activités associées (checkboxes)
  - [x] Boutons Sauvegarder / Annuler
- [x] Implémenter `RegimeController`
  - [x] `index()` : lister + paginer
  - [x] `create()` : afficher le formulaire vide
  - [x] `store()` : valider + insérer en base + insérer les pivots activités
  - [x] `edit($id)` : afficher le formulaire prérempli
  - [x] `update($id)` : valider + mettre à jour + mettre à jour les pivots
  - [x] `delete($id)` : supprimer le régime et ses pivots

---

## Back-office — CRUD Activités sportives ✅

- [x] Créer `bo/activites.php` — liste
  - [x] Tableau paginé : nom, niveau, fréquence, durée par séance
  - [x] Boutons éditer et supprimer par ligne
  - [x] Bouton "Nouvelle activité"
- [x] Créer `bo/activites_form.php` — formulaire
  - [x] Champ nom
  - [x] Champ description
  - [x] Champ fréquence par semaine (nombre)
  - [x] Champ durée par séance en minutes
  - [x] Sélecteur niveau : Débutant / Intermédiaire / Avancé
- [x] Implémenter `ActiviteController`
  - [x] `index()` : lister + paginer
  - [x] `create()` / `store()` : création
  - [x] `edit($id)` / `update($id)` : modification
  - [x] `delete($id)` : suppression (vérifier qu'aucun régime actif ne la référence)

---

## Back-office — CRUD Codes portefeuille ✅

- [x] Créer `bo/codes.php` — liste
  - [x] Tableau paginé : code (monospace), montant, statut (badge vert/rouge), utilisé par, date
  - [x] Filtre par statut : Tous / Actifs / Utilisés
  - [x] Bouton "Invalider" sur les codes actifs
  - [x] Bouton "Générer des codes"
- [x] Créer `bo/codes_form.php` — formulaire de génération
  - [x] Champ montant en Ariary
  - [x] Champ quantité à générer (1 à 50)
  - [x] Bouton Générer
  - [x] Affichage de la liste des codes générés après soumission (copiables)
- [x] Implémenter `CodeController`
  - [x] `index()` : lister avec filtre statut
  - [x] `generate()` : générer N codes aléatoires uniques format `NP-XXXX-XXXX`
  - [x] `invalidate($id)` : passer le statut à "invalide"
  - [x] `delete($id)` : supprimer uniquement si statut "invalide"

---

## Back-office — CRUD Paramètres ✅

- [x] Créer `bo/parametres.php`
  - [x] Tableau des paramètres : clé, valeur actuelle (éditable inline), description
  - [x] Paramètres à gérer :
    - [x] `prix_gold` : prix de l'option Gold en Ariary
    - [x] `imc_maigreur` : seuil inférieur (défaut : 18.5)
    - [x] `imc_surpoids` : seuil supérieur normal (défaut : 25)
    - [x] `imc_obesite` : seuil obésité (défaut : 30)
    - [x] `remise_gold_pct` : pourcentage de remise Gold (défaut : 15)
  - [x] Bouton Sauvegarder par ligne
  - [x] Message de confirmation après modification
- [x] Implémenter `ParamController`
  - [x] `index()` : lister tous les paramètres
  - [x] `update($cle)` : mettre à jour la valeur d'un paramètre via AJAX

---

## Tests & livraison

- [ ] Tests manuels front-office
  - [ ] Parcours complet : inscription étape 1 → étape 2 → objectif → suggestion → choisir régime
  - [ ] Tester l'export PDF avec un régime sélectionné
  - [ ] Tester la recharge du portefeuille avec un code valide et un code déjà utilisé
  - [ ] Tester l'achat de l'option Gold avec solde suffisant et insuffisant
  - [ ] Vérifier que la remise 15% s'applique correctement après activation Gold
  - [ ] Tester la mise à jour du profil et le recalcul de l'IMC
  - [ ] Vérifier les redirections si non connecté
- [ ] Tests manuels back-office
  - [ ] Tester le login admin (bon / mauvais identifiant)
  - [ ] Tester le CRUD complet des régimes (créer, modifier, supprimer)
  - [ ] Tester la validation de la somme % viande + poisson + volaille = 100%
  - [ ] Tester le CRUD des activités
  - [ ] Tester la génération de codes et leur invalidation
  - [ ] Vérifier les statistiques du dashboard (cohérence avec les données)
  - [ ] Tester la modification des paramètres et leur prise en compte
- [ ] Livraison
  - [ ] Rédiger le `README.md` : prérequis, installation, configuration `.env`, identifiants de test
  - [ ] Vérifier que tous les commits sont bien poussés sur GitLab/GitHub tout au long du projet
  - [ ] Vérifier que la branche `main` contient la version finale après merge
  - [ ] Remplir le formulaire Google Forms de livraison avec le lien du dépôt et le script SQL
  - [ ] Vérifier la liste Google Sheets de suivi des tâches à jour
