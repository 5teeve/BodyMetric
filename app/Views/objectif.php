<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>BodyMetric - Objectifs</title>
    <link rel="stylesheet" href="<?= base_url('css/objectif.css') ?>">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Vos Objectifs</h1>
            <p>Choisissez l'objectif que vous souhaitez suivre</p>
        </div>

        <div class="cards-grid">
            <a href="#" class="card" data-objectif="augmenter">
                <div class="card-icon">⬆️</div>
                <h2>Augmenter</h2>
                <p>Prise de masse et force</p>
            </a>

            <a href="#" class="card" data-objectif="reduire">
                <div class="card-icon">⬇️</div>
                <h2>Réduire</h2>
                <p>Perte de poids et sèche</p>
            </a>

            <a href="#" class="card" data-objectif="imc-ideal">
                <div class="card-icon">⚖️</div>
                <h2>IMC Idéal</h2>
                <p>Maintien et équilibre</p>
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                card.addEventListener('click', function(e) {
                    e.preventDefault();
                    const objectif = this.dataset.objectif;
                    console.log('Objectif choisi:', objectif);
                    // TODO: envoyer le choix au serveur (fetch POST)
                    // TODO: rediriger vers la page de suivi/adaptation
                });
            });
        });
    </script>
</body>
</html>
