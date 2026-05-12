<?php
$pageTitle = 'Mes Régimes';
$isConnected = (bool) session()->get('user_id');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle) ?> - BodyMetric</title>
    <link rel="stylesheet" href="<?= base_url('css/header.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/resultats.css') ?>">
    <style>
        .my-regimes-page {
            padding: 2rem 1rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .my-regimes-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .my-regimes-header h1 {
            font-size: 2.5rem;
            color: #1A7A48;
            margin: 1rem 0;
        }

        .my-regimes-header p {
            color: #666;
            font-size: 1.1rem;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, #1A7A48 0%, #0f5a3a 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .stat-card .number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .stat-card .label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            background: #f5f5f5;
            border-radius: 8px;
            border: 2px dashed #ddd;
        }

        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .empty-state-text {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .empty-state-btn {
            display: inline-block;
            background: #1A7A48;
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 4px;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .empty-state-btn:hover {
            background: #0f5a3a;
        }

        .regimes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .regime-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .regime-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        }

        .regime-card-header {
            background: linear-gradient(135deg, #1A7A48 0%, #0f5a3a 100%);
            color: white;
            padding: 1.5rem;
        }

        .regime-card-header h3 {
            margin: 0;
            font-size: 1.3rem;
        }

        .regime-status {
            display: inline-block;
            font-size: 0.8rem;
            background: rgba(255, 255, 255, 0.3);
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            margin-top: 0.5rem;
        }

        .regime-status.actif {
            background: #4caf50;
            color: white;
        }

        .regime-status.termine {
            background: #ff9800;
            color: white;
        }

        .regime-status.annule {
            background: #f44336;
            color: white;
        }

        .regime-card-body {
            padding: 1.5rem;
        }

        .regime-info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.9rem;
        }

        .regime-info-row:last-child {
            border-bottom: none;
        }

        .regime-info-label {
            color: #666;
            font-weight: 500;
        }

        .regime-info-value {
            color: #1A7A48;
            font-weight: 600;
        }

        .composition-bar {
            margin: 1rem 0;
            height: 8px;
            background: #f0f0f0;
            border-radius: 4px;
            overflow: hidden;
            display: flex;
        }

        .composition-bar-segment {
            height: 100%;
        }

        .viande {
            background: #d4a574;
        }

        .poisson {
            background: #4a90e2;
        }

        .volaille {
            background: #f5a623;
        }

        .composition-legend {
            display: flex;
            gap: 1rem;
            font-size: 0.85rem;
            margin: 0.5rem 0;
            flex-wrap: wrap;
        }

        .composition-item {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .composition-color {
            width: 12px;
            height: 12px;
            border-radius: 2px;
        }

        .price-info {
            background: #f9f9f9;
            padding: 1rem;
            border-radius: 4px;
            margin: 1rem 0;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .price-original {
            text-decoration: line-through;
            color: #999;
            font-size: 0.9rem;
        }

        .price-final {
            font-size: 1.3rem;
            font-weight: bold;
            color: #1A7A48;
        }

        .regime-card-footer {
            padding: 1rem 1.5rem;
            background: #f9f9f9;
            display: flex;
            gap: 0.5rem;
            border-top: 1px solid #e0e0e0;
        }

        .btn {
            flex: 1;
            padding: 0.7rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background 0.3s ease;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #1A7A48;
            color: white;
        }

        .btn-primary:hover {
            background: #0f5a3a;
        }

        .btn-secondary {
            background: #f0f0f0;
            color: #333;
        }

        .btn-secondary:hover {
            background: #e0e0e0;
        }

        .btn-danger {
            background: #f44336;
            color: white;
        }

        .btn-danger:hover {
            background: #d32f2f;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #1A7A48;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <?= view('partials/header') ?>

    <main class="my-regimes-page">
        <div class="my-regimes-header">
            <h1>📋 Mes Régimes</h1>
            <p>Gérez vos régimes choisis et suivez votre progression</p>
        </div>

        <!-- Statistiques -->
        <?php if (!empty($totalRegimes)): ?>
            <div class="stats-container">
                <div class="stat-card">
                    <div class="number"><?= esc((string) $totalRegimes) ?></div>
                    <div class="label">Régimes Total</div>
                </div>
                <div class="stat-card">
                    <div class="number"><?= esc((string) $regimesActifs) ?></div>
                    <div class="label">En Cours</div>
                </div>
            </div>
        <?php endif; ?>

        <!-- État vide -->
        <?php if (empty($regimes)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">🍽️</div>
                <p class="empty-state-text">Vous n'avez pas encore choisi de régime</p>
                <p style="color: #999; font-size: 0.95rem;">Découvrez nos suggestions personnalisées et choisissez le régime qui vous correspond</p>
                <a href="<?= base_url('/resultat') ?>" class="empty-state-btn">Voir les Suggestions</a>
            </div>
        <?php else: ?>
            <!-- Grille des régimes -->
            <div class="regimes-grid">
                <?php foreach ($regimes as $regime): ?>
                    <div class="regime-card">
                        <!-- En-tête -->
                        <div class="regime-card-header">
                            <h3><?= esc($regime['nom']) ?></h3>
                            <span class="regime-status <?= esc($regime['status']) ?>">
                                <?php
                                $statusLabels = ['actif' => '✓ Actif', 'termine' => 'Terminé', 'annule' => 'Annulé'];
                                echo $statusLabels[$regime['status']] ?? esc($regime['status']);
                                ?>
                            </span>
                        </div>

                        <!-- Corps -->
                        <div class="regime-card-body">
                            <!-- Info de base -->
                            <div class="regime-info-row">
                                <span class="regime-info-label">Durée :</span>
                                <span class="regime-info-value"><?= esc((string) $regime['duree']) ?> jours</span>
                            </div>
                            <div class="regime-info-row">
                                <span class="regime-info-label">Delta Poids :</span>
                                <span class="regime-info-value"><?= esc((string) $regime['delta_poids']) ?> kg</span>
                            </div>
                            <div class="regime-info-row">
                                <span class="regime-info-label">Choisi le :</span>
                                <span class="regime-info-value"><?= date('d/m/Y', strtotime($regime['date_achat'])) ?></span>
                            </div>

                            <?php if ($regime['status'] === 'actif'): ?>
                                <div class="regime-info-row">
                                    <span class="regime-info-label">Fin prévue :</span>
                                    <span class="regime-info-value"><?= date('d/m/Y', strtotime($regime['date_fin'])) ?></span>
                                </div>
                            <?php endif; ?>

                            <!-- Composition -->
                            <div style="margin: 1rem 0;">
                                <div style="font-size: 0.9rem; font-weight: 600; margin-bottom: 0.5rem; color: #333;">Composition</div>
                                <div class="composition-bar">
                                    <div class="composition-bar-segment viande" style="width: <?= esc((string) $regime['pct_viande']) ?>%"></div>
                                    <div class="composition-bar-segment poisson" style="width: <?= esc((string) $regime['pct_poisson']) ?>%"></div>
                                    <div class="composition-bar-segment volaille" style="width: <?= esc((string) $regime['pct_volaille']) ?>%"></div>
                                </div>
                                <div class="composition-legend">
                                    <div class="composition-item">
                                        <div class="composition-color viande"></div>
                                        <span>Viande <?= esc(number_format((float) $regime['pct_viande'], 0)) ?>%</span>
                                    </div>
                                    <div class="composition-item">
                                        <div class="composition-color poisson"></div>
                                        <span>Poisson <?= esc(number_format((float) $regime['pct_poisson'], 0)) ?>%</span>
                                    </div>
                                    <div class="composition-item">
                                        <div class="composition-color volaille"></div>
                                        <span>Volaille <?= esc(number_format((float) $regime['pct_volaille'], 0)) ?>%</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Prix -->
                            <div class="price-info">
                                <div class="price-row">
                                    <span style="color: #666;">Prix payé</span>
                                    <span class="price-final"><?= esc(number_format($regime['prix_paye'], 0)) ?> Ar</span>
                                </div>
                                <?php if ($regime['remise_appliquee']): ?>
                                    <div style="font-size: 0.8rem; color: #4caf50; margin-top: 0.3rem;">✓ Remise Gold appliquée</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Pied de page -->
                        <div class="regime-card-footer">
                            <a href="<?= base_url('/export-pdf?regime=' . esc((string) $regime['regime_id'])) ?>" class="btn btn-secondary" title="Exporter le plan en PDF">
                                📥 PDF
                            </a>
                            <?php if ($regime['status'] === 'actif'): ?>
                                <button class="btn btn-danger" onclick="cancelRegime(<?= esc((string) $regime['id']) ?>)" title="Annuler ce régime">
                                    ✕ Annuler
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Lien vers suggestions -->
            <div style="text-align: center; margin-top: 2rem;">
                <a href="<?= base_url('/resultat') ?>" style="color: #1A7A48; text-decoration: none; font-weight: 600;">
                    ← Voir d'autres suggestions
                </a>
            </div>
        <?php endif; ?>
    </main>

    <script>
        /**
         * Annuler un régime
         */
        function cancelRegime(regimeId) {
            if (!confirm('Êtes-vous sûr de vouloir annuler ce régime ?')) {
                return;
            }

            const btn = event.target;
            btn.disabled = true;
            btn.innerHTML = '<span class="loading"></span>';

            fetch(`<?= base_url('/regimes/cancel') ?>/${regimeId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Régime annulé avec succès');
                    location.reload();
                } else {
                    alert('Erreur: ' + (data.error || 'Impossible d\'annuler le régime'));
                    btn.disabled = false;
                    btn.innerHTML = '✕ Annuler';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur serveur');
                btn.disabled = false;
                btn.innerHTML = '✕ Annuler';
            });
        }
    </script>
</body>
</html>
