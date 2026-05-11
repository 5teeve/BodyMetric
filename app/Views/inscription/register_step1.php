<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>BodyMetric - Inscription 1</title>
    <link rel="stylesheet" href="<?= base_url('css/global.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/header.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/register_step1.css') ?>">
</head>
<body>
    <div class="container">
        <div class="stepper">
            <div class="step active">1</div>
            <div class="step">2</div>
        </div>
        <div class="card">
            <div class="header">
                <h1>Inscription</h1>
                <p>Étape 1 : Informations personnelles</p>
            </div>
            <form id="form1" action="<?= base_url('inscription/step1') ?>" method="POST">
                <?= csrf_field() ?>
                <?php if (session()->has('validation_errors')): ?>
                    <div class="alert alert-danger">
                        <?php foreach (session()->get('validation_errors') as $error): ?>
                            <p><?= esc($error) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="nom" class="input-field" value="<?= old('nom') ?>" required>
                </div>
                <div class="form-group">
                    <label>Prénom</label>
                    <input type="text" name="prenom" class="input-field" value="<?= old('prenom') ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="input-field" value="<?= old('email') ?>" required>
                </div>
                <div class="form-group">
                    <label>Genre</label>
                    <div class="radio-group">
                        <label class="radio-item"><input type="radio" name="genre" value="M" <?= old('genre') === 'M' ? 'checked' : '' ?> required> Homme</label>
                        <label class="radio-item"><input type="radio" name="genre" value="F" <?= old('genre') === 'F' ? 'checked' : '' ?>> Femme</label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Mot de passe</label>
                    <div class="password-wrapper">
                        <input type="password" name="mdp" class="input-field" placeholder="Au moins 8 caractères" required>
                        <button type="button" class="toggle-password" onclick="togglePassword(this)" title="Afficher/Masquer le mot de passe">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="eye-icon">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    <small>Doit contenir une majuscule, une minuscule et un chiffre</small>
                </div>
                <button type="submit" class="btn">Suivant</button>
            </form>
        </div>
    </div>
    <script src="<?= base_url('js/register_step1.js') ?>"></script>
</body>
</html>
