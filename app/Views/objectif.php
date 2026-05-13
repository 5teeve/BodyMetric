<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>BodyMetric - Objectifs</title>
    <link rel="stylesheet" href="<?= base_url('css/global.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/objectif.css') ?>">
    <style>
        .objectif-form {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: var(--radius-lg);
            padding: var(--spacing-lg);
            margin-top: var(--spacing-xl);
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .form-section {
            margin-bottom: var(--spacing-lg);
        }

        .form-section h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: var(--spacing-md);
            color: var(--text);
        }

        .form-group {
            margin-bottom: var(--spacing-md);
        }

        .form-group label {
            display: block;
            margin-bottom: var(--spacing-sm);
            font-weight: 500;
            color: var(--text-secondary);
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--input-border);
            border-radius: var(--radius-md);
            background: var(--input-bg);
            color: var(--text);
            font-size: 15px;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--input-border-focus);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .button-group {
            display: flex;
            gap: var(--spacing-md);
            margin-top: var(--spacing-lg);
        }

        .button-group button {
            flex: 1;
            padding: 16px;
            border: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-base);
        }

        .button-group .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%);
            color: white;
        }

        .button-group .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-glow);
        }

        .button-group .btn-secondary {
            background: transparent;
            border: 1px solid var(--card-border);
            color: var(--text-secondary);
        }

        .button-group .btn-secondary:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .poids-info {
            background: rgba(16, 185, 129, 0.1);
            border-left: 4px solid var(--primary);
            padding: var(--spacing-md);
            border-radius: var(--radius-md);
            margin-top: var(--spacing-md);
            font-size: 14px;
            color: var(--text-secondary);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Vos Objectifs</h1>
            <p>Choisissez l'objectif que vous souhaitez suivre</p>
        </div>

        <!-- Formulaire pour saisir l'objectif et le poids cible -->
        <form method="POST" action="<?= base_url('/objectif/store') ?>" class="objectif-form" id="objectifForm">
            <?= csrf_field() ?>
            
            <div class="form-section">
                <h3>Définir votre objectif</h3>
                
                <div class="form-group">
                    <label for="objectif">Type d'objectif</label>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--spacing-sm);">
                        <label style="display: flex; align-items: center; margin-bottom: 0;">
                            <input type="radio" name="objectif" value="augmenter" required style="margin-right: 8px;">
                            Augmenter
                        </label>
                        <label style="display: flex; align-items: center; margin-bottom: 0;">
                            <input type="radio" name="objectif" value="reduire" required style="margin-right: 8px;">
                            Réduire
                        </label>
                        <label style="display: flex; align-items: center; margin-bottom: 0;">
                            <input type="radio" name="objectif" value="ideal" required style="margin-right: 8px;">
                            IMC Idéal
                        </label>
                    </div>
                </div>

                <div class="form-group" id="poidsObjectifGroup" style="display: none;">
                    <label for="objectif_poids">Poids objectif (kg)</label>
                    <input 
                        type="number" 
                        id="objectif_poids" 
                        name="objectif_poids" 
                        placeholder="Entrez le poids que vous visez"
                        step="0.1"
                        min="0.1"
                        max="500"
                    >
                    <div class="poids-info">
                        Entrez votre poids cible en kilogrammes. L'algorithme trouvera le meilleur régime et les activités adaptées.
                    </div>
                </div>
            </div>

            <div class="button-group">
                <button type="reset" class="btn-secondary">Réinitialiser</button>
                <button type="submit" class="btn-primary">Voir les résultats</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const objectifForm = document.getElementById('objectifForm');
            const poidsObjectifGroup = document.getElementById('poidsObjectifGroup');

            // Gestion de l'affichage du champ poids objectif
            const objectifRadios = document.querySelectorAll('input[name="objectif"]');
            objectifRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'ideal') {
                        poidsObjectifGroup.style.display = 'none';
                        document.getElementById('objectif_poids').value = '';
                    } else {
                        poidsObjectifGroup.style.display = 'block';
                    }
                });
            });

            // Validation du formulaire
            objectifForm.addEventListener('submit', function(e) {
                const objectif = document.querySelector('input[name="objectif"]:checked');
                
                if (!objectif) {
                    e.preventDefault();
                    alert('Veuillez sélectionner un type d\'objectif');
                    return;
                }

                if (objectif.value !== 'ideal') {
                    const poidsInput = document.getElementById('objectif_poids');
                    if (!poidsInput.value || parseFloat(poidsInput.value) <= 0) {
                        e.preventDefault();
                        alert('Veuillez entrer un poids objectif valide');
                        return;
                    }
                }
            });
        });
    </script>
</body>
</html>
