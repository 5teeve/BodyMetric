<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>BodyMetric - Connexion</title>
    <link rel="stylesheet" href="<?= base_url('css/login.css') ?>">
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>Connexion</h1>
                <p>Accédez à votre compte</p>
            </div>

            <?php if (session()->has('error')): ?>
                <div class="alert alert-danger">
                    <p><?= esc(session('error')) ?></p>
                </div>
            <?php endif; ?>

            <?php if (session()->has('validation_errors')): ?>
                <div class="alert alert-danger">
                    <?php foreach (session()->get('validation_errors') as $error): ?>
                        <p><?= esc($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('connexion') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="input-field" value="<?= old('email') ?>" required>
                </div>

                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="mdp" class="input-field" required>
                </div>

                <button type="submit" class="btn">Se connecter</button>
            </form>
        </div>
    </div>
</body>
</html>
