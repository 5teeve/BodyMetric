# 🚀 Guide de Démarrage Rapide - Système de Régimes

## 📌 Déploiement Immédiat

### Étape 1: Préparer la Base de Données

**Option A: Avec CodeIgniter Spark (recommandé)**
```bash
cd /home/mihaja/Documents/SI/BodyMetric
php spark migrate
```

**Option B: Script SQL direct**
```bash
cd /home/mihaja/Documents/SI/BodyMetric
mysql -u root -proot body_metric_db < database/006_regimes_achetes.sql
```

**Option C: Vérifier la table**
```bash
mysql -u root -proot body_metric_db
> DESCRIBE regimes_achetes;
```

### Étape 2: Démarrer l'Application

```bash
cd /home/mihaja/Documents/SI/BodyMetric
php spark serve
```

L'app sera disponible à: `http://localhost:8080`

### Étape 3: Tester Manuellement

**Test 1: Voir les suggestions**
```
1. Aller à http://localhost:8080/resultats
2. Vous devriez voir des régimes avec un bouton "Choisir ce régime"
```

**Test 2: Acheter un régime**
```
1. Cliquer sur "Choisir ce régime"
2. Vérifier que vous êtes redirigé vers /mes-regimes
3. Vérifier que le régime apparaît dans la liste
```

**Test 3: Vérifier les erreurs**
```
1. Tenter de choisir le même régime deux fois
   → Erreur: "⚠️ Vous avez déjà choisi ce régime"

2. Si solde insuffisant:
   → Erreur: "❌ Solde insuffisant"

3. Si régime n'existe pas:
   → Erreur: "❌ Régime non trouvé"
```

**Test 4: Annuler un régime**
```
1. Aller à http://localhost:8080/mes-regimes
2. Cliquer sur "✕ Annuler" sur un régime
3. Confirmer l'annulation
4. Vérifier que le statut change à "Annulé"
```

## 🔍 Vérifications Rapides

### Vérifier que la migration est appliquée
```bash
mysql -u root -proot body_metric_db -e "SELECT * FROM regimes_achetes LIMIT 1;"
```

### Vérifier les logs d'erreurs
```bash
tail -f /home/mihaja/Documents/SI/BodyMetric/writable/logs/log-*.log
```

### Vérifier la structure de la table
```bash
mysql -u root -proot body_metric_db -e "SHOW CREATE TABLE regimes_achetes\G"
```

### Vérifier les données d'un utilisateur
```bash
mysql -u root -proot body_metric_db -e "
SELECT ra.*, r.nom 
FROM regimes_achetes ra
JOIN regimes r ON r.id = ra.regime_id
WHERE ra.user_id = 1
LIMIT 5;
"
```

## 📊 Données de Test

### Insérer des régimes de test
```sql
INSERT INTO regimes (nom, description, duree, prix, delta_poids, pct_viande, pct_poisson, pct_volaille)
VALUES 
('Régime Protéiné', 'Haute protéine', 30, 1000, -3, 40, 30, 30),
('Régime Équilibré', 'Équilibre alimentaire', 45, 1500, -5, 25, 25, 50);
```

### Mettre à jour le wallet d'un utilisateur
```sql
UPDATE users SET wallet = 5000 WHERE id = 1;
```

### Activer le statut Gold
```sql
UPDATE users SET is_gold = 1 WHERE id = 1;
```

## 🛠️ Commandes Utiles

### Voir les routes disponibles
```bash
cd /home/mihaja/Documents/SI/BodyMetric
php spark routes
```

### Générer une nouvelle migration
```bash
php spark make:migration CreateTableName
```

### Voir l'état de la DB
```bash
php spark db:info
```

### Voir les seeds disponibles
```bash
php spark seed:list
```

## 📝 Structure des Réponses API

### Succès de l'achat
```json
{
  "success": true,
  "message": "Régime choisi avec succès!",
  "prix_paye": 850,
  "nouveau_solde": 4150
}
```

### Erreur - Solde insuffisant
```json
{
  "error": "Solde insuffisant",
  "solde_actuel": 500,
  "prix_requis": 1000
}
```

### Erreur - Régime existant
```json
{
  "error": "Vous avez déjà ce régime"
}
```

## 🎯 Prochaines Étapes (Optionnel)

### Améliorations à court terme
1. Ajouter export PDF du plan du régime
2. Ajouter notifications utilisateur
3. Ajouter suivi de progression
4. Ajouter renouvellement automatique

### Améliorations à long terme
1. Système de panier d'achat
2. Coupons de réduction
3. Historique d'achat détaillé
4. Statistiques d'utilisation pour BO

## 🆘 Dépannage

### Erreur: "Cannot find table regimes_achetes"
```
Solution: Exécuter php spark migrate
```

### Erreur: "Access denied for user 'root'"
```
Vérifier les credentials dans .env:
- database.default.username
- database.default.password
- database.default.database
```

### Erreur: "Régime non trouvé"
```
Vérifier que:
1. Le régime existe dans la table regimes
2. Le regime_id est correct
3. Les données n'ont pas été supprimées
```

### Page blanche sur /mes-regimes
```
Vérifier les logs:
tail -f writable/logs/log-*.log
```

## 📚 Documentation Complète

- **FEATURE_REGIMES_DOCUMENTATION.md** - Documentation technique exhaustive
- **IMPLEMENTATION_SUMMARY.md** - Résumé de l'implémentation

## ✅ Checklist Final

- [ ] Base de données migrated
- [ ] Routes visibles avec `php spark routes`
- [ ] Page /resultats affiche les régimes
- [ ] Bouton "Choisir ce régime" visible et fonctionnel
- [ ] Page /mes-regimes accessible et affiche les régimes achetés
- [ ] Errors s'affichent correctement (402, 409, etc.)
- [ ] Remise Gold appliquée correctement
- [ ] Annulation fonctionne
- [ ] Logs ne montrent pas d'erreurs
- [ ] Prêt pour le déploiement

---

**Besoin d'aide?** Consulter FEATURE_REGIMES_DOCUMENTATION.md ou IMPLEMENTATION_SUMMARY.md
