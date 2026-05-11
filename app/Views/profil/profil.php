    <?php
    $nom = $user['nom'] ?? '';
    $prenom = $user['prenom'] ?? '';
    $email = $user['email'] ?? '';
    $genre = $user['genre'] ?? '';
    $taille = $user['taille'] ?? '';
    $poids = $user['poids'] ?? '';
    $wallet = $user['wallet'] ?? null;
    $isGold = $user['is_gold'] ?? null;
    $imc = $user['imc'] ?? null;
    $imcLabel = $imcLabel ?? 'Non calcule';

    $missingPersonal = empty($nom) || empty($prenom) || empty($email) || empty($genre);
    $missingHealth = empty($taille) || empty($poids);

    $personalDisabled = 'disabled';
    $healthDisabled = 'disabled';
    $isGoldActive = ($isGold === 1 || $isGold === '1');
    $goldPrice = $goldPrice ?? 100000;
    ?>

    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Profil</title>
        <link rel="stylesheet" href="<?= base_url('css/header.css') ?>">
        <link rel="stylesheet" href="<?= base_url('css/profil.css') ?>">
    </head>

    <body>
        <?= view('partials/header') ?>


        <div class="page">

            <div class="page-title">Mon profil</div>
            <div class="page-sub">
                Vos informations personnelles et de santé.
            </div>

            <?php if ($missingPersonal || $missingHealth): ?>
                <div class="alert alert-danger">
                    Certaines informations sont manquantes. Merci de completer votre profil.
                </div>
            <?php endif; ?>

            <div id="profileAlert" class="alert" hidden></div>

            <div class="row">

                <div class="col">
                    <div class="card">

                        <div class="card-title">
                            Informations personnelles
                        </div>

                        <div class="wallet-card">
                            <div class="wallet-title">Wallet</div>
                            <div class="wallet-value">
                                <span id="walletAmount"><?= esc(is_null($wallet) ? '-' : number_format((float) $wallet, 2, '.', ' ')) ?></span> <span>AR</span>
                            </div>
                            <div class="wallet-meta">
                                Statut: <span id="walletStatus" class="<?= $isGoldActive ? 'gold' : 'standard' ?>">
                                    <?= $isGoldActive ? 'Gold' : 'Standard' ?>
                                </span>
                            </div>

                            <?php if (!$isGoldActive): ?>
                                <form method="post" action="<?= base_url('profil/gold-ajax') ?>" data-ajax="gold" class="gold-form">
                                    <?= csrf_field() ?>
                                    <div class="gold-note">
                                        Passer en Gold (paiement unique)
                                    </div>
                                    <button class="btn-main gold-cta" type="submit" data-gold-price="<?= esc((string) $goldPrice) ?>">
                                        Passer Gold - <?= esc(number_format((float) $goldPrice, 2, '.', ' ')) ?> Ar
                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="gold-note is-active">Gold actif - paiement deja effectue</div>
                            <?php endif; ?>

                            <a href="<?= base_url('export-pdf') ?>" class="btn-secondary" style="display:block; margin-top:15px; text-align:center;">
                                Exporter mon résumé PDF
                            </a>
                        </div>

                        <form method="post" action="<?= base_url('profil/perso-ajax') ?>" data-ajax="profile" data-section="personal">
                            <?= csrf_field() ?>

                            <div class="field">
                                <label>Nom</label>
                                <input type="text" name="nom" class="input-field js-editable" value="<?= esc($nom) ?>" <?= $personalDisabled ?>>
                            </div>

                            <div class="field">
                                <label>Prenom</label>
                                <input type="text" name="prenom" class="input-field js-editable" value="<?= esc($prenom) ?>" <?= $personalDisabled ?>>
                            </div>

                            <div class="field">
                                <label>Adresse email</label>
                                <input type="email" name="email" class="input-field js-editable" value="<?= esc($email) ?>" <?= $personalDisabled ?>>
                            </div>

                            <div class="field">
                                <label>Genre</label>
                                <select name="genre" class="js-editable" <?= $personalDisabled ?>>
                                    <option value="" <?= ($genre === '') ? 'selected' : '' ?> disabled>Selectionner</option>
                                    <option value="M" <?= ($genre === 'M') ? 'selected' : '' ?>>Homme</option>
                                    <option value="F" <?= ($genre === 'F') ? 'selected' : '' ?>>Femme</option>
                                </select>
                            </div>

                            <div class="action-row">
                                <button class="btn-secondary js-edit-btn" type="button">Modifier</button>
                                <button class="btn-main js-submit" type="submit" <?= $personalDisabled ?>>Enregistrer</button>
                            </div>
                        </form>

                    </div>
                </div>

                <div class="col">
                    <div class="card">

                        <div class="card-title">
                            Données de santé
                        </div>

                        <form id="form2" method="post" action="<?= base_url('profil/sante-ajax') ?>" data-ajax="profile" data-section="health">
                            <?= csrf_field() ?>

                            <div class="row2">

                                <div class="field">
                                    <label>Taille (cm)</label>
                                    <input type="number" name="taille" class="input-field js-editable" value="<?= esc($taille) ?>" <?= $healthDisabled ?>>
                                </div>

                                <div class="field">
                                    <label>Poids actuel (kg)</label>
                                    <input type="number" name="poids" class="input-field js-editable" value="<?= esc($poids) ?>" <?= $healthDisabled ?>>
                                </div>

                            </div>

                            <div class="alert">

                                <div class="small bmi-label">
                                    IMC calculé
                                </div>

                                <div class="imc">
                                    <span id="bmiValue"><?= esc($imc ?? '-') ?></span> <span id="bmiLabelText">— <?= esc($imcLabel) ?></span>
                                </div>

                            </div>

                            <div class="action-row">
                                <button class="btn-secondary js-edit-btn" type="button">Modifier</button>
                                <button class="btn-main js-submit" type="submit" <?= $healthDisabled ?>>Enregistrer</button>
                            </div>
                        </form>

                    </div>
                </div>

            </div>

        </div>

    </body>

    <script src="<?= base_url('js/register_step2.js') ?>"></script>
    <script src="<?= base_url('js/profil.js') ?>"></script>

    </html>