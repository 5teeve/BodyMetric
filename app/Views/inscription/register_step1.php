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
            <form id="form1" action="<?= base_url('inscription/step1') ?>" method="POST">
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
                        <label class="radio-item"><input type="radio" name="genre" value="H" <?= old('genre') === 'H' ? 'checked' : '' ?> required> Homme</label>
                        <label class="radio-item"><input type="radio" name="genre" value="F" <?= old('genre') === 'F' ? 'checked' : '' ?>> Femme</label>
                    </div>
                </div>
                <button type="submit" class="btn">Suivant</button>
            </form>
        </div>
    </div>
    <script src="<?= base_url('js/register_step1.js') ?>"></script>
</body>
</html>
