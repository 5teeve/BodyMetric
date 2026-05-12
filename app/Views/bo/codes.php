<?php
$codes = $codes ?? [];
$success = $success ?? null;
$errors = $errors ?? [];
$statut = $statut ?? null;
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>BO — Liste des codes</title>
    <link rel="stylesheet" href="<?= base_url('css/header.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/wallet.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/bo_codes.css') ?>">
</head>
<body data-wallet-page="true">
    <?= view('partials/sidebar_bo') ?>
    <main class="bo-content">
        <section class="wallet-card">
            <div class="card-heading">
                <div>
                    <p class="card-kicker">BO — Codes</p>
                    <h2>Liste des codes</h2>
                </div>
                <a class="btn" href="<?= base_url('bo/codes/form') ?>">Générer des codes</a>
            </div>

            <form method="get" class="filter-row" action="<?= base_url('bo/codes') ?>">
                <label for="statut" class="filter-label">Filtrer par statut</label>
                <select id="statut" name="statut" class="filter-select" onchange="this.form.submit()">
                    <option value="" <?= $statut === null || $statut === '' ? 'selected' : '' ?>>Tous</option>
                    <option value="actif" <?= $statut === 'actif' ? 'selected' : '' ?>>Actifs</option>
                    <option value="utilise" <?= $statut === 'utilise' ? 'selected' : '' ?>>Utilisés</option>
                </select>
            </form>

            <?php if (! empty($success)): ?>
                <div class="form-alert form-alert--success" style="margin-bottom:16px;">
                    <?= esc($success) ?>
                </div>
            <?php endif; ?>

            <?php if (! empty($errors)): ?>
                <div class="form-alert form-alert--error" style="margin-bottom:16px;">
                    <strong>Erreur :</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= esc((string) $error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="codes-table-wrap">
                <table class="codes-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Utilisé par</th>
                            <th>Utilisé le</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($codes)): ?>
                        <tr>
                            <td colspan="6">
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
                                $usedBy = trim(($c['prenom'] ?? '') . ' ' . ($c['nom'] ?? ''));
                            ?>
                            <tr>
                                <td class="mono"><?= esc((string) ($c['code'] ?? '')) ?></td>
                                <td><?= number_format((float) ($c['montant'] ?? 0), 2, ',', ' ') ?> Ar</td>
                                <td>
                                    <?php if ($isActif): ?>
                                        <span class="balance-badge">Actif</span>
                                    <?php else: ?>
                                        <span class="badge--muted" style="color:var(--muted);padding:6px 10px;border-radius:999px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.02);">Utilisé</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $usedBy !== '' ? esc($usedBy) : '-' ?></td>
                                <td><?= $usedAt ? date('d/m/Y H:i', strtotime($usedAt)) : '-' ?></td>
                                <td>
                                    <div class="action-group">
                                        <a class="btn" href="<?= base_url('bo/codes/form/' . ($c['id'] ?? 0)) ?>">Modifier</a>

                                        <?php if ($isActif): ?>
                                            <form method="post" action="<?= base_url('bo/codes/invalidate/' . ($c['id'] ?? 0)) ?>" class="inline-form">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn--secondary">Invalider</button>
                                            </form>
                                        <?php endif; ?>

                                        <form method="post" action="<?= base_url('bo/codes/delete/' . ($c['id'] ?? 0)) ?>" class="inline-form" onsubmit="return confirm('Supprimer ce code ?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn--secondary">Supprimer</button>
                                        </form>
                                    </div>
                                </td>
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
