<?php
$errors = $errors ?? [];
$success = $success ?? null;
$oldMontant = $oldMontant ?? '';
$oldQuantite = $oldQuantite ?? '1';
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>BO — Génération de codes</title>
    <link rel="stylesheet" href="<?= base_url('css/wallet.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/bo_codes.css') ?>">
</head>
<body data-wallet-page="true">
    <main class="wallet-page">
        <section class="hero-panel bo-form-hero">
            <div class="hero-copy">
                <p class="eyebrow">BO — Codes</p>
                <h1>Générer des codes de recharge.</h1>
                <p class="hero-text">
                    Créez plusieurs codes en une seule opération avec un montant fixe par code.
                </p>
            </div>

            <div class="hero-stat">
                <span class="stat-label">Table cible</span>
                <strong>codes</strong>
                <small>Montant, quantité et statut actif.</small>
            </div>
        </section>

        <section class="wallet-card form-card">
            <div class="card-heading compact">
                <div>
                    <p class="card-kicker">Nouveau lot</p>
                    <h2>Formulaire de génération</h2>
                </div>
            </div>

            <?php if (! empty($success)): ?>
                <div class="form-alert form-alert--success"><?= esc($success) ?></div>
            <?php endif; ?>

            <?php if (! empty($errors)): ?>
                <div class="form-alert form-alert--error">
                    <strong>Veuillez corriger les champs suivants :</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form class="codes-form" method="post" action="<?= base_url('bo/codes/form') ?>" autocomplete="off">
                <?= csrf_field() ?>

                <div class="form-grid">
                    <div class="field-shell">
                        <label for="montant">Montant par code</label>
                        <input
                            id="montant"
                            name="montant"
                            type="number"
                            step="0.01"
                            min="0.01"
                            placeholder="Ex. 25.00"
                            value="<?= esc($oldMontant) ?>"
                            required
                        >
                        <small>Valeur créditée à l'utilisation du code.</small>
                    </div>

                    <div class="field-shell">
                        <label for="quantite">Quantité</label>
                        <input
                            id="quantite"
                            name="quantite"
                            type="number"
                            step="1"
                            min="1"
                            max="100"
                            placeholder="Ex. 10"
                            value="<?= esc($oldQuantite) ?>"
                            required
                        >
                        <small>Nombre de codes à générer dans la table.</small>
                    </div>
                </div>

                <div class="form-actions">
                    <a class="btn btn--secondary" href="<?= base_url('bo/codes') ?>">Retour à la liste</a>
                    <button class="btn" type="submit">Générer les codes</button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
