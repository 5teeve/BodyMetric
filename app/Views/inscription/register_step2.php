<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>BodyMetric - Inscription 2</title>
    <link rel="stylesheet" href="<?= base_url('css/register_step2.css') ?>">
    <script src="<?= base_url('js/register_step2.js') ?>"></script>
</head>
<body>
    <div class="container">
        <div class="stepper">
            <div class="step completed">✓</div>
            <div class="step active">2</div>
        </div>
        <div class="card">
            <div class="header">
                <h1>Santé</h1>
                <p>Étape 2 : Vos mesures et mot de passe</p>
            </div>

            <!-- Récapitulatif utilisateur -->
            <div class="info-summary">
                <p>
                    <span class="label">Nom complet</span>
                    <strong><?= isset($registration['nom']) ? esc($registration['nom'] . ' ' . $registration['prenom']) : '-' ?></strong>
                </p>
                <p>
                    <span class="label">Email</span>
                    <span class="value"><?= isset($registration['email']) ? esc($registration['email']) : '-' ?></span>
                </p>
            </div>

            <form id="form2" action="<?= base_url('inscription/step2') ?>" method="POST">
                <?= csrf_field() ?>
                
                <?php $errors = session()->get('validation_errors'); ?>

                <div class="form-group">
                    <label>Taille (cm)</label>
                    <input type="number" name="taille" class="input-field <?= isset($errors['taille']) ? 'error' : '' ?>" placeholder="175" step="0.1" min="50" max="250" inputmode="decimal" pattern="[0-9]*" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode === 44 || event.charCode === 46" value="<?= old('taille') ?>" autocomplete="off" required>
                    <?php if (isset($errors['taille'])): ?>
                        <div class="field-error"><?= $errors['taille'] ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>Poids (kg)</label>
                    <input type="number" name="poids" class="input-field <?= isset($errors['poids']) ? 'error' : '' ?>" placeholder="70" step="0.1" min="20" max="300" inputmode="decimal" pattern="[0-9]*" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode === 44 || event.charCode === 46" value="<?= old('poids') ?>" autocomplete="off" required>
                    <?php if (isset($errors['poids'])): ?>
                        <div class="field-error"><?= $errors['poids'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Mot de passe</label>
                    <div class="password-wrapper">
                        <input type="password" name="mdp" class="input-field <?= isset($errors['mdp']) ? 'error' : '' ?>" placeholder="8 caractères minimum" autocomplete="new-password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword(this)" title="Afficher/Masquer le mot de passe">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="eye-icon">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    <small>Minimum 8 caractères, 1 majuscule, 1 minuscule, 1 chiffre</small>
                    <?php if (isset($errors['mdp'])): ?>
                        <div class="field-error"><?= $errors['mdp'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- BMI Box Premium -->
                <div class="bmi-box">
                    <div class="bmi-label">IMC Estimé</div>
                    <div class="bmi-value" id="bmiValue">--.-</div>
                    <div class="bmi-category" id="bmiCategory" style="display: none;"></div>
                    <div class="gauge">
                        <div class="gauge-segments">
                            <span>Maigre</span>
                            <span>Normal</span>
                            <span>Surpoids</span>
                            <span>Obèse</span>
                        </div>
                            <div class="indicator" id="bmiIndicator" style="left: 0%">
                            <div class="indicator-dot" id="bmiIndicatorDot"></div>
                        </div>
                    </div>
                </div>

                <div class="flex-btns">
                    <a href="<?= base_url('inscription/step1') ?>" class="btn btn-outline" style="text-decoration:none; text-align:center;">← Retour</a>
                    <button type="submit" class="btn">Terminer l'inscription</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
