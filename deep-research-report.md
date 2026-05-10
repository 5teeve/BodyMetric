# Projet BodyMetric – Plan d’implémentation détaillé

## Analyse des fonctionnalités : UX/UI et performance  
- **Expérience utilisateur (UX/UI)** : priorisez une interface claire et cohérente. Utilisez des grilles visuelles structurées, une hiérarchie d’informations nette, des couleurs et contrastes conformes aux normes d’accessibilité【4†L134-L139】. Chaque élément cliquable doit être évident, chaque page agréable à consulter【4†L134-L139】. Par exemple, les formulaires doivent ne comporter que l’essentiel et inclure un indicateur de progression pour rassurer l’utilisateur【26†L42-L49】【11†L44-L50】. Affichez immédiatement la catégorie d’IMC (maigreur, normal, surpoids, obésité) avec une barre colorée intuitive. Fournissez des retours visuels (feedback) en temps réel : validation instantanée des champs obligatoires, messages d’erreur clairs, etc., afin de réduire les erreurs【26†L42-L49】. Ne laissez aucun champ flou ou facultatif sans mention : tous les champs requis doivent être marqués et validés aussi bien côté client qu’un serveur【26†L42-L49】【15†L139-L143】.  
- **Performance** : optimisez dès la conception. Activez la compression Gzip dans CodeIgniter (`$config['compress_output']=TRUE`) pour réduire la taille des réponses HTTP【1†L60-L68】. Mettez l’application en mode production (`ENVIRONMENT='production'`) pour désactiver les warnings et journaux trop verbeux【1†L75-L82】. Combinez et minifiez vos CSS/JS (par exemple via Grunt, Webpack ou outils CI) afin de diminuer les requêtes et le poids des fichiers【18†L264-L270】. Optimisez les images (pas de redimensionnement en CSS, formats compressés) pour accélérer le chargement【18†L219-L224】. Servez les ressources statiques (images, CSS, JS) avec une politique de cache HTTP (headers Expires/Cache-Control)【18†L238-L246】. Indexez les colonnes fréquemment utilisées en filtre ou jointure (p. ex. index sur les clés étrangères) pour accélérer les requêtes SQL【9†L153-L161】. Côté serveur, mettez en cache les pages ou les requêtes lourdes avec CI (`$this->output->cache()`)【1†L128-L136】. En somme, tout doit être réactif : interface fluide, chargement rapide des pages et chargement asynchrone des données non critiques via AJAX. Investir dans un bon UX se traduit par de meilleurs KPIs : on observe jusqu’à +200% sur les indicateurs clés quand on suit ces bonnes pratiques【4†L170-L174】.

## Base de données : conception détaillée  
- **Tables et colonnes** : définissez cinq tables principales. Par exemple :
  - `users` : `id` (INT auto-increment, PK), `nom`, `prenom`, `email` (VARCHAR, **unique**, NOT NULL), `password` (VARCHAR, NOT NULL, stockant le haché), `genre` (ENUM ou TINYINT), `taille` (INT cm, NOT NULL), `poids` (INT kg, NOT NULL), `objectif` (ENUM('gain','perte','ideal') DEFAULT 'ideal'), `is_gold` (BOOLEAN DEFAULT 0), `created_at` (TIMESTAMP DEFAULT CURRENT_TIMESTAMP). Indexez **email** et ajoutez un index sur `objectif` si vous filtrez souvent par objectif.
  - `regimes` : `id` (INT, PK), `nom` (VARCHAR), `pourcent_viande`, `pourcent_poisson`, `pourcent_volaille` (TINYINT chaque, la somme devrait faire 100% – contrainte CHECK si possible), `duree` (INT jours ou semaines), `prix` (DECIMAL), `delta_poids_min`, `delta_poids_max` (INT). Ajoutez un index sur `delta_poids_min`/`max` si vous interrogez par plage de perte/gain.
  - `activites` : `id`, `nom`, `description` (TEXT), `duree_min` (INT minutes), `calories_par_heure` (INT), `niveau` (ENUM('debutant','intermediaire','avance')). Validez que `duree_min` et `calories_par_heure` sont positifs.
  - `codes` (portefeuille) : `id`, `code` (VARCHAR UNIQUE, p.ex. 10 caractères alphanumériques), `montant` (DECIMAL), `statut` (ENUM('actif','utilise') DEFAULT 'actif'), `user_id` (INT NULL, FK → `users.id`), `date_utilisation` (DATETIME NULL). Indexez la colonne `code` et ajoutez une clé étrangère sur `user_id` (important pour les jointures【9†L153-L161】).  
  - `parametres` : `id`, `cle` (VARCHAR UNIQUE, ex. 'prix_gold', 'seuil_surpoids', etc.), `valeur` (VARCHAR ou TEXT). Cette table clé/valeur stocke la config générale (prix de l’option Gold, seuils IMC, etc.).

- **Contraintes et clés** : définissez les clés primaires (`id`) et clés étrangères (`codes.user_id` → `users.id`). Indexez les colonnes de jointure pour de meilleures performances【9†L153-L161】. Par exemple : 
  ```sql
  CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    genre ENUM('M','F','Autre'),
    taille INT NOT NULL,
    poids INT NOT NULL,
    objectif ENUM('gain','perte','ideal') DEFAULT 'ideal',
    is_gold BOOLEAN NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );
  ```  
  ```sql
  CREATE TABLE regimes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    pourcent_viande TINYINT NOT NULL,
    pourcent_poisson TINYINT NOT NULL,
    pourcent_volaille TINYINT NOT NULL,
    duree INT NOT NULL,
    prix DECIMAL(8,2) NOT NULL,
    delta_poids_min INT NOT NULL,
    delta_poids_max INT NOT NULL
  );
  CREATE TABLE activites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    duree_min INT NOT NULL,
    calories_par_heure INT NOT NULL,
    niveau ENUM('debutant','intermediaire','avance') NOT NULL DEFAULT 'debutant'
  );
  CREATE TABLE codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(16) NOT NULL UNIQUE,
    montant DECIMAL(8,2) NOT NULL,
    statut ENUM('actif','utilise') NOT NULL DEFAULT 'actif',
    user_id INT NULL,
    date_utilisation DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id)
  );
  CREATE TABLE parametres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cle VARCHAR(50) NOT NULL UNIQUE,
    valeur VARCHAR(255) NOT NULL
  );
  ```

- **Données de test** : insérez quelques lignes fictives. Par exemple 5 utilisateurs (différents sexes, poids, objectifs, dont au moins un *Gold*), 5 régimes variés (par ex. « Musculation », « Végétarien », etc.), 5 activités sportives (course, natation...), et 15 codes de portefeuille avec montants aléatoires. Marquez certains codes comme « utilisés » en renseignant `user_id` et `date_utilisation`. Cela permet de tester toutes les logiques CRUD et authentification.

## Front-office  
- **Inscription (2 étapes)** :  
  1. *Étape 1 (infos perso)* : formulaire avec *nom, prénom, email, mot de passe, genre*. Design épuré et mobile-friendly, avec barre de progression « Étape 1/2 ». Masquez les champs superflus et demandez d’abord les infos simples【26†L42-L49】. Utilisez des libellés explicites (pas seulement des placeholders) et marquez les champs obligatoires【26†L42-L49】. Côté client, validez en JavaScript (format de l’email, longueur mot de passe) avant soumission; côté serveur, refaites la validation pour éviter les données malformées ou le script malveillant (filtrage XSS【15†L92-L100】).  
  2. *Étape 2 (santé)* : formulaire avec *taille, poids* (+ optionnellement âge). Le calcul de l’IMC se fait *en temps réel* dès que l’utilisateur saisit les valeurs (via JS ou appel AJAX léger), sans recharger la page. Affichez le résultat numérique et une barre colorée indiquant la catégorie IMC (p. ex. vert si normal, rouge si obésité). Mettez à jour le résumé (IMC calculé) dynamiquement pour rendre concret l’enjeu. À la validation finale, hashez le mot de passe (PHP `password_hash()`) et insérez l’utilisateur en base. Stockez temporairement les infos de la première étape dans la session CI4 entre-temps (p. ex. `session()->set()`), puis videz la session après insertion. Ne stockez jamais le mot de passe en clair. 

- **Connexion** : page simple (email + mot de passe). Design épuré, centrée sur la page. Vérifiez l’email et utilisez `password_verify()` pour le haché. Après succès, régénérez l’ID de session pour prévenir le fixation d’ID【15†L122-L130】. Activez la protection CSRF sur le formulaire selon les recommandations CI【15†L82-L90】. Prévoyez un lien *« Mot de passe oublié »* permettant de réinitialiser le mot de passe par email – un oubli fréquent dans les specs. En cas d’échec, affichez un message non ambigu (« Identifiants incorrects »). Filtrez l’accès aux pages privées : redirigez vers la page de connexion si l’utilisateur n’est pas authentifié.

- **Profil utilisateur** : page affichant les données personnelles (+ IMC actuel calculé) et un badge « Gold » si applicable. Fournissez un formulaire d’édition (nom, prénom, email, taille, poids, etc.). La mise à jour peut se faire via AJAX pour plus de réactivité : par exemple, si l’utilisateur modifie sa taille/poids, recalculer aussitôt l’IMC en JS et mettre à jour le graphique de la barre. Côté serveur, vérifiez les nouvelles valeurs, modifiez la base, puis retournez une réponse JSON indiquant « Profil mis à jour ». Cette réactivité améliore l’expérience utilisateur. N’oubliez pas que l’email doit rester unique (renvoyer une erreur si le nouvel email est déjà pris).  

- **Sélection d’objectif** : page présentant trois grandes cartes cliquables (« Augmenter mon poids », « Réduire mon poids », « Atteindre mon IMC idéal »). Chaque carte a un design distinct (icône, titre clair, bref descriptif). Au clic, surlignez la carte (e.g. bordure, check mark) pour indiquer la sélection, et sauvegardez cet objectif en session et en base pour l’utilisateur. La sélection déclenche un redirection vers la page de suggestions. L’illustration visuelle aide à éviter la confusion sur les termes « gain » vs « prise de masse », etc. Rappelez le choix à l’utilisateur dans son profil.

- **Suggestion de régimes et activités** : affichez une liste de régimes adaptés selon l’IMC et l’objectif choisi. Design en « cartes » : pour chaque régime, montrez le nom, le prix (indiquer clairement la réduction 15% si Gold), la durée et les pourcentages aliments (barres horizontales ou infographie). Par exemple, utilisez une barre de progression pour montrer visuellement % viande/poisson/volaille. Cette visualisation facilite la compréhension rapide. En backend, sélectionnez les régimes dont le delta de poids correspond à l’objectif (ex. `delta_poids_max >= 0` pour gain de poids, `<0` pour perte). Proposez aussi une liste d’activités sportives associées à ce profil (basé sur niveau, durée, calories) – peut-être les plus efficaces pour l’objectif (ex. cardio pour perte, renforcement musculaire pour prise). Calculez par exemple le temps estimé de chaque activité pour atteindre le delta de poids du régime. 

- **Export PDF du plan** : intégrez une option « Télécharger le plan en PDF ». Utilisez une librairie PHP dédiée (TCPDF, DomPDF ou mPDF). La fiche PDF doit résumer l’IMC, l’objectif, et détailler le régime choisi avec les activités recommandées. Stylisez le PDF de manière professionnelle (logos, entêtes). Veillez à ce que le rendu soit clair (pas de débordements de texte), et générez-le sur le serveur pour garantir une cohérence du contenu. Testez avec des exemples de données longues (plusieurs activités, etc.) pour valider la mise en page.

- **Portefeuille (wallet)** : page montrant le solde actuel de crédits (ex. *€* restants), un champ de saisie pour entrer un code de recharge, et l’historique des transactions (tableau simple). Lors de la soumission du code, effectuez une requête AJAX : côté serveur, vérifiez que le code existe, est « actif » et pas déjà utilisé【15†L82-L90】. En cas de succès, créditez le solde de l’utilisateur et marquez le code comme « utilisé » avec l’ID utilisateur et la date. En cas d’échec, renvoyez un message d’erreur (« Code invalide ou déjà utilisé »). Toutes ces opérations doivent se faire en transaction pour éviter tout problème de concurrence. Mettez à jour l’affichage du solde sans recharger. Dans l’historique, mentionnez les recharges (montant, date) et les achats effectués (date, description).

- **Option Gold** : page de promotion de l’option Gold. Présentez clairement les avantages (remise 15%, accès prioritaire, etc.), le prix unique (récupéré de la table `parametres`), et un bouton d’achat. Lors de l’achat : vérifiez si le solde de l’utilisateur est suffisant, puis débitez le montant. Cochez `is_gold=1` pour cet utilisateur, et enregistrez la date d’activation au besoin. Après cet achat, appliquez automatiquement la réduction de 15% sur tous les régimes lors de leur affichage (côté serveur : `prix *= 0.85` si `is_gold`). Informez l’utilisateur en AJAX de la réussite ou des erreurs (ex. « Solde insuffisant »). N’autorisez pas de double achat Gold par le même utilisateur.

- **Navigation et design** :  
  - *Navbar responsive* : en haut, afficher logo/accueil, liens vers « Mes régimes » (suggestions passées), « Profil », etc. Si l’utilisateur est connecté, afficher son avatar (ou initiales) et un petit badge Gold s’il est abonné. Sinon, afficher « Se connecter / S’inscrire ». Évitez un menu surchargé. Utilisez un menu hamburger sur mobile.  
  - *Footer* (optionnel) : liens contact, support, CGU, réseaux sociaux.  
  - *Formulaires et feedback* : les boutons doivent être clairement libellés (ex. « Valider », « Envoyer »). Montrez des états de chargement (spinners) lors des requêtes AJAX longues pour signaler à l’utilisateur que ça tourne. Utilisez des icônes intuitives (p.ex. corbeille pour supprimer).
  - **Important** : implémentez la sécurité sur tous les formulaires (CSRF tokens, filtre XSS【15†L92-L100】) et validez toutes les données côté serveur, pas seulement côté client. Pour les sessions, utilisez la bibliothèque CI4 : stockez les sessions en base si possible, régénérez l’ID après la connexion【15†L122-L130】, et activez les cookies sécurisés sur HTTPS.

## Back-office (Administration)  
- **Authentification admin** : page de login dédiée aux administrateurs (formulaire simple sur fond sombre). Créez un mécanisme d’authentification séparé (p. ex. rôle `admin` dans `users` ou table distincte `admin`). Après vérification des identifiants, démarrez une session admin distincte. Incluez les mêmes sécurités que pour les utilisateurs (CSRF, mot de passe haché, etc.)【15†L82-L90】. Implémentez un *middleware* ou filtre CI pour protéger toutes les routes `/admin` : redirigez vers login si l’utilisateur n’a pas le statut admin. Envisagez un verrouillage après plusieurs échecs ou la 2FA pour plus de sécurité, si le projet le permet.
  
- **Tableau de bord (Dashboard)** : page d’accueil admin avec des KPI et graphiques. Affichez des « cards » avec des chiffres clés : nombre total d’utilisateurs inscrits, nombre de régimes vendus/choisis, nombre de codes activés/utilisés, nombre d’abonnés Gold. Utilisez Chart.js pour deux graphiques : 
  - Évolution mensuelle des inscriptions : ligne sur 6-12 mois.  
  - Répartition des objectifs des utilisateurs : camembert (gain/perte/normal).  
  Récupérez ces données via requêtes agrégées SQL (`COUNT`, `GROUP BY mois` ou `objectif`). Mettez ces requêtes dans un modèle CI et servez-les en JSON au front. Assurez-vous que le calcul des stats est rapide (index sur date d’inscription, etc.) et cachez-les si besoin pour éviter un recalcul à chaque chargement. Le dashboard doit rester lisible même sur mobile (graphiques redimensionnables).

- **CRUD Régimes** : 
  - *Liste* : page listant tous les régimes dans un tableau paginé. Colonnes : Nom, %Viande, %Poisson, %Volaille, Durée, Prix, Delta poids (min-max), Actions (éditer, supprimer). Ajoutez des filtres simples si nécessaire (ex. chercher par nom).  
  - *Formulaire création/édition* : champs pour nom, les trois pourcentages (contrôlez côté serveur que leur somme = 100), durée, prix, delta min/max. Validez la cohérence (pas de pourcentage négatif, somme=100, prix >0). Indiquez clairement les erreurs.  
  - *Suppression* : un bouton supprimer doit demander confirmation (« Voulez-vous vraiment supprimer ce régime ? ») pour éviter la suppression accidentelle. Bloquez les suppressions si des utilisateurs l’ont choisi (intégrité référentielle ou prévenez l’admin).  
  - Toutes les actions doivent vérifier le rôle admin (middleware) et utiliser CSRF. Mettez en place la logique CRUD complète (controllers CI) avec queries préparées ou Query Builder (pour éviter les injections SQL【15†L99-L106】).

- **CRUD Activités sportives** : similaire aux régimes. Liste paginée (Nom, Durée min, Calories/heure, Niveau). Formulaire avec nom, description, durée minimale, calories, niveau. Validez les nombres (p.ex. durée≥0). Description longue en `<textarea>`. Vous pouvez ajouter une zone WYSIWYG ou markdown pour le texte descriptif si besoin, mais pas obligatoire. Comme toujours, actions sécurisées par CSRF et contrôle d’accès.

- **CRUD Codes portefeuille** : 
  - *Génération de codes* : un formulaire où l’admin saisit un montant (à créditer) et la quantité à créer. À la soumission, générez X codes uniques alphanumériques (p. ex. 8-10 caractères) et insérez-les en base avec statut « actif ». Assurez l’unicité (index unique sur `code`). Vous pouvez implémenter un boucle en PHP avec `random_bytes` ou `bin2hex`.  
  - *Liste des codes* : affichez les codes créés avec colonnes : Code, Montant, Statut (actif/utilisé), Utilisé par (ID ou email utilisateur), Date d’utilisation, Actions (invalider). Les codes utilisés doivent être marqués et non éliminés (pour audit). 
  - *Invalidation* : bouton « Marquer utilisé » pour un code actif. Confirmez l’action (danger). Côté serveur, passez `statut='utilise'` et renseignez `user_id` et date (parfois on invalide quand un utilisateur demande remboursement d’un code, etc.).  
  - Comme d’habitude, contrôlez chaque action par session admin.

- **CRUD Paramètres** : page (formulaire) pour gérer les paramètres globaux de l’application. Par exemple : prix de l’option Gold, seuils d’IMC (maigreur, surpoids, obésité), etc. Listez les clés et leurs valeurs actuelles, permettez l’édition en ligne. Validez la cohérence (p.ex. un seuil IMC doit être numérique et que pauvreté < surpoids < obésité). Sauvegardez dans la table `parametres`. Ainsi, toute modification se fait via l’interface sans avoir à toucher le code. 

**En résumé**, ce plan doit être exhaustif et précis : n’hésitez pas à ajouter tous les contrôles nécessaires (vérification côté serveur, messages d’erreur explicites) et à prévoir des éléments manquants (par ex. réinitialisation de mot de passe, protection XSS/CSRF【15†L82-L90】, tests simples). Chaque page doit être responsive et accessible (contrastes, textes alternatifs pour les images, navigation clavier…). Les liaisons de données (formulaires, requêtes AJAX) doivent être robustes et performantes. Ce niveau de détail garantit une application efficace, performante et une expérience utilisateur professionnelle.

**Sources :** bonnes pratiques UX/UI【4†L134-L139】【26†L42-L49】, optimisation CodeIgniter (compression, cache, minification)【1†L60-L68】【18†L264-L270】, sécurité CI (CSRF, XSS, sessions)【15†L82-L90】【15†L122-L130】, bases de données (indexer clés étrangères)【9†L153-L161】, etc. Les citations ci-dessus illustrent certaines recommandations clés.