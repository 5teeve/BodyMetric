<?php
$error = $error ?? null;
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>BO — Connexion</title>
    <link rel="stylesheet" href="<?= base_url('css/bo_login.css') ?>">
</head>
<body>
    <div class="bo-login">
        <div class="bo-login-card">
            <div class="bo-login-header">
                <div class="bo-login-badge">Back-Office</div>
                <h1>Connexion admin</h1>
                <p>Accédez au tableau de bord NutriPlan.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="bo-login-alert">
                    <?= esc($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= base_url('bo/login') ?>" class="bo-login-form">
                <?= csrf_field() ?>
                <div class="bo-field">
                    <label for="email">Email admin</label>
                    <input id="email" name="email" type="email" placeholder="admin@domaine.com" required value="<?= esc(old('email', '')) ?>">
                </div>
                <div class="bo-field">
                    <label for="mdp">Mot de passe</label>
                    <input id="mdp" name="mdp" type="password" placeholder="Votre mot de passe" required>
                </div>
                <button type="submit" class="bo-btn">Se connecter</button>
            </form>

            <div class="bo-login-footer">
                <a href="<?= base_url('/') ?>">Retour au site</a>
            </div>
        </div>
    </div>
</body>
</html>
