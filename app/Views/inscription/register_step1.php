<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>BodyMetric - Inscription 1</title>
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
            <?php if (session()->has('error')): ?>
                <div class="alert alert-danger">
                    <?= session('error') ?>
                </div>
            <?php endif; ?>

            <form id="form1" action="<?= base_url('inscription/step1') ?>" method="POST">
                <?= csrf_field() ?>

                <?php $errors = session()->get('validation_errors'); ?>

                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="nom" class="input-field <?= isset($errors['nom']) ? 'error' : '' ?>" placeholder="Ex: Dupont" value="<?= old('nom', $registration['nom'] ?? '') ?>" required>
                    <?php if (isset($errors['nom'])): ?>
                        <div class="field-error"><?= $errors['nom'] ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>Prénom</label>
                    <input type="text" name="prenom" class="input-field <?= isset($errors['prenom']) ? 'error' : '' ?>" placeholder="Ex: Jean" value="<?= old('prenom', $registration['prenom'] ?? '') ?>" required>
                    <?php if (isset($errors['prenom'])): ?>
                        <div class="field-error"><?= $errors['prenom'] ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="input-field <?= isset($errors['email']) ? 'error' : '' ?>" placeholder="jean.dupont@example.com" value="<?= old('email', $registration['email'] ?? '') ?>" required>
                    <?php if (isset($errors['email'])): ?>
                        <div class="field-error"><?= $errors['email'] ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>Genre</label>
                    <div class="radio-group">
                        <?php $genre = old('genre', $registration['genre'] ?? ''); ?>
                        <label class="radio-item"><input type="radio" name="genre" value="H" <?= $genre === 'H' ? 'checked' : '' ?> required> Homme</label>
                        <label class="radio-item"><input type="radio" name="genre" value="F" <?= $genre === 'F' ? 'checked' : '' ?>> Femme</label>
                    </div>
                    <?php if (isset($errors['genre'])): ?>
                        <div class="field-error"><?= $errors['genre'] ?></div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn">Suivant</button>
            </form>
        </div>
    </div>
    <script src="<?= base_url('js/register_step1.js') ?>"></script>
</body>
</html>
