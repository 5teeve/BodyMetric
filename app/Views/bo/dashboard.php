<?php
$usersTotal = $usersTotal ?? 0;
$codesUsed = $codesUsed ?? 0;
$caTotal = $caTotal ?? 0.0;
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>BO — Tableau de bord</title>
    <link rel="stylesheet" href="<?= base_url('css/header.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/wallet.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/bo_dashboard.css') ?>">
</head>
<body data-wallet-page="true">
    <?= view('partials/header', ['isAdmin' => $isAdmin ?? false, 'isConnected' => $isConnected ?? false]) ?>
    <main class="wallet-page">
        <section class="hero-panel bo-dashboard-hero">
            <div class="hero-copy">
                <p class="eyebrow">Back-office</p>
                <h1>Tableau de bord</h1>
                <p class="hero-text">Vue rapide des indicateurs clefs.</p>
            </div>
            <div class="hero-stat">
                <span class="stat-label">Mise a jour</span>
                <strong><?= esc(date('d/m/Y H:i')) ?></strong>
                <small>Donnees en temps reel.</small>
            </div>
        </section>

        <section class="kpi-grid">
            <article class="kpi-card">
                <p class="kpi-label">Utilisateurs</p>
                <h2><?= esc((string) $usersTotal) ?></h2>
                <p class="kpi-meta">Nombre total de comptes.</p>
            </article>
            <article class="kpi-card">
                <p class="kpi-label">CA (codes utilises)</p>
                <h2><?= esc(number_format($caTotal, 2, ',', ' ')) ?> Ar</h2>
                <p class="kpi-meta">Somme des codes valides.</p>
            </article>
            <article class="kpi-card">
                <p class="kpi-label">Codes valides</p>
                <h2><?= esc((string) $codesUsed) ?></h2>
                <p class="kpi-meta">Codes utilises.</p>
            </article>
        </section>

        <section class="wallet-card bo-dashboard-card">
            <div class="card-heading">
                <div>
                    <p class="card-kicker">Actions</p>
                    <h2>Gerer les codes</h2>
                </div>
                <div class="action-group">
                    <a class="btn" href="<?= base_url('bo/codes') ?>">Voir les codes</a>
                    <a class="btn btn--secondary" href="<?= base_url('bo/codes/form') ?>">Generer des codes</a>
                </div>
            </div>
            <p class="bo-dashboard-note">Utilise les boutons pour acceder rapidement aux operations back-office.</p>
        </section>
    </main>
</body>
</html>
