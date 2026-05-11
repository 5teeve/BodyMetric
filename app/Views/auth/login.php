<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BodyMetric - Connexion</title>
    <link rel="stylesheet" href="<?= base_url('css/login.css') ?>">
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>Connexion</h1>
                <p>Accédez à votre portefeuille et vos recommandations</p>
            </div>

            <?php if (session()->has('error')): ?>
                <div class="alert alert-danger"><?= esc(session('error')) ?></div>
            <?php endif; ?>

            <?php if (session()->has('success')): ?>
                <div class="alert alert-success"><?= esc(session('success')) ?></div>
            <?php endif; ?>

            <?php if (session()->has('validation_errors')): ?>
                <div class="alert alert-danger">
                    <?php foreach (session('validation_errors') as $error): ?>
                        <p><?= esc($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= site_url('connexion') ?>" autocomplete="on">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" class="input-field" value="<?= old('email') ?>" required>
                </div>

                <div class="form-group">
                    <label for="mdp">Mot de passe</label>
                    <input id="mdp" type="password" name="mdp" class="input-field" required>
                </div>

                <button type="submit" class="btn">Se connecter</button>
            </form>

            <div class="helper-links">
                <a href="<?= site_url('inscription/step1') ?>">Créer un compte</a>
            </div>
        </div>
    </div>
</body>
</html>
