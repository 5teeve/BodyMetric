<?php
$codes = $codes ?? [];
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>BO — Liste des codes</title>
    <link rel="stylesheet" href="<?= base_url('css/wallet.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/bo_codes.css') ?>">
</head>
<body data-wallet-page="true">
    <main class="wallet-page">
        <section class="wallet-card">
            <div class="card-heading">
                <div>
                    <p class="card-kicker">BO — Codes</p>
                    <h2>Liste des codes</h2>
                </div>
                <a class="btn" href="<?= base_url('bo/codes/form') ?>">Générer des codes</a>
            </div>

            <div class="codes-table-wrap">
                <table class="codes-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Utilisé le</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($codes)): ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <div class="empty-icon">#</div>
                                    <div>
                                        <h3>Aucun code trouvé</h3>
                                        <p>La table `codes` est vide.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($codes as $c): ?>
                            <?php
                                $statut = $c['statut'] ?? ($c['status'] ?? 'actif');
                                $isActif = ($statut === 'actif');
                                $usedAt = $c['date_utilisation'] ?? null;
                            ?>
                            <tr>
                                <td class="mono"><?= esc($c['code'] ?? '') ?></td>
                                <td><?= number_format((float) ($c['montant'] ?? 0), 2, ',', ' ') ?> €</td>
                                <td>
                                    <?php if ($isActif): ?>
                                        <span class="balance-badge">Actif</span>
                                    <?php else: ?>
                                        <span class="badge--muted" style="color:var(--muted);padding:6px 10px;border-radius:999px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.02);">Utilisé</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $usedAt ? date('d/m/Y H:i', strtotime($usedAt)) : '-' ?></td>
                                <td><a class="btn" href="#">Voir</a></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <nav class="pagination-wrap" aria-label="Pagination">
                <?php if (isset($pager)): ?>
                    <?= $pager->links() ?>
                <?php endif; ?>
            </nav>
        </section>
    </main>

    
</body>
</html>
