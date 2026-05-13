<?php

namespace App\Controllers;

use App\Models\User;
use Mpdf\Mpdf;

class ExportPdfController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function generate()
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
