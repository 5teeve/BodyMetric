<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suggestions d'activités sportives</title>
    <link rel="stylesheet" href="<?= base_url('css/header.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/suggestions.css') ?>">
</head>

<body>
    <?= view('partials/header') ?>
    <main class="suggestions-page">
        <header class="suggestions-header">
            <p class="eyebrow">Suggestions</p>
            <h1>Activités sportives recommandées</h1>
            <p class="subtext">Sélection personnalisée basée sur votre profil et vos objectifs.</p>
        </header>

        <section class="profile-section">
            <div class="profile-card">
                <h2>Votre profil</h2>
                <div class="profile-stats">
                    <?php if (!empty($userProfile)): ?>
                        <div class="stat-item">
                            <span class="stat-label">IMC</span>
                            <span class="stat-value"><?= esc(number_format($userProfile['imc'], 1)) ?></span>
                            <span class="stat-badge <?= esc($userProfile['categorie_imc']) ?>">
                                <?= match($userProfile['categorie_imc']) {
                                    'insuffisance_ponderale' => 'Insuffisance pondérale',
                                    'normal' => 'Normal',
                                    'surpoids' => 'Surpoids',
                                    'obesite' => 'Obésité',
                                    default => 'Non catégorisé'
                                } ?>
                            </span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Poids</span>
                            <span class="stat-value"><?= esc(number_format($userProfile['poids'], 1)) ?> kg</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Taille</span>
                            <span class="stat-value"><?= esc(number_format($userProfile['taille'], 1)) ?> cm</span>
                        </div>
                    <?php else: ?>
                        <p class="empty-profile">Profil incomplet. Veuillez mettre à jour vos informations.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="objective-card">
                <h2>Objectif actuel</h2>
                <div class="objective-badge <?= esc($objectif ?? 'maintenir') ?>">
                    <?= match($objectif ?? 'maintenir') {
                        'reduire' => 'Réduire le poids',
                        'augmenter' => 'Augmenter le poids',
                        'maintenir' => 'Maintenir le poids',
                        default => 'Maintenir'
                    } ?>
                </div>
                <p class="objective-description">
                    <?php if (($objectif ?? 'maintenir') === 'reduire'): ?>
                        Privilégiez les activités cardio à intensité modérée pour brûler des graisses.
                    <?php elseif (($objectif ?? 'maintenir') === 'augmenter'): ?>
                        Concentrez-vous sur le renforcement musculaire pour gagner en masse.
                    <?php else: ?>
                        Variez les activités pour maintenir votre forme et votre bien-être.
                    <?php endif; ?>
                </p>
            </div>
        </section>

        <section class="activities-section">
            <div class="section-title">
                <h2>Activités suggérées</h2>
                <p>Durées estimées adaptées à votre profil et à votre objectif.</p>
            </div>

            <?php $suggestions = $suggestions ?? []; ?>

            <?php if (empty($suggestions)): ?>
                <div class="empty-state">
                    <p>Aucune suggestion disponible pour le moment.</p>
                </div>
            <?php else: ?>
                <div class="activities-grid">
                    <?php foreach ($suggestions as $activite): ?>
                        <article class="activity-card <?= esc($activite['intensite']) ?>">
                            <div class="activity-header">
                                <div class="activity-type-icon <?= esc($activite['type']) ?>">
                                    <?= match($activite['type']) {
                                        'cardio' => '♥',
                                        'musculation' => '💪',
                                        'flexibilite' => '🧘',
                                        'sport' => '⚡',
                                        default => '🎯'
                                    } ?>
                                </div>
                                <div class="activity-badges">
                                    <span class="badge type"><?= ucfirst(esc($activite['type'])) ?></span>
                                    <span class="badge intensite <?= esc($activite['intensite']) ?>">
                                        <?= match($activite['intensite']) {
                                            'faible' => 'Faible',
                                            'moderee' => 'Modérée',
                                            'moyenne' => 'Moyenne',
                                            'elevee' => 'Élevée',
                                            default => 'Modérée'
                                        } ?>
                                    </span>
                                </div>
                            </div>

                            <h3 class="activity-name"><?= esc($activite['nom']) ?></h3>

                            <?php if (!empty($activite['description'])): ?>
                                <p class="activity-description"><?= esc($activite['description']) ?></p>
                            <?php endif; ?>

                            <div class="activity-metrics">
                                <div class="metric">
                                    <span class="metric-label">Durée estimée</span>
                                    <span class="metric-value"><?= esc((string) $activite['duree_estimee']) ?> min</span>
                                </div>
                                <div class="metric">
                                    <span class="metric-label">Calories</span>
                                    <span class="metric-value">~<?= esc((string) $activite['calories_estimees']) ?> kcal</span>
                                </div>
                            </div>

                            <div class="activity-footer">
                                <div class="relevance-score">
                                    <span class="score-label">Recommandation</span>
                                    <div class="score-bar">
                                        <div class="score-fill" style="width: <?= min(100, (float) ($activite['score_relevance'] ?? 50)) ?>%"></div>
                                    </div>
                                    <span class="score-value"><?= esc((string) round((float) ($activite['score_relevance'] ?? 0))) ?>%</span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="tips-section">
            <h2>Conseils pour votre séance</h2>
            <div class="tips-grid">
                <div class="tip-card">
                    <span class="tip-icon">⏱️</span>
                    <h4>Durée adaptée</h4>
                    <p>Les durées suggérées sont calculées selon votre IMC et votre niveau d'activité actuel.</p>
                </div>
                <div class="tip-card">
                    <span class="tip-icon">📈</span>
                    <h4>Progression</h4>
                    <p>Augmentez progressivement l'intensité et la durée pour éviter les blessures.</p>
                </div>
                <div class="tip-card">
                    <span class="tip-icon">💧</span>
                    <h4>Hydratation</h4>
                    <p>Buvez au moins 500ml d'eau avant, pendant et après votre séance.</p>
                </div>
                <div class="tip-card">
                    <span class="tip-icon">🎯</span>
                    <h4>Régularité</h4>
                    <p>Pratiquez ces activités 3 à 5 fois par semaine pour des résultats optimaux.</p>
                </div>
            </div>
        </section>
    </main>
</body>

</html>
