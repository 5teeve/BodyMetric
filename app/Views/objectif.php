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
        <div class="chart-wrapper" style="max-width:600px;margin:40px auto;">
            <canvas id="objectifChart" width="600" height="400"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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

        // Fetch distribution data and render a pie chart
        (function renderObjectifChart() {
            const url = '<?= base_url('/api/objectifs/distribution') ?>';
            fetch(url)
                .then(res => res.json())
                .then(json => {
                    const ctx = document.getElementById('objectifChart').getContext('2d');
                    const labels = json.labels || [];
                    const data = json.data || [];
                    const colors = [
                        '#4dc9f6', '#f67019', '#f53794', '#537bc4', '#acc236', '#166a8f'
                    ];

                    new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: data,
                                backgroundColor: colors.slice(0, labels.length),
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { position: 'bottom' }
                            }
                        }
                    });
                })
                .catch(err => console.error('Erreur récupération distribution objectifs:', err));
        })();
    </script>
</body>
</html>
