<?php
$usersTotal = $usersTotal ?? 0;
$goldMembers = $goldMembers ?? 0;
$codesTotal = $codesTotal ?? 0;
$codesUsed = $codesUsed ?? 0;
$caTotal = $caTotal ?? 0.0;
$regimesSold = $regimesSold ?? 0;
$topRegimes = $topRegimes ?? [];
$monthlyLabels = $monthlyLabels ?? [];
$monthlyData = $monthlyData ?? [];
$objectivesLabels = $objectivesLabels ?? [];
$objectivesData = $objectivesData ?? [];
$objectivesColors = $objectivesColors ?? [];
$hasChartData = !empty($monthlyLabels) && !empty($monthlyData);
$hasObjectivesData = !empty($objectivesLabels) && !empty($objectivesData);
$monthlyMax = $hasChartData ? max($monthlyData) : 0;
$objectivesMax = $hasObjectivesData ? max($objectivesData) : 0;
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
    <?= view('partials/sidebar_bo') ?>
    <main class="bo-dashboard-content">
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
                <p class="kpi-label">Régimes vendus</p>
                <h2><?= esc((string) $regimesSold) ?></h2>
                <p class="kpi-meta">Achats enregistrés.</p>
            </article>
            <article class="kpi-card">
                <p class="kpi-label">Codes validés</p>
                <h2><?= esc((string) $codesUsed) ?> / <?= esc((string) $codesTotal) ?></h2>
                <p class="kpi-meta">CA: <?= esc(number_format($caTotal, 2, ',', ' ')) ?> Ar</p>
            </article>
            <article class="kpi-card">
                <p class="kpi-label">Membres Gold</p>
                <h2><?= esc((string) $goldMembers) ?></h2>
                <p class="kpi-meta">Comptes premium.</p>
            </article>
        </section>

        <section class="charts-row">
            <div class="chart-card chart-half">
                <div class="chart-header">
                    <div>
                        <p class="chart-kicker">Évolution</p>
                        <h2>Inscriptions par mois</h2>
                    </div>
                    <span class="chart-badge">6 derniers mois</span>
                </div>
                <div class="chart-container">
                    <?php if ($hasChartData): ?>
                        <div class="bar-chart">
                            <?php foreach ($monthlyLabels as $index => $label): ?>
                                <?php $value = (int) ($monthlyData[$index] ?? 0); $height = $monthlyMax > 0 ? max(8, ($value / $monthlyMax) * 100) : 0; ?>
                                <div class="bar-item">
                                    <div class="bar-value"><?= esc((string) $value) ?></div>
                                    <div class="bar-track">
                                        <div class="bar-fill" style="height: <?= esc((string) $height) ?>%;"></div>
                                    </div>
                                    <div class="bar-label"><?= esc($label) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="chart-empty">
                            <p>Aucune donnée d'inscription disponible.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="chart-card chart-half">
                <div class="chart-header">
                    <div>
                        <p class="chart-kicker">Répartition</p>
                        <h2>Objectifs des utilisateurs</h2>
                    </div>
                </div>
                <div class="chart-container">
                    <?php if ($hasObjectivesData): ?>
                        <div class="objective-chart">
                            <?php foreach ($objectivesLabels as $index => $label): ?>
                                <?php $value = (int) ($objectivesData[$index] ?? 0); $percent = $objectivesMax > 0 ? ($value / $objectivesMax) * 100 : 0; $color = $objectivesColors[$index] ?? '#22c55e'; ?>
                                <div class="objective-row">
                                    <div class="objective-head">
                                        <span class="objective-label"><?= esc($label) ?></span>
                                        <span class="objective-count"><?= esc((string) $value) ?></span>
                                    </div>
                                    <div class="objective-track">
                                        <div class="objective-fill" style="width: <?= esc((string) $percent) ?>%; background: <?= esc($color) ?>;"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="chart-empty">
                            <p>Aucune donnée d'objectif disponible.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="chart-card top-regimes">
            <div class="chart-header">
                <div>
                    <p class="chart-kicker">Top ventes</p>
                    <h2>Régimes les plus vendus</h2>
                </div>
            </div>

            <?php if (!empty($topRegimes)): ?>
                <div class="top-regimes-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Régime</th>
                                <th>Ventes</th>
                                <th>CA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topRegimes as $row): ?>
                                <tr>
                                    <td><?= esc((string) ($row['nom'] ?? '')) ?></td>
                                    <td><?= esc((string) ($row['total'] ?? 0)) ?></td>
                                    <td><?= esc(number_format((float) ($row['ca'] ?? 0), 2, ',', ' ')) ?> Ar</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="chart-empty">
                    <p>Aucun achat de régime pour l'instant.</p>
                </div>
            <?php endif; ?>
        </section>

        <section class="quick-actions">
            <h2>Accès rapide</h2>
            <div class="actions-grid">
                <a href="<?= base_url('bo/regimes') ?>" class="action-card">
                    <span class="action-icon">🍽️</span>
                    <span class="action-label">Gérer les régimes</span>
                </a>
                <a href="<?= base_url('bo/activites') ?>" class="action-card">
                    <span class="action-icon">🏃</span>
                    <span class="action-label">Gérer les activités</span>
                </a>
                <a href="<?= base_url('bo/codes') ?>" class="action-card">
                    <span class="action-icon">🎫</span>
                    <span class="action-label">Gérer les codes</span>
                </a>
                <a href="<?= base_url('bo/parametres') ?>" class="action-card">
                    <span class="action-icon">⚙️</span>
                    <span class="action-label">Paramètres</span>
                </a>
            </div>
        </section>
    </main>
</body>
</html>
