<?php
$activite = $activite ?? null;
$isEditing = $isEditing ?? false;
$activiteId = is_array($activite) ? (int) ($activite['id'] ?? 0) : 0;
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEditing ? 'Modifier' : 'Créer' ?> une activité - BO</title>
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

        .form-card {
            max-width: 600px;
            background: #1e2230;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
        }

        .form-header {
            margin-bottom: 1.5rem;
        }

        .form-header h1 {
            margin: 0;
            font-size: 1.5rem;
            color: #f1f5f9;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #94a3b8;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #2d3148;
            border-radius: 8px;
            font-size: 1rem;
            background: #161923;
            color: #e2e8f0;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .btn-primary {
            background: #16a34a;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
        }

        .btn-primary:hover {
            background: #15803d;
        }

        .btn-secondary {
            background: #1e2230;
            color: #94a3b8;
            padding: 0.75rem 1.5rem;
            border: 1px solid #2d3148;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-secondary:hover {
            background: #161923;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .alert-error {
            background: #450a0a;
            color: #fca5a5;
            border: 1px solid #7f1d1d;
        }

        @media (max-width: 768px) {
            .bo-content {
                margin-left: 0;
                width: 100%;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }

        body{

            background: #0f1117;
        }
    </style>
</head>

<body>
    <?= view('partials/sidebar_bo') ?>

    <main class="bo-content">
        <div class="form-card">
            <div class="form-header">
                <h1><?= $isEditing ? 'Modifier l\'activité' : 'Nouvelle activité' ?></h1>
            </div>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-error">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <p><?= $error ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="<?= $isEditing ? base_url('bo/activites/update/' . $activiteId) : base_url('bo/activites/form') ?>" method="post">
                <div class="form-group">
                    <label for="nom">Nom de l'activité *</label>
                    <input type="text" id="nom" name="nom" value="<?= old('nom', $activite['nom'] ?? '') ?>" required maxlength="100">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="type">Type *</label>
                        <select id="type" name="type" required>
                            <option value="cardio" <?= old('type', $activite['type'] ?? '') === 'cardio' ? 'selected' : '' ?>>Cardio</option>
                            <option value="musculation" <?= old('type', $activite['type'] ?? '') === 'musculation' ? 'selected' : '' ?>>Musculation</option>
                            <option value="flexibilite" <?= old('type', $activite['type'] ?? '') === 'flexibilite' ? 'selected' : '' ?>>Flexibilité</option>
                            <option value="sport" <?= old('type', $activite['type'] ?? '') === 'sport' ? 'selected' : '' ?>>Sport</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="intensite">Intensité *</label>
                        <select id="intensite" name="intensite" required>
                            <option value="faible" <?= old('intensite', $activite['intensite'] ?? '') === 'faible' ? 'selected' : '' ?>>Faible</option>
                            <option value="moderee" <?= old('intensite', $activite['intensite'] ?? '') === 'moderee' ? 'selected' : '' ?>>Modérée</option>
                            <option value="moyenne" <?= old('intensite', $activite['intensite'] ?? '') === 'moyenne' ? 'selected' : '' ?>>Moyenne</option>
                            <option value="elevee" <?= old('intensite', $activite['intensite'] ?? '') === 'elevee' ? 'selected' : '' ?>>Élevée</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="duree_base">Durée de base (minutes) *</label>
                        <input type="number" id="duree_base" name="duree_base" value="<?= old('duree_base', $activite['duree_base'] ?? '') ?>" required min="1">
                    </div>
                    <div class="form-group">
                        <label for="calories_min">Calories/minute *</label>
                        <input type="number" id="calories_min" name="calories_min" value="<?= old('calories_min', $activite['calories_min'] ?? '') ?>" required min="1">
                    </div>
                </div>

                <div class="form-group">
                    <label for="objectif">Objectif associé *</label>
                    <select id="objectif" name="objectif" required>
                        <option value="reduire" <?= old('objectif', $activite['objectif'] ?? '') === 'reduire' ? 'selected' : '' ?>>Réduire le poids</option>
                        <option value="augmenter" <?= old('objectif', $activite['objectif'] ?? '') === 'augmenter' ? 'selected' : '' ?>>Augmenter le poids</option>
                        <option value="maintenir" <?= old('objectif', $activite['objectif'] ?? '') === 'maintenir' ? 'selected' : '' ?>>Maintenir le poids</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" maxlength="500"><?= old('description', $activite['description'] ?? '') ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary"><?= $isEditing ? 'Mettre à jour' : 'Créer' ?></button>
                    <a href="<?= base_url('bo/activites') ?>" class="btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </main>
</body>

</html>