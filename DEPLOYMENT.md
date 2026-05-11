# 🚀 Guide de Déploiement — BodyMetric

## Pré-déploiement Checklist

- [x] Tous les tests locaux réussis
- [x] Base de données testée
- [x] Variables d'environnement configurées
- [x] Git repository propre
- [x] Documentation à jour

---

## 1. Déploiement en Local

### Installation Minimale

```bash
# 1. Cloner le repo
git clone https://github.com/yourname/bodymetric.git
cd bodymetric

# 2. Installer les dépendances
composer install

# 3. Copier et configurer .env
cp .env.example .env
# Éditer .env avec vos paramètres

# 4. Créer la base de données
mysql -u root -p < database/migration.sql

# 5. Générer la clé
php spark key:generate

# 6. Lancer le serveur
php spark serve
```

### Vérification

```bash
# Tester l'application
curl http://localhost:8080

# Vérifier les logs
tail -f writable/logs/log-*.log
```

---

## 2. Déploiement en Production (Linux/Apache)

### Prérequis Serveur

```bash
# Mise à jour système
sudo apt update && sudo apt upgrade -y

# Installation PHP 8.0+
sudo apt install php8.1 php8.1-cli php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-intl

# Installation MySQL
sudo apt install mysql-server

# Installation Composer
sudo apt install composer

# Installation Git
sudo apt install git
```

### Configuration Apache

```bash
# Activer mod_rewrite
sudo a2enmod rewrite

# Créer le VirtualHost
sudo nano /etc/apache2/sites-available/bodymetric.conf
```

**Contenu du VirtualHost** :

```apache
<VirtualHost *:80>
    ServerName bodymetric.com
    ServerAdmin admin@bodymetric.com
    
    DocumentRoot /var/www/bodymetric/public
    
    <Directory /var/www/bodymetric/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
        
        <IfModule mod_rewrite.c>
            RewriteEngine On
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteRule ^ index.php [QSA,L]
        </IfModule>
    </Directory>
    
    # Logs
    ErrorLog ${APACHE_LOG_DIR}/bodymetric_error.log
    CustomLog ${APACHE_LOG_DIR}/bodymetric_access.log combined
</VirtualHost>
```

### Activer et Redémarrer

```bash
# Activer le site
sudo a2ensite bodymetric

# Tester la config
sudo apache2ctl configtest

# Redémarrer Apache
sudo systemctl restart apache2
```

### Installation Application

```bash
# Créer le répertoire
sudo mkdir -p /var/www/bodymetric
cd /var/www/bodymetric

# Cloner le projet
sudo git clone https://github.com/yourname/bodymetric.git .

# Permissions
sudo chown -R www-data:www-data /var/www/bodymetric
sudo chmod -R 755 /var/www/bodymetric
sudo chmod -R 775 /var/www/bodymetric/writable

# Installer les dépendances
sudo -u www-data composer install --no-dev

# Copier .env
sudo cp .env.example .env
sudo chown www-data:www-data .env
sudo nano .env  # Éditer les paramètres
```

### Configuration Base de Données

```bash
# Créer la base de données
mysql -u root -p -e "CREATE DATABASE bodymetric CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p -e "GRANT ALL PRIVILEGES ON bodymetric.* TO 'bodymetric_user'@'localhost' IDENTIFIED BY 'SecurePassword123!'; FLUSH PRIVILEGES;"

# Importer la migration
mysql -u bodymetric_user -p bodymetric < database/migration.sql
```

### Générer la clé

```bash
sudo -u www-data php spark key:generate
```

### SSL/HTTPS avec Let's Encrypt

```bash
# Installer Certbot
sudo apt install certbot python3-certbot-apache

# Générer le certificat
sudo certbot --apache -d bodymetric.com -d www.bodymetric.com

# Renouvellement automatique
sudo systemctl enable certbot.timer
sudo systemctl start certbot.timer
```

---

## 3. Déploiement en Heroku (Cloud Simple)

### Prérequis

```bash
# Installer Heroku CLI
curl https://cli-assets.heroku.com/install.sh | sh

# Se connecter à Heroku
heroku login
```

### Préparation du Projet

```bash
# Créer le Procfile
echo "web: vendor/bin/heroku-php-apache2 public/" > Procfile

# Créer le .htaccess
echo "php_value upload_max_filesize 10M" >> public/.htaccess

# Configurer la base de données
heroku addons:create cleardb:ignite -a bodymetric
```

### Déploiement

```bash
# Créer l'app sur Heroku
heroku create bodymetric

# Ajouter le remote
git remote add heroku https://git.heroku.com/bodymetric.git

# Configurer les variables d'environnement
heroku config:set APP_ENVIRONMENT=production
heroku config:set DATABASE_URL="mysql://..."

# Déployer
git push heroku main
```

---

## 4. Déploiement en Docker

### Dockerfile

```dockerfile
FROM php:8.1-apache

# Extensions PHP
RUN docker-php-ext-install pdo pdo_mysql mbstring intl

# Mod Rewrite
RUN a2enmod rewrite

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Working directory
WORKDIR /var/www/html

# Copier le projet
COPY . .

# Permissions
RUN chown -R www-data:www-data /var/www/html

# Installer les dépendances
RUN composer install --no-dev

# Exposer le port
EXPOSE 80

# CMD
CMD ["apache2-foreground"]
```

### Docker Compose

```yaml
version: '3.8'

services:
  web:
    build: .
    ports:
      - "8000:80"
    environment:
      - DATABASE_HOST=db
      - DATABASE_NAME=bodymetric
      - DATABASE_USER=user
      - DATABASE_PASSWORD=password
    depends_on:
      - db

  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: bodymetric
      MYSQL_USER: user
      MYSQL_PASSWORD: password
    ports:
      - "3306:3306"
    volumes:
      - db_data:/var/lib/mysql
      - ./database/migration.sql:/docker-entrypoint-initdb.d/init.sql

volumes:
  db_data:
```

### Lancer avec Docker

```bash
# Construire et lancer
docker-compose up -d

# Vérifier les logs
docker-compose logs -f

# Accéder à l'app
http://localhost:8000
```

---

## 5. Monitoring et Maintenance

### Logs

```bash
# Logs Apache
sudo tail -f /var/log/apache2/bodymetric_error.log
sudo tail -f /var/log/apache2/bodymetric_access.log

# Logs Application
tail -f /var/www/bodymetric/writable/logs/log-*.log
```

### Backups

```bash
# Backup base de données (quotidien)
0 2 * * * mysqldump -u bodymetric_user -p'SecurePassword123!' bodymetric > /backups/bodymetric-$(date +\%Y\%m\%d).sql

# Backup fichiers
0 3 * * * tar -czf /backups/bodymetric-$(date +\%Y\%m\%d).tar.gz /var/www/bodymetric
```

### Performance

```bash
# Monitoring CPU/RAM
htop

# Vérifier l'espace disque
df -h

# Vérifier les processus PHP
ps aux | grep php
```

### Mises à Jour

```bash
# Mettre à jour Composer
composer update

# Mettre à jour le système
sudo apt update && sudo apt upgrade

# Vérifier les mises à jour de sécurité
sudo apt list --upgradable | grep security
```

---

## 6. Résolution des Problèmes Courants

### Erreur 404

```bash
# Vérifier mod_rewrite est activé
sudo a2enmod rewrite
sudo apache2ctl restart

# Vérifier .htaccess
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ index.php [QSA,L]
</IfModule>
```

### Erreur de Permission

```bash
# Permissions writable
sudo chmod -R 775 /var/www/bodymetric/writable
sudo chown -R www-data:www-data /var/www/bodymetric/writable
```

### Erreur Base de Données

```bash
# Vérifier la connexion
mysql -h localhost -u bodymetric_user -p -e "SELECT 1;"

# Vérifier les permissions MySQL
GRANT ALL PRIVILEGES ON bodymetric.* TO 'bodymetric_user'@'localhost';
FLUSH PRIVILEGES;
```

### Erreur de Mémoire PHP

```bash
# Augmenter memory_limit dans php.ini
memory_limit = 256M

# Ou dans .htaccess
php_value memory_limit 256M
```

---

## 7. Post-Déploiement Checklist

- [ ] Application accessible via le domaine
- [ ] HTTPS activé et valide
- [ ] Base de données connectée
- [ ] Identifiants de test valides
- [ ] Emails de notification configurés
- [ ] Backups automatiques en place
- [ ] Monitoring actif
- [ ] Logs révisés (pas d'erreurs)
- [ ] Performance acceptable (< 2s par page)
- [ ] Responsive design testé
- [ ] Tests manuels sur production

---

## 8. Certificat SSL

### Auto-renouvellement Let's Encrypt

```bash
# Vérifier le statut
sudo certbot certificates

# Test de renouvellement
sudo certbot renew --dry-run

# Forcer le renouvellement
sudo certbot renew --force-renewal
```

---

## 9. Escalabilité Future

### Si croissance importante

```bash
# Load Balancer (Nginx)
sudo apt install nginx

# Caching Redis
sudo apt install redis-server

# CDN
Intégrer Cloudflare

# Base de données répliquée
MySQL Replication ou Cluster
```

---

## 📞 Support Déploiement

**Problème** → **Solution**

- Port 80 occupé → `sudo lsof -i :80` et fermer le processus
- Permission denied → Vérifier les chmod/chown
- Base de données introuvable → Vérifier DATABASE_URL dans .env
- Dépendances manquantes → `composer install --no-dev`

---

## ✅ Déploiement Réussi

Une fois ces étapes complétées, votre application BodyMetric est en production et accessible 24/7.

**Statut** : ✅ DÉPLOIEMENT AUTORISÉ

---

**Dernière mise à jour** : 11 mai 2026
