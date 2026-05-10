<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultats</title>
    <link rel="stylesheet" href="<?= base_url('css/resultats.css') ?>">
</head>

<body>
    <main class="results-page">
        <header class="results-header">
            <p class="eyebrow">Resultats</p>
            <h1>Regimes et activites suggeres</h1>
            <p class="subtext">Petits carreaux pour comparer vite les options.</p>
        </header>

        <section class="results-section">
            <div class="section-title">
                <h2>Regimes disponibles</h2>
                <p>Chaque regime est compose de viande, poisson et volaille.</p>
            </div>

            <?php if (empty($regimes)): ?>
                <div class="empty-state">Aucun regime disponible.</div>
            <?php else: ?>
                <div class="card-grid">
                    <?php foreach ($regimes as $regime): ?>
                        <article class="mini-card">
                            <div class="card-head">
                                <h3><?= esc($regime['nom'] ?? 'Regime') ?></h3>
                                <span class="tag"><?= esc((string) ($regime['duree'] ?? '0')) ?> j</span>
                            </div>

                            <div class="composition">
                                <div class="pill">Viande <?= esc(number_format((float) ($regime['pct_viande'] ?? 0), 0)) ?>%</div>
                                <div class="pill">Poisson <?= esc(number_format((float) ($regime['pct_poisson'] ?? 0), 0)) ?>%</div>
                                <div class="pill">Volaille <?= esc(number_format((float) ($regime['pct_volaille'] ?? 0), 0)) ?>%</div>
                            </div>

                            <div class="meta">
                                <div class="price-badge">
                                    <?= esc(number_format((float) ($regime['prix'] ?? 0), 2, '.', ' ')) ?> Ar
                                </div>
                                <span>Delta poids: <?= esc($regime['delta_poids'] ?? '-') ?></span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="results-section">
            <div class="section-title">
                <h2>Activites suggerees</h2>
                <p>Selection rapide selon votre energie.</p>
            </div>

            <div class="card-grid">
                <?php foreach ($activities as $activity): ?>
                    <article class="mini-card">
                        <div class="card-head">
                            <h3><?= esc($activity['title']) ?></h3>
                            <span class="tag"><?= esc($activity['duration']) ?></span>
                        </div>
                        <div class="meta">
                            <span>Intensite: <?= esc($activity['intensity']) ?></span>
                            <span>Objectif: <?= esc($activity['goal']) ?></span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
</body>

</html>