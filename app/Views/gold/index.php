<?php
$user = $user ?? [];
$goldPrice = $goldPrice ?? 100000.0;
$isGold = $isGold ?? false;
$wallet = $wallet ?? 0.0;
$canAfford = $wallet >= $goldPrice;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Option Gold - BodyMetric</title>
    <link rel="stylesheet" href="<?= base_url('css/header.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/gold.css') ?>">
</head>
<body>
    <?= view('partials/header', ['isAdmin' => $isAdmin ?? false, 'isConnected' => $isConnected ?? false]) ?>

    <main class="gold-page">
        <section class="gold-hero">
            <div class="gold-badge-large">GOLD</div>
            <h1>Devenez membre Gold</h1>
            <p class="gold-subtitle">Débloquez des avantages exclusifs et optimisez votre parcours santé</p>
        </section>

        <?php if ($isGold): ?>
            <section class="gold-status-section">
                <div class="gold-card status-active">
                    <div class="status-icon">✓</div>
                    <h2>Vous êtes membre Gold !</h2>
                    <p>Profitez de tous vos avantages exclusifs dès maintenant.</p>
                    <a href="/resultats" class="btn-primary">Voir mes suggestions</a>
                </div>
            </section>
        <?php else: ?>
            <section class="gold-pricing">
                <div class="price-card">
                    <div class="price-header">
                        <span class="price-label">Prix unique</span>
                        <div class="price-value">
                            <span class="amount"><?= number_format($goldPrice, 0, ',', ' ') ?></span>
                            <span class="currency">Ar</span>
                        </div>
                    </div>

                    <div class="wallet-status <?= $canAfford ? 'sufficient' : 'insufficient' ?>">
                        <span class="wallet-label">Votre solde :</span>
                        <span class="wallet-amount"><?= number_format($wallet, 2, ',', ' ') ?> Ar</span>
                        <?php if (!$canAfford): ?>
                            <span class="wallet-message">Solde insuffisant. Rechargez votre portefeuille.</span>
                        <?php endif; ?>
                    </div>

                    <?php if ($canAfford): ?>
                        <button
                            type="button"
                            id="btn-buy-gold"
                            class="btn-buy"
                            data-price="<?= $goldPrice ?>"
                            data-csrf-name="<?= csrf_token() ?>"
                            data-csrf-hash="<?= csrf_hash() ?>"
                            data-endpoint="<?= base_url('profil/gold-ajax') ?>"
                        >
                            Activer maintenant
                        </button>
                    <?php else: ?>
                        <a href="/portefeuille" class="btn-recharge">Recharger mon portefeuille</a>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

        <section class="benefits-section">
            <h2>Avantages Gold</h2>
            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon">💰</div>
                    <h3>Remise 15%</h3>
                    <p>Sur tous les régimes alimentaires suggérés</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">📊</div>
                    <h3>Export PDF</h3>
                    <p>Téléchargez vos plans personnalisés en PDF</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">⭐</div>
                    <h3>Prioritaire</h3>
                    <p>Accès prioritaire aux nouvelles fonctionnalités</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">🏆</div>
                    <h3>Badge Exclusif</h3>
                    <p>Badge Gold visible sur votre profil</p>
                </div>
            </div>
        </section>

        <section class="how-it-works">
            <h2>Comment ça marche ?</h2>
            <div class="steps">
                <div class="step">
                    <span class="step-number">1</span>
                    <p>Activez l'option Gold une seule fois</p>
                </div>
                <div class="step">
                    <span class="step-number">2</span>
                    <p>Profitez immédiatement de la remise 15%</p>
                </div>
                <div class="step">
                    <span class="step-number">3</span>
                    <p>Accès illimité à vie aux avantages</p>
                </div>
            </div>
        </section>
    </main>

    <script src="<?= base_url('js/gold.js') ?>"></script>
</body>
</html>
