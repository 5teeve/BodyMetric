<?php
$regime = $regime ?? null;
$isEditing = $isEditing ?? false;
$activites = $activites ?? [];
$selectedActivites = $selectedActivites ?? [];
$regimeId = is_array($regime) ? (int) ($regime['id'] ?? 0) : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEditing ? 'Modifier' : 'Créer' ?> un régime - BO</title>
    <link rel="stylesheet" href="<?= base_url('css/header.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/bo_dashboard.css') ?>">
    <style>
        .bo-content {
            margin-left: 260px;
            padding: 2rem;
            min-height: 100vh;
            background: #f1f5f9;
            width: calc(100% - 260px);
            box-sizing: border-box;
        }
        .form-card {
            max-width: 600px;
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .form-header {
            margin-bottom: 1.5rem;
        }
        .form-header h1 {
            margin: 0;
            font-size: 1.5rem;
            color: #1f2937;
        }
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 1rem;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .form-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }
        .composition-hint {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.5rem;
        }
        .composition-hint.error {
            color: #ef4444;
        }
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
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
        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-secondary:hover { background: #e5e7eb; }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        @media (max-width: 768px) {
            .bo-content {
                margin-left: 0;
                width: 100%;
            }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <?= view('partials/sidebar_bo') ?>

    <main class="bo-content">
        <div class="form-card">
            <div class="form-header">
                <h1><?= $isEditing ? 'Modifier le régime' : 'Nouveau régime' ?></h1>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-error"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-error">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <p><?= $error ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="<?= $isEditing ? base_url('bo/regimes/update/' . $regimeId) : base_url('bo/regimes/form') ?>" method="post" id="regimeForm">
                <div class="form-group">
                    <label for="nom">Nom du régime *</label>
                    <input type="text" id="nom" name="nom" value="<?= old('nom', $regime['nom'] ?? '') ?>" required maxlength="100">
                </div>

                <div class="form-group">
                    <label>Composition (%)</label>
                    <div class="form-row">
                        <div>
                            <label for="pct_viande" style="font-weight: normal; font-size: 0.875rem;">Viande</label>
                            <input type="number" id="pct_viande" name="pct_viande" value="<?= old('pct_viande', $regime['pct_viande'] ?? '') ?>" required min="0" max="100" step="0.01">
                        </div>
                        <div>
                            <label for="pct_poisson" style="font-weight: normal; font-size: 0.875rem;">Poisson</label>
                            <input type="number" id="pct_poisson" name="pct_poisson" value="<?= old('pct_poisson', $regime['pct_poisson'] ?? '') ?>" required min="0" max="100" step="0.01">
                        </div>
                        <div>
                            <label for="pct_volaille" style="font-weight: normal; font-size: 0.875rem;">Volaille</label>
                            <input type="number" id="pct_volaille" name="pct_volaille" value="<?= old('pct_volaille', $regime['pct_volaille'] ?? '') ?>" required min="0" max="100" step="0.01">
                        </div>
                    </div>
                    <p class="composition-hint" id="compositionHint">La somme doit être égale à 100%</p>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="duree">Durée (jours) *</label>
                        <input type="number" id="duree" name="duree" value="<?= old('duree', $regime['duree'] ?? '') ?>" required min="1">
                    </div>
                    <div class="form-group">
                        <label for="prix">Prix (Ar) *</label>
                        <input type="number" id="prix" name="prix" value="<?= old('prix', $regime['prix'] ?? '') ?>" required min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="delta_poids">Delta poids (kg)</label>
                        <input type="number" id="delta_poids" name="delta_poids" value="<?= old('delta_poids', $regime['delta_poids'] ?? '') ?>" step="0.01">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary"><?= $isEditing ? 'Mettre à jour' : 'Créer' ?></button>
                    <a href="<?= base_url('bo/regimes') ?>" class="btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </main>

    <script>
        // Validation composition en temps réel
        const viande = document.getElementById('pct_viande');
        const poisson = document.getElementById('pct_poisson');
        const volaille = document.getElementById('pct_volaille');
        const hint = document.getElementById('compositionHint');

        function checkComposition() {
            const sum = parseFloat(viande.value || 0) + parseFloat(poisson.value || 0) + parseFloat(volaille.value || 0);
            if (Math.abs(sum - 100) > 0.01) {
                hint.textContent = `Somme actuelle: ${sum.toFixed(2)}% (doit être 100%)`;
                hint.classList.add('error');
                return false;
            } else {
                hint.textContent = '✓ Somme = 100%';
                hint.classList.remove('error');
                return true;
            }
        }

        [viande, poisson, volaille].forEach(input => {
            input.addEventListener('input', checkComposition);
        });

        document.getElementById('regimeForm').addEventListener('submit', function(e) {
            if (!checkComposition()) {
                e.preventDefault();
                alert('La somme des pourcentages doit être égale à 100%');
            }
        });

        checkComposition();
    </script>
</body>
</html>
