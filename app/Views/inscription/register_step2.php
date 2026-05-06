<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>BodyMetric - Inscription 2</title>
    <link rel="stylesheet" href="<?= base_url('css/register_step2.css') ?>">
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
            <form action="<?= base_url('inscription/step2') ?>" method="POST">
                <div class="form-group">
                    <label>Taille (cm)</label>
                    <input type="number" name="taille" class="input-field" placeholder="175" required>
                </div>
                <div class="form-group">
                    <label>Poids (kg)</label>
                    <input type="number" name="poids" class="input-field" placeholder="70" required>
                </div>

                <!-- Squelette visuel IMC (sans calcul) -->
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
