<?php
$usersTotal = $usersTotal ?? 0;
$codesUsed = $codesUsed ?? 0;
$caTotal = $caTotal ?? 0.0;
$monthlyLabels = $monthlyLabels ?? [];
$monthlyData = $monthlyData ?? [];
$objectivesLabels = $objectivesLabels ?? [];
$objectivesData = $objectivesData ?? [];
$objectivesColors = $objectivesColors ?? [];
$hasChartData = !empty($monthlyLabels) && !empty($monthlyData);
$hasObjectivesData = !empty($objectivesLabels) && !empty($objectivesData);
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
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

        <section class="charts-row">
            <div class="chart-card chart-half">
                <div class="chart-header">
                    <div>
                        <p class="chart-kicker">Évolution</p>
                        <h2>Inscriptions par mois</h2>
                    </div>
                    <span class="chart-badge">12 derniers mois</span>
                </div>
                <div class="chart-container">
                    <?php if ($hasChartData): ?>
                        <canvas id="registrationsChart"></canvas>
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
                        <canvas id="objectivesChart"></canvas>
                    <?php else: ?>
                        <div class="chart-empty">
                            <p>Aucune donnée d'objectif disponible.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
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

    <?php if ($hasChartData): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('registrationsChart').getContext('2d');

            const labels = <?= json_encode($monthlyLabels) ?>;
            const data = <?= json_encode($monthlyData) ?>;

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Nouvelles inscriptions',
                        data: data,
                        borderColor: '#22c55e',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#22c55e',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: '#e2e8f0',
                                font: {
                                    size: 13
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            titleColor: '#e2e8f0',
                            bodyColor: '#e2e8f0',
                            borderColor: 'rgba(148, 163, 184, 0.2)',
                            borderWidth: 1,
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + ' inscription' + (context.parsed.y > 1 ? 's' : '');
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: 'rgba(148, 163, 184, 0.1)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#94a3b8',
                                font: {
                                    size: 11
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(148, 163, 184, 0.1)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#94a3b8',
                                font: {
                                    size: 11
                                },
                                stepSize: 1
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });

            <?php if ($hasObjectivesData): ?>
            // Objectives Pie Chart
            const ctxObj = document.getElementById('objectivesChart').getContext('2d');
            new Chart(ctxObj, {
                type: 'pie',
                data: {
                    labels: <?= json_encode($objectivesLabels) ?>,
                    datasets: [{
                        data: <?= json_encode($objectivesData) ?>,
                        backgroundColor: <?= json_encode($objectivesColors) ?>,
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#e2e8f0',
                                padding: 15,
                                font: { size: 12 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
            <?php endif; ?>
        });
    </script>
    <?php endif; ?>
</body>
</html>
