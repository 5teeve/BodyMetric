<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultats</title>
    <link rel="stylesheet" href="<?= base_url('css/resultats.css') ?>">
</head>

<body>
    <?= view('partials/header') ?>
    <main class="results-page">
        <header class="results-header">
            <p class="eyebrow">Resultats</p>
            <h1>Combos regime + sport</h1>
            <p class="subtext">Chaque suggestion combine un regime et plusieurs activites recommandees.</p>
        </header>

        <section class="results-section">
            <div class="section-title">
                <h2>Suggestions personnalisees</h2>
                <p>Front seulement pour l'instant, les predictions viendront plus tard.</p>
            </div>

            <?php $combos = $combos ?? []; ?>

            <?php if (empty($combos)): ?>
                <div class="empty-state">Aucune suggestion disponible.</div>
            <?php else: ?>
                <div class="card-grid">
                    <?php foreach ($combos as $combo): ?>
                        <article class="combo-card">
                            <div class="combo-header">
                                <div>
                                    <p class="combo-label">Regime</p>
                                    <h3>Regimes conseilles</h3>
                                </div>
                                <span class="tag"><?= esc((string) count($combo['regimes'] ?? [])) ?> regimes</span>
                            </div>

                            <div class="regime-list">
                                <?php if (empty($combo['regimes'])): ?>
                                    <div class="regime-card empty">Aucun regime pour le moment</div>
                                <?php else: ?>
                                    <?php foreach ($combo['regimes'] as $regime): ?>
                                        <div class="regime-card">
                                            <div class="regime-head">
                                                <span><?= esc($regime['nom'] ?? 'Regime') ?></span>
                                                <span class="tag"><?= esc((string) ($regime['duree'] ?? '0')) ?> j</span>
                                            </div>
                                            <div class="composition">
                                                <div class="pill">Viande <?= esc(number_format((float) ($regime['pct_viande'] ?? 0), 0)) ?>%</div>
                                                <div class="pill">Poisson <?= esc(number_format((float) ($regime['pct_poisson'] ?? 0), 0)) ?>%</div>
                                                <div class="pill">Volaille <?= esc(number_format((float) ($regime['pct_volaille'] ?? 0), 0)) ?>%</div>
                                            </div>
                                            <?php
                                                $prix = (float) ($regime['prix'] ?? 0);
                                                $prixPromo = $prix > 0 ? $prix * 0.85 : 0;
                                            ?>
                                            <div class="combo-price">
                                                <?php if (!empty($isGold)): ?>
                                                    <div class="price-row">
                                                        <span class="price-old"><?= esc(number_format($prix, 2, '.', ' ')) ?> Ar</span>
                                                        <span class="price-new"><?= esc(number_format($prixPromo, 2, '.', ' ')) ?> Ar</span>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="price-badge">
                                                        <?= esc(number_format($prix, 2, '.', ' ')) ?> Ar
                                                    </div>
                                                <?php endif; ?>
                                                <span>Delta poids: <?= esc($regime['delta_poids'] ?? '-') ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <div class="divider"></div>

                            <div class="combo-header">
                                <div>
                                    <p class="combo-label">Sport</p>
                                    <h3>Activites conseillees</h3>
                                </div>
                                <span class="tag"><?= esc((string) count($combo['activities'] ?? [])) ?> actions</span>
                            </div>

                            <div class="activity-list">
                                <?php if (empty($combo['activities'])): ?>
                                    <div class="activity-card empty">Aucune activite pour le moment</div>
                                <?php else: ?>
                                    <?php foreach ($combo['activities'] as $activity): ?>
                                        <div class="activity-card">
                                            <div class="activity-head">
                                                <span><?= esc($activity['title'] ?? 'Activite') ?></span>
                                                <span class="tag"><?= esc($activity['duration'] ?? '0 min') ?></span>
                                            </div>
                                            <div class="activity-meta">
                                                <span>Intensite: <?= esc($activity['intensity'] ?? '-') ?></span>
                                                <span>Objectif: <?= esc($activity['goal'] ?? '-') ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>

</html>