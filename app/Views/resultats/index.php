<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultats</title>
    <link rel="stylesheet" href="<?= base_url('css/header.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/resultats.css') ?>">
    <style>
        .app-nav {
            max-width: 1300px;
            padding: 18px 24px;
        }

        .app-nav .nav-links {
            flex-wrap: nowrap;
        }

        .app-nav .nav-links a {
            white-space: nowrap;
        }
    </style>
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

                            <?php
                                $comboTotal = array_reduce($combo['regimes'] ?? [], function ($carry, $regime) {
                                    return $carry + ((float) ($regime['prix'] ?? 0));
                                }, 0.0);
                                $afficheTotal = !empty($isGold) ? ($comboTotal * 0.85) : $comboTotal;
                            ?>
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
                                                    <div class="combo-price">
                                                <span>Delta poids: <?= esc($regime['delta_poids'] ?? '-') ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <div class="combo-action">
                                <div class="combo-total-price">
                                    <span>Total du pack:</span>
                                    <strong><?= esc(number_format($afficheTotal, 2, '.', ' ')) ?> Ar</strong>
                                    <?php if (!empty($isGold)): ?>
                                        <small>Prix Gold</small>
                                    <?php endif; ?>
                                </div>
                                <?php $regimeIds = array_map(fn($regime) => (int) ($regime['id'] ?? 0), $combo['regimes'] ?? []); ?>
                                <button class="choose-combo-btn" onclick='chooseCombo(<?= json_encode($regimeIds, JSON_HEX_APOS | JSON_HEX_QUOT) ?>, this)'>
                                    Acheter le pack
                                </button>
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

    <style>
        .choose-regime-btn,
        .choose-combo-btn {
            width: 100%;
            padding: 0.8rem;
            margin-top: 0.8rem;
            background: linear-gradient(135deg, #1A7A48 0%, #0f5a3a 100%);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .choose-regime-btn:hover,
        .choose-combo-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(26, 122, 72, 0.3);
        }

        .choose-regime-btn:disabled,
        .choose-combo-btn:disabled {
            background: #999;
            cursor: not-allowed;
            transform: none;
        }

        .combo-action {
            margin-top: 1rem;
        }

        .combo-total-price {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
            padding: 1rem 1.2rem;
            background: #f0fdf4;
            border: 1px solid #d1fae5;
            border-radius: 12px;
            font-size: 0.95rem;
            color: #0f5132;
        }

        .combo-total-price strong {
            font-size: 1.1rem;
            color: #166534;
        }

        .combo-total-price small {
            display: inline-block;
            margin-left: 0.5rem;
            color: #166534;
            opacity: 0.9;
        }

        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 0.5rem;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>

    <script>
        /**
         * Choisir un pack de régimes
         */
        async function chooseCombo(regimeIds, button) {
            try {
                button.disabled = true;
                const originalText = button.innerText;
                button.innerHTML = '<span class="loading-spinner"></span>Chargement...';

                const csrfToken = '<?= csrf_hash() ?>';
                const csrfName = '<?= csrf_token() ?>';

                const response = await fetch('<?= base_url('/regimes/choisir-combo') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ regime_ids: regimeIds, [csrfName]: csrfToken })
                });

                const data = await response.json();

                if (data.success) {
                    alert('✓ Pack de régimes choisi avec succès!');
                    setTimeout(() => {
                        window.location.href = '<?= base_url('/mes-regimes') ?>';
                    }, 500);
                } else {
                    button.disabled = false;
                    button.innerText = originalText;

                    if (response.status === 402) {
                        alert('❌ Solde insuffisant\n\nVous n\'avez pas assez de pièces d\'or pour ce pack.');
                    } else if (response.status === 409) {
                        alert('⚠️ Vous avez déjà acheté un des régimes de ce pack');
                    } else if (response.status === 404) {
                        alert('❌ Régime non trouvé');
                    } else {
                        alert('❌ Erreur: ' + (data.error || 'Impossible de choisir ce pack')); 
                    }
                }
            } catch (error) {
                console.error('Erreur:', error);
                button.disabled = false;
                button.innerText = originalText;
                alert('❌ Erreur serveur. Veuillez réessayer.');
            }
        }
    </script>
</body>

</html>