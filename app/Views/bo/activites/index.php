<?php
$activites = $activites ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Activités - BO</title>
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
        .badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-weight: 500;
        }
        .badge-cardio { background: #dbeafe; color: #1e40af; }
        .badge-musculation { background: #fee2e2; color: #991b1b; }
        .badge-flexibilite { background: #f3e8ff; color: #7c3aed; }
        .badge-sport { background: #d1fae5; color: #065f46; }
        .badge-faible { background: #d1fae5; color: #065f46; }
        .badge-moderee { background: #dbeafe; color: #1e40af; }
        .badge-moyenne { background: #fef3c7; color: #92400e; }
        .badge-elevee { background: #fee2e2; color: #991b1b; }
        .badge-reduire { background: #fef3c7; color: #92400e; }
        .badge-augmenter { background: #dbeafe; color: #1e40af; }
        .badge-maintenir { background: #d1fae5; color: #065f46; }
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
        .truncate {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
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
                <h1>Gestion des Activités Sportives</h1>
                <p><?= count($activites) ?> activité(s) enregistrée(s)</p>
            </div>
            <a href="<?= base_url('bo/activites/form') ?>" class="btn-add">+ Nouvelle activité</a>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <div class="crud-table">
            <?php if (empty($activites)): ?>
                <div class="empty-state">
                    <p>Aucune activité enregistrée.</p>
                    <a href="<?= base_url('bo/activites/form') ?>" class="btn-add" style="margin-top: 1rem; display: inline-block;">Créer une activité</a>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Type</th>
                            <th>Intensité</th>
                            <th>Durée</th>
                            <th>Cal/min</th>
                            <th>Objectif</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activites as $activite): ?>
                            <tr>
                                <td><strong><?= esc($activite['nom']) ?></strong></td>
                                <td><span class="badge badge-<?= $activite['type'] ?>"><?= ucfirst($activite['type']) ?></span></td>
                                <td><span class="badge badge-<?= $activite['intensite'] ?>"><?= ucfirst($activite['intensite']) ?></span></td>
                                <td><?= $activite['duree_base'] ?> min</td>
                                <td><?= $activite['calories_min'] ?> kcal</td>
                                <td><span class="badge badge-<?= $activite['objectif'] ?>"><?= ucfirst($activite['objectif']) ?></span></td>
                                <td class="truncate" title="<?= esc($activite['description']) ?>"><?= esc($activite['description']) ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="<?= base_url('bo/activites/form/' . $activite['id']) ?>" class="btn-sm btn-edit">Modifier</a>
                                        <form action="<?= base_url('bo/activites/delete/' . $activite['id']) ?>" method="post" style="display: inline;" onsubmit="return confirm('Supprimer cette activité ?')">
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
