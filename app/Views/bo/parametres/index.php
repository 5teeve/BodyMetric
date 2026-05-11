<?php
$parametres = $parametres ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - BO</title>
    <link rel="stylesheet" href="<?= base_url('css/header.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/bo_dashboard.css') ?>">
    <style>
        .bo-content {
            margin-left: 260px;
            padding: 2rem;
            min-height: 100vh;
            background: #f1f5f9;
        }
        .param-header {
            margin-bottom: 1.5rem;
        }
        .param-header h1 {
            margin: 0;
            font-size: 1.5rem;
            color: #1f2937;
        }
        .param-form {
            max-width: 600px;
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .param-group {
            margin-bottom: 1.5rem;
        }
        .param-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #374151;
        }
        .param-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 1rem;
        }
        .param-group input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .param-description {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.5rem;
        }
        .param-section {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }
        .param-section:last-child {
            border-bottom: none;
        }
        .section-title {
            font-size: 1.125rem;
            color: #1f2937;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #22c55e;
        }
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        .btn-primary {
            background: #22c55e;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
        }
        .btn-primary:hover { background: #16a34a; }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        @media (max-width: 768px) {
            .bo-content { margin-left: 0; }
        }
    </style>
</head>
<body>
    <?= view('partials/sidebar_bo') ?>

    <main class="bo-content">
        <div class="param-header">
            <h1>Paramètres Généraux</h1>
            <p>Configuration des valeurs clés de l'application</p>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('bo/parametres/update') ?>" method="post" class="param-form">
            
            <div class="param-section">
                <h2 class="section-title">💰 Option Gold</h2>
                <div class="param-group">
                    <label for="prix_gold">Prix de l'option Gold (Ar)</label>
                    <input type="number" id="prix_gold" name="parametres[prix_gold]" 
                           value="<?= esc($parametres['prix_gold']['valeur'] ?? '100000') ?>" 
                           required min="0" step="1">
                    <p class="param-description"><?= $parametres['prix_gold']['description'] ?? 'Prix pour passer en membre Gold' ?></p>
                </div>
                <div class="param-group">
                    <label for="remise_gold_pourcent">Remise Gold (%)</label>
                    <input type="number" id="remise_gold_pourcent" name="parametres[remise_gold_pourcent]" 
                           value="<?= esc($parametres['remise_gold_pourcent']['valeur'] ?? '15') ?>" 
                           required min="0" max="100" step="1">
                    <p class="param-description"><?= $parametres['remise_gold_pourcent']['description'] ?? 'Pourcentage de remise sur les régimes pour les membres Gold' ?></p>
                </div>
            </div>

            <div class="param-section">
                <h2 class="section-title">📊 Seuils IMC</h2>
                <div class="param-group">
                    <label for="imc_seuil_maigreur">Seuil Maigreur</label>
                    <input type="number" id="imc_seuil_maigreur" name="parametres[imc_seuil_maigreur]" 
                           value="<?= esc($parametres['imc_seuil_maigreur']['valeur'] ?? '18.5') ?>" 
                           required min="0" step="0.1">
                    <p class="param-description"><?= $parametres['imc_seuil_maigreur']['description'] ?? 'IMC en dessous duquel on considère la maigreur' ?></p>
                </div>
                <div class="param-group">
                    <label for="imc_seuil_surpoids">Seuil Surpoids</label>
                    <input type="number" id="imc_seuil_surpoids" name="parametres[imc_seuil_surpoids]" 
                           value="<?= esc($parametres['imc_seuil_surpoids']['valeur'] ?? '25') ?>" 
                           required min="0" step="0.1">
                    <p class="param-description"><?= $parametres['imc_seuil_surpoids']['description'] ?? 'IMC à partir duquel on considère le surpoids' ?></p>
                </div>
                <div class="param-group">
                    <label for="imc_seuil_obesite">Seuil Obésité</label>
                    <input type="number" id="imc_seuil_obesite" name="parametres[imc_seuil_obesite]" 
                           value="<?= esc($parametres['imc_seuil_obesite']['valeur'] ?? '30') ?>" 
                           required min="0" step="0.1">
                    <p class="param-description"><?= $parametres['imc_seuil_obesite']['description'] ?? 'IMC à partir duquel on considère l\'obésité' ?></p>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Enregistrer les modifications</button>
            </div>
        </form>
    </main>
</body>
</html>
