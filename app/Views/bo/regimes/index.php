<?php
$regimes = $regimes ?? [];
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
            background: #0f1117;
            width: calc(100% - 260px);
            box-sizing: border-box;
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
            color: #f1f5f9;
        }

        .crud-table {
            width: 100%;
            background: #1e2230;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
        }

        .crud-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .crud-table th,
        .crud-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #2d3148;
        }

        .crud-table th {
            background: #161923;
            font-weight: 600;
            color: #94a3b8;
            font-size: 0.875rem;
        }

        .crud-table td {
            color: #cbd5e1;
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

        .badge-viande {
            background: #450a0a;
            color: #fca5a5;
        }

        .badge-poisson {
            background: #1e3a5f;
            color: #93c5fd;
        }

        .badge-volaille {
            background: #422006;
            color: #fcd34d;
        }

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
            background: #2563eb;
            color: white;
        }

        .btn-edit:hover {
            background: #1d4ed8;
        }

        .btn-delete {
            background: #dc2626;
            color: white;
        }

        .btn-delete:hover {
            background: #b91c1c;
        }

        .btn-add {
            background: #16a34a;
            color: white;
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
        }

        .btn-add:hover {
            background: #15803d;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #475569;
        }

        @media (max-width: 768px) {
            .bo-content {
                margin-left: 0;
                width: 100%;
            }

            .crud-table {
                overflow-x: auto;
            }
        }
        body{

            background: #161923;
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