<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\RegimesAchetesModel;
use Mpdf\Mpdf;

class ExportPdfController extends BaseController
{
    protected $userModel;
    protected $regimesAchetesModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->regimesAchetesModel = new RegimesAchetesModel();
    }

    public function generate($regimeId = null)
    {
        $session = session();

        // Require authentication
        if (!$session->has('user_id')) {
            return redirect()->to('/connexion');
        }
        $user = $this->userModel->find($session->get('user_id'));

        if (!$user) {
            return redirect()->to('/')->with('error', 'Utilisateur non trouvé');
        }

        // Vérifier si un régime spécifique est demandé (via paramètre GET ou route)
        if ($regimeId === null) {
            $regimeId = $this->request->getGet('regime');
        }
        if ($regimeId) {
            return $this->generateRegimePdf($user, (int)$regimeId);
        }

        $mpdf = new Mpdf([
            'mode'          => 'utf-8',
            'format'        => 'A4',
            'margin_left'   => 15,
            'margin_right'  => 15,
            'margin_top'    => 15,
            'margin_bottom' => 15,
        ]);

        $mpdf->SetTitle('Résumé BodyMetric - ' . $user['prenom'] . ' ' . $user['nom']);
        $mpdf->SetAuthor('BodyMetric');
        $mpdf->SetCreator('BodyMetric');

        $html = $this->buildHtml($user);
        $mpdf->WriteHTML($html);

        $filename = 'bodymetric_resume_' . date('Y-m-d') . '.pdf';
        $mpdf->Output($filename, 'D');
        exit();
    }

    private function generateRegimePdf(array $user, int $regimeId)
    {
        // Vérifier que l'utilisateur possède ce régime
        $regimeAchete = $this->regimesAchetesModel->getById($regimeId);

        if (!$regimeAchete || (int)$regimeAchete['user_id'] !== (int)$user['id']) {
            return redirect()->to('/mes-regimes')->with('error', 'Régime non trouvé ou accès non autorisé');
        }

        // Récupérer les détails complets du régime
        $regimeDetails = $this->regimesAchetesModel->select('regimes_achetes.*, regimes.nom, regimes.pct_viande, regimes.pct_poisson, regimes.pct_volaille, regimes.duree, regimes.prix as prix_original, regimes.delta_poids')
            ->join('regimes', 'regimes.id = regimes_achetes.regime_id')
            ->where('regimes_achetes.id', $regimeId)
            ->first();

        if (!$regimeDetails) {
            return redirect()->to('/mes-regimes')->with('error', 'Détails du régime non trouvés');
        }

        $mpdf = new Mpdf([
            'mode'          => 'utf-8',
            'format'        => 'A4',
            'margin_left'   => 15,
            'margin_right'  => 15,
            'margin_top'    => 15,
            'margin_bottom' => 15,
        ]);

        $mpdf->SetTitle('Plan Régime - ' . $regimeDetails['nom']);
        $mpdf->SetAuthor('BodyMetric');
        $mpdf->SetCreator('BodyMetric');

        $html = $this->buildRegimeHtml($user, $regimeDetails);
        $mpdf->WriteHTML($html);

        $filename = 'regime_' . $regimeDetails['nom'] . '_' . date('Y-m-d') . '.pdf';
        $mpdf->Output($filename, 'D');
        exit();
    }

    private function buildHtml(array $user): string
    {
        $imc      = $user['imc'] ?? null;
        $imcLabel = $this->getImcLabel($imc);
        $imcColor = $this->getImcColor($imc);
        $imcText  = ($imc ? number_format((float)$imc, 2) : 'Non calculé') . ' — ' . $imcLabel;
        $genre    = $user['genre'] === 'M' ? 'Masculin' : ($user['genre'] === 'F' ? 'Féminin' : 'Autre');
        $taille   = $user['taille'] ? $user['taille'] . ' cm' : 'Non renseigné';
        $poids    = $user['poids'] ? $user['poids'] . ' kg'  : 'Non renseigné';
        $date     = date('d/m/Y à H:i');

        return <<<HTML
        <style>
            body        { font-family: Arial, sans-serif; font-size: 11pt; color: #1f2937; }
            h1          { text-align: center; font-size: 22pt; color: #166534; margin-bottom: 20px; }
            .section-title {
                background: #e5e7eb;
                padding: 6px 10px;
                font-size: 13pt;
                font-weight: bold;
                margin-top: 20px;
                margin-bottom: 8px;
                border-left: 4px solid #22c55e;
            }
            table       { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
            td          { padding: 6px 8px; font-size: 11pt; }
            td.label    { width: 40%; font-weight: bold; color: #374151; }
            td.value    { color: #4b5563; }
            .imc-value  { font-weight: bold; color: {$imcColor}; }
            ul          { margin: 0; padding-left: 20px; }
            li          { margin-bottom: 6px; font-size: 11pt; }
            .footer     { text-align: center; font-size: 9pt; color: #9ca3af; margin-top: 30px; font-style: italic; }
        </style>

        <h1>Résumé BodyMetric</h1>

        <div class="section-title">Informations Personnelles</div>
        <table>
            <tr><td class="label">Nom :</td><td class="value">{$user['nom']} {$user['prenom']}</td></tr>
            <tr><td class="label">Email :</td><td class="value">{$user['email']}</td></tr>
            <tr><td class="label">Genre :</td><td class="value">{$genre}</td></tr>
        </table>

        <div class="section-title">Données de Santé</div>
        <table>
            <tr><td class="label">Taille :</td><td class="value">{$taille}</td></tr>
            <tr><td class="label">Poids :</td><td class="value">{$poids}</td></tr>
            <tr><td class="label">IMC :</td><td class="value"><span class="imc-value">{$imcText}</span></td></tr>
        </table>

        <div class="section-title">Régime et Objectifs</div>
        <p>Selon votre profil et vos objectifs, BodyMetric vous propose un régime personnalisé pour atteindre vos buts de santé.</p>

        <div class="section-title">Activités Recommandées</div>
        <ul>
            <li>Marche rapide (30 min/jour)</li>
            <li>Natation (2x par semaine)</li>
            <li>Renforcement musculaire (3x par semaine)</li>
            <li>Yoga ou étirements (pour la récupération)</li>
        </ul>

        <div class="footer">
            Document généré le {$date}<br>
            BodyMetric — Votre compagnon santé
        </div>
        HTML;
    }

    private function buildRegimeHtml(array $user, array $regime): string
    {
        $date = date('d/m/Y à H:i');
        $dateAchat = date('d/m/Y', strtotime($regime['date_achat']));
        $dateFin = date('d/m/Y', strtotime($regime['date_fin']));
        $prixPaye = number_format((float)$regime['prix_paye'], 2, '.', ' ') . ' Ar';
        $prixOriginal = number_format((float)$regime['prix_original'], 2, '.', ' ') . ' Ar';

        $pctViande = (float)$regime['pct_viande'];
        $pctPoisson = (float)$regime['pct_poisson'];
        $pctVolaille = (float)$regime['pct_volaille'];

        $status = $regime['status'] === 'actif' ? 'Actif' : ($regime['status'] === 'termine' ? 'Terminé' : 'Annulé');
        $statusColor = $regime['status'] === 'actif' ? '#16a34a' : ($regime['status'] === 'termine' ? '#d97706' : '#dc2626');

        return <<<HTML
        <style>
            body        { font-family: Arial, sans-serif; font-size: 11pt; color: #1f2937; }
            h1          { text-align: center; font-size: 22pt; color: #166534; margin-bottom: 20px; }
            h2          { font-size: 16pt; color: #166534; margin-top: 25px; margin-bottom: 10px; }
            .section-title {
                background: #e5e7eb;
                padding: 6px 10px;
                font-size: 13pt;
                font-weight: bold;
                margin-top: 20px;
                margin-bottom: 8px;
                border-left: 4px solid #22c55e;
            }
            table       { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
            td          { padding: 6px 8px; font-size: 11pt; }
            td.label    { width: 40%; font-weight: bold; color: #374151; }
            td.value    { color: #4b5563; }
            .status     { font-weight: bold; color: {$statusColor}; }
            .composition-table {
                width: 100%;
                margin: 10px 0;
                border-collapse: collapse;
            }
            .composition-table td {
                height: 30px;
                padding: 0;
                border: 1px solid #d1d5db;
                text-align: center;
                font-weight: bold;
                font-size: 10pt;
                color: #fff;
            }
            .viande { background: #d4a574; }
            .poisson { background: #4a90e2; }
            .volaille { background: #f5a623; }
            .legend-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 8px;
            }
            .legend-table td {
                padding: 6px 8px;
                text-align: center;
                font-size: 10pt;
                color: #6b7280;
                border: none;
            }
            .footer     { text-align: center; font-size: 9pt; color: #9ca3af; margin-top: 30px; font-style: italic; }
        </style>

        <h1>Plan Régime BodyMetric</h1>

        <div class="section-title">Informations du Régime</div>
        <table>
            <tr><td class="label">Nom du régime :</td><td class="value">{$regime['nom']}</td></tr>
            <tr><td class="label">Statut :</td><td class="value"><span class="status">{$status}</span></td></tr>
            <tr><td class="label">Date d'achat :</td><td class="value">{$dateAchat}</td></tr>
            <tr><td class="label">Date de fin :</td><td class="value">{$dateFin}</td></tr>
            <tr><td class="label">Durée :</td><td class="value">{$regime['duree']} jours</td></tr>
        </table>

        <div class="section-title">Prix et Paiement</div>
        <table>
            <tr><td class="label">Prix payé :</td><td class="value">{$prixPaye}</td></tr>
            <tr><td class="label">Prix original :</td><td class="value">{$prixOriginal}</td></tr>
        </table>

        <div class="section-title">Composition Alimentaire</div>
        <table class="composition-table">
            <tr>
                <td class="viande" style="width: {$pctViande}%">Viande {$pctViande}%</td>
                <td class="poisson" style="width: {$pctPoisson}%">Poisson {$pctPoisson}%</td>
                <td class="volaille" style="width: {$pctVolaille}%">Volaille {$pctVolaille}%</td>
            </tr>
        </table>
        <table class="legend-table">
            <tr>
                <td>Viande: {$pctViande}%</td>
                <td>Poisson: {$pctPoisson}%</td>
                <td>Volaille: {$pctVolaille}%</td>
            </tr>
        </table>

        <div class="section-title">Objectif du Régime</div>
        <table>
            <tr><td class="label">Delta poids ciblé :</td><td class="value">{$regime['delta_poids']} kg</td></tr>
        </table>

        <h2>Recommandations BodyMetric</h2>
        <p>Suivez ce régime pendant {$regime['duree']} jours pour atteindre votre objectif de {$regime['delta_poids']} kg. Combinez ce régime avec des activités physiques régulières pour optimiser vos résultats.</p>

        <div class="section-title">Activités Recommandées</div>
        <ul>
            <li>Marche rapide (30 min/jour)</li>
            <li>Renforcement musculaire (3x par semaine)</li>
            <li>Activités cardio (2x par semaine)</li>
            <li>Yoga ou étirements (pour la récupération)</li>
        </ul>

        <div class="footer">
            Document généré le {$date}<br>
            BodyMetric — Votre compagnon santé<br>
            Propriétaire: {$user['prenom']} {$user['nom']}
        </div>
        HTML;
    }

    private function getImcLabel($imc): string
    {
        if (!is_numeric($imc)) return 'Non calculé';
        if ($imc < 18.5)       return 'Maigreur';
        if ($imc < 25)         return 'Normal';
        if ($imc < 30)         return 'Surpoids';
        return 'Obésité';
    }

    private function getImcColor($imc): string
    {
        if (!is_numeric($imc)) return '#6b7280';
        if ($imc < 18.5)       return '#f97316';
        if ($imc < 25)         return '#16a34a';
        if ($imc < 30)         return '#d97706';
        return '#dc2626';
    }
}
