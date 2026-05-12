<?php
$regimes = $regimes ?? [];
$objectif = $objectif ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Régimes - BO</title>
    <link rel="stylesheet" href="<?= base_url('css/header.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/bo_dashboard.css') ?>">
    <style>
        .bo-content {
            margin-left: 260px;
            padding: 2rem;
            min-height: 100vh;
            background: #f1f5f9;
        }
        .crud-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .crud-title h1 {
            margin: 0;
            font-size: 1.5rem;
            color: #1f2937;
        }
        .crud-table {
            width: 100%;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .crud-table table {
            width: 100%;
            border-collapse: collapse;
        }
        .crud-table th,
        .crud-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        .crud-table th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
            font-size: 0.875rem;
        }
        .crud-table td {
            color: #4b5563;
        }
        .composition-badges {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .badge-comp {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-weight: 500;
        }
        .badge-viande { background: #fee2e2; color: #991b1b; }
        .badge-poisson { background: #dbeafe; color: #1e40af; }
        .badge-volaille { background: #fef3c7; color: #92400e; }
        .actions {
            display: flex;
            gap: 0.5rem;
        }
        .btn-sm {
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            font-size: 0.875rem;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        .btn-edit {
            background: #3b82f6;
            color: white;
        }
        .btn-edit:hover { background: #2563eb; }
        .btn-delete {
            background: #ef4444;
            color: white;
        }
        .btn-delete:hover { background: #dc2626; }
        .btn-add {
            background: #22c55e;
            color: white;
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
        }
        .btn-add:hover { background: #16a34a; }
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6b7280;
        }
        .filter-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 1rem;
        }
        .filter-label {
            font-size: 0.875rem;
            color: #374151;
        }
        .filter-select {
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            background: white;
        }
        @media (max-width: 768px) {
            .bo-content { margin-left: 0; }
            .crud-table { overflow-x: auto; }
        }
    </style>
</head>
<body>
    <?= view('partials/sidebar_bo') ?>

    <main class="bo-content">
        <div class="crud-header">
            <div class="crud-title">
                <h1>Gestion des Régimes</h1>
                <p><?= count($regimes) ?> régime(s) enregistré(s)</p>
            </div>
            <a href="<?= base_url('bo/regimes/form') ?>" class="btn-add">+ Nouveau régime</a>
        </div>

        <form method="get" class="filter-row" action="<?= base_url('bo/regimes') ?>">
            <label for="objectif" class="filter-label">Filtrer par objectif</label>
            <select id="objectif" name="objectif" class="filter-select" onchange="this.form.submit()">
                <option value="" <?= $objectif === '' ? 'selected' : '' ?>>Tous</option>
                <option value="augmenter" <?= $objectif === 'augmenter' ? 'selected' : '' ?>>Augmenter</option>
                <option value="reduire" <?= $objectif === 'reduire' ? 'selected' : '' ?>>Réduire</option>
                <option value="equilibre" <?= $objectif === 'equilibre' ? 'selected' : '' ?>>Équilibre</option>
            </select>
        </form>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <div class="crud-table">
            <?php if (empty($regimes)): ?>
                <div class="empty-state">
                    <p>Aucun régime enregistré.</p>
                    <a href="<?= base_url('bo/regimes/form') ?>" class="btn-add" style="margin-top: 1rem; display: inline-block;">Créer un régime</a>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Composition</th>
                            <th>Durée</th>
                            <th>Prix</th>
                            <th>Delta poids</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($regimes as $regime): ?>
                            <tr>
                                <td><strong><?= esc((string) $regime['nom']) ?></strong></td>
                                <td>
                                    <div class="composition-badges">
                                        <span class="badge-comp badge-viande">V: <?= $regime['pct_viande'] ?>%</span>
                                        <span class="badge-comp badge-poisson">P: <?= $regime['pct_poisson'] ?>%</span>
                                        <span class="badge-comp badge-volaille">Vo: <?= $regime['pct_volaille'] ?>%</span>
                                    </div>
                                </td>
                                <td><?= $regime['duree'] ?> jours</td>
                                <td><?= number_format($regime['prix'], 2, ',', ' ') ?> Ar</td>
                                <td><?= $regime['delta_poids'] > 0 ? '+' : '' ?><?= $regime['delta_poids'] ?> kg</td>
                                <td>
                                    <div class="actions">
                                        <a href="<?= base_url('bo/regimes/form/' . $regime['id']) ?>" class="btn-sm btn-edit">Modifier</a>
                                        <form action="<?= base_url('bo/regimes/delete/' . $regime['id']) ?>" method="post" style="display: inline;" onsubmit="return confirm('Supprimer ce régime ?')">
                                            <button type="submit" class="btn-sm btn-delete">Supprimer</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
