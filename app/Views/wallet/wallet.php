<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BodyMetric - Portefeuille</title>
    <link rel="stylesheet" href="<?= base_url('css/wallet.css') ?>">
</head>
<body>
    <main class="wallet-page">
        <section class="hero-panel">
            <div class="hero-copy">
                <p class="eyebrow">Portefeuille</p>
                <h1>Gérez vos crédits en un seul endroit.</h1>
                <p class="hero-text">
                    Suivez votre solde, préparez votre code de recharge et gardez un œil sur votre historique.
                </p>
            </div>

            <div class="hero-stat">
                <span class="stat-label">Solde actuel</span>
                <strong><?= number_format((float) $balance, 2, ',', ' ') ?> €</strong>
                <small>Dernière mise à jour : <?= esc($lastUpdated) ?></small>
            </div>
        </section>

        <section class="grid-layout">
            <article class="wallet-card balance-card">
                <div class="card-heading">
                    <div>
                        <p class="card-kicker">Crédits disponibles</p>
                        <h2>Bonjour <?= esc($displayName) ?></h2>
                    </div>
                    <span class="balance-badge">Actif</span>
                </div>

                <div class="balance-value">
                    <span><?= number_format((float) $balance, 2, ',', ' ') ?></span>
                    <small>€</small>
                </div>

                <p class="card-note">
                    Le portefeuille servira à créditer vos accès et vos options premium après validation.
                </p>
            </article>

            <article class="wallet-card code-card">
                <div class="card-heading compact">
                    <div>
                        <p class="card-kicker">Recharge</p>
                        <h2>Entrer un code</h2>
                    </div>
                </div>

                <div class="code-input-shell">
                    <label for="walletCode">Code de recharge</label>
                    <div class="code-field-row">
                        <input
                            id="walletCode"
                            type="text"
                            name="code"
                            placeholder="Ex. BM-9F2K-2Q8X"
                            autocomplete="off"
                            inputmode="text"
                            aria-describedby="codeHelp"
                        >
                        <button type="button" disabled>Valider</button>
                    </div>
                    <p id="codeHelp">La validation AJAX sera branchée dans l’étape suivante.</p>
                </div>
            </article>
        </section>

        <section class="wallet-card history-card">
            <div class="card-heading compact">
                <div>
                    <p class="card-kicker">Historique</p>
                    <h2>Dernières opérations</h2>
                </div>
            </div>

            <?php if (empty($history)): ?>
                <div class="empty-state">
                    <div class="empty-icon">€</div>
                    <div>
                        <h3>Aucune opération pour le moment</h3>
                        <p>Les recharges et débits apparaîtront ici dès que le portefeuille sera utilisé.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="history-list">
                    <?php foreach ($history as $item): ?>
                        <div class="history-item">
                            <div>
                                <strong><?= esc($item['label'] ?? '') ?></strong>
                                <p><?= esc($item['date'] ?? '') ?></p>
                            </div>
                            <span class="history-amount <?= (($item['type'] ?? 'credit') === 'debit') ? 'is-debit' : 'is-credit' ?>">
                                <?= esc($item['amount'] ?? '') ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>