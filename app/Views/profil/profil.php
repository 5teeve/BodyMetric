<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="stylesheet" href="<?= base_url('css/profil.css') ?>">
</head>

<body>

    <div class="page">

        <div class="page-title">Mon profil</div>
        <div class="page-sub">
            Vos informations personnelles et de santé.
        </div>

        <div class="row">

            <div class="col">
                <div class="card">

                    <div class="card-title">
                        Informations personnelles
                    </div>

                    <div class="field">
                        <label>Nom complet</label>
                        <input type="text" value="Jean Dupont">
                    </div>

                    <div class="field">
                        <label>Adresse email</label>
                        <input type="email" value="jean.dupont@email.com">
                    </div>

                    <div class="field">
                        <label>Genre</label>
                        <select>
                            <option selected>Homme</option>
                            <option>Femme</option>
                        </select>
                    </div>

                    <div class="field">
                        <label>Date de naissance</label>
                        <input type="date" value="1995-03-14">
                    </div>

                    <button class="btn-main">
                        Enregistrer les modifications
                    </button>

                </div>
            </div>

            <div class="col">
                <div class="card">

                    <div class="card-title">
                        Données de santé
                    </div>

                    <div class="row2">

                        <div class="field">
                            <label>Taille (cm)</label>
                            <input type="number" value="175">
                        </div>

                        <div class="field">
                            <label>Poids actuel (kg)</label>
                            <input type="number" value="70">
                        </div>

                    </div>

                    <div class="field">
                        <label>Niveau d'activité</label>
                        <select>
                            <option>
                                Modérément actif (3–5 jours/sem.)
                            </option>
                        </select>
                    </div>

                    <div class="alert">

                        <div class="small">
                            IMC calculé
                        </div>

                        <div class="imc">
                            22.4 <span>— Poids normal</span>
                        </div>

                    </div>

                    <button class="btn-main">
                        Mettre à jour
                    </button>

                </div>
            </div>

        </div>

    </div>

</body>

</html>