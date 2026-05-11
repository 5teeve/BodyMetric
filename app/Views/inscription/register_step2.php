<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>BodyMetric - Inscription 2</title>
    <link rel="stylesheet" href="<?= base_url('css/header.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/register_step2.css') ?>">
    <script src="<?= base_url('js/register_step2.js') ?>"></script>
</head>
<body>
    
    <div class="container">
        <div class="stepper">
            <div class="step active">✓</div>
            <div class="step active">2</div>
        </div>
        <div class="card">
            <div class="header">
                <h1>Santé</h1>
                <p>Étape 2 : Vos mesures</p>
            </div>

            <?php if (session()->has('validation_errors')): ?>
                <div class="alert alert-danger">
                    <?php foreach (session()->get('validation_errors') as $error): ?>
                        <p><?= esc($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (session()->has('error')): ?>
                <div class="alert alert-danger">
                    <?= esc(session('error')) ?>
                </div>
            <?php endif; ?>

            <!-- Récapitulatif étape 1 -->
            <div class="info-summary">
                <p><strong>Nom:</strong> <?= isset($registration['nom']) ? esc($registration['nom']) : '' ?></p>
                <p><strong>Prénom:</strong> <?= isset($registration['prenom']) ? esc($registration['prenom']) : '' ?></p>
                <p><strong>Email:</strong> <?= isset($registration['email']) ? esc($registration['email']) : '' ?></p>
            </div>

            <form id="form2" action="<?= base_url('inscription/step2') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Taille (cm)</label>
                    <input type="number" name="taille" class="input-field" placeholder="175" step="0.1" min="50" max="250" inputmode="decimal" pattern="[0-9]*" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode === 44 || event.charCode === 46" value="<?= old('taille') ?>" required>
                </div>
                <div class="form-group">
                    <label>Poids (kg)</label>
                    <input type="number" name="poids" class="input-field" placeholder="70" step="0.1" min="20" max="300" inputmode="decimal" pattern="[0-9]*" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode === 44 || event.charCode === 46" value="<?= old('poids') ?>" required>
                </div>

                <div class="bmi-box">
                    <div class="bmi-label">IMC Estimé</div>
                    <div class="bmi-value">--.-</div>
                    <div class="gauge">
                        <div class="indicator" style="left: 0%"></div>
                    </div>
                </div>

                <div class="flex-btns">
                    <a href="<?= base_url('inscription/step1') ?>" class="btn btn-outline" style="text-decoration:none; text-align:center;">Retour</a>
                    <button type="submit" class="btn">Terminer</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
