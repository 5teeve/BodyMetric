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
        $userId = (int) session()->get('user_id');

        if ($userId <= 0) {
            return redirect()->to('/connexion');
        }

        $user = $this->userModel->getById($userId);

        if (!$user) {
            return redirect()->to('/')->with('error', 'Utilisateur non trouvé');
        }

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
        ]);

        $mpdf->SetTitle('Résumé BodyMetric - ' . $user['prenom'] . ' ' . $user['nom']);
        $mpdf->SetAuthor('BodyMetric');
        $mpdf->SetCreator('BodyMetric');

        $this->addContent($mpdf, $user);

        $filename = 'bodymetric_resume_' . date('Y-m-d') . '.pdf';

        $mpdf->Output($filename, 'D');
        exit();
    }

    private function addContent(Mpdf $mpdf, array $user): void
    {
        $imc = $user['imc'] ?? null;
        $imcLabel = $this->getImcLabel($imc);
        $imcColorRgb = $this->getImcColorRgb($imc);

        $genre = $user['genre'] === 'M' ? 'Masculin' : ($user['genre'] === 'F' ? 'Féminin' : 'Autre');

        // Titre
        $mpdf->SetFont('', 'B', 20);
        $mpdf->Cell(0, 15, 'Résumé BodyMetric', 0, 1, 'C');
        $mpdf->Ln(5);

        // Section Informations Personnelles
        $mpdf->SetFillColor(230, 230, 230);
        $mpdf->Rect(15, $mpdf->GetY(), 180, 10, 'F');
        $mpdf->SetFont('', 'B', 14);
        $mpdf->Cell(0, 10, 'Informations Personnelles', 0, 1, 'L');
        $mpdf->Ln(2);

        $mpdf->SetFont('', '', 11);
        $mpdf->Cell(60, 8, 'Nom:', 0, 0);
        $mpdf->Cell(0, 8, $user['nom'] . ' ' . $user['prenom'], 0, 1);

        $mpdf->Cell(60, 8, 'Email:', 0, 0);
        $mpdf->Cell(0, 8, $user['email'], 0, 1);

        $mpdf->Cell(60, 8, 'Genre:', 0, 0);
        $mpdf->Cell(0, 8, $genre, 0, 1);
        $mpdf->Ln(5);

        // Section Données de Santé
        $mpdf->SetFillColor(230, 230, 230);
        $mpdf->Rect(15, $mpdf->GetY(), 180, 10, 'F');
        $mpdf->SetFont('', 'B', 14);
        $mpdf->Cell(0, 10, 'Données de Santé', 0, 1, 'L');
        $mpdf->Ln(2);

        $mpdf->SetFont('', '', 11);
        $mpdf->Cell(60, 8, 'Taille:', 0, 0);
        $mpdf->Cell(0, 8, ($user['taille'] ? $user['taille'] . ' cm' : 'Non renseigné'), 0, 1);

        $mpdf->Cell(60, 8, 'Poids:', 0, 0);
        $mpdf->Cell(0, 8, ($user['poids'] ? $user['poids'] . ' kg' : 'Non renseigné'), 0, 1);

        $mpdf->SetFont('', 'B', 11);
        $mpdf->Cell(60, 8, 'IMC:', 0, 0);

        $imcText = ($imc ? number_format($imc, 2) : 'Non calculé') . ' — ' . $imcLabel;
        $mpdf->SetTextColor($imcColorRgb[0], $imcColorRgb[1], $imcColorRgb[2]);
        $mpdf->Cell(0, 8, $imcText, 0, 1);
        $mpdf->SetTextColor(0, 0, 0);
        $mpdf->Ln(5);

        // Section Régime et Objectifs
        $mpdf->SetFillColor(230, 230, 230);
        $mpdf->Rect(15, $mpdf->GetY(), 180, 10, 'F');
        $mpdf->SetFont('', 'B', 14);
        $mpdf->Cell(0, 10, 'Régime et Objectifs', 0, 1, 'L');
        $mpdf->Ln(2);

        $mpdf->SetFont('', '', 11);
        $mpdf->MultiCell(0, 8, 'Selon votre profil et vos objectifs, BodyMetric vous propose un régime personnalisé pour atteindre vos buts de santé.', 0, 'L');
        $mpdf->Ln(5);

        // Section Activités Recommandées
        $mpdf->SetFillColor(230, 230, 230);
        $mpdf->Rect(15, $mpdf->GetY(), 180, 10, 'F');
        $mpdf->SetFont('', 'B', 14);
        $mpdf->Cell(0, 10, 'Activités Recommandées', 0, 1, 'L');
        $mpdf->Ln(2);

        $activities = [
            'Marche rapide (30 min/jour)',
            'Natation (2x par semaine)',
            'Renforcement musculaire (3x par semaine)',
            'Yoga ou étirements (pour la récupération)',
        ];

        $mpdf->SetFont('', '', 11);
        foreach ($activities as $activity) {
            $mpdf->Cell(10, 8, chr(149), 0, 0, 'C');
            $mpdf->Cell(0, 8, $activity, 0, 1);
        }
        $mpdf->Ln(5);

        // Footer
        $mpdf->SetFont('', 'I', 9);
        $mpdf->Cell(0, 10, 'Document généré le ' . date('d/m/Y à H:i'), 0, 1, 'C');
        $mpdf->Cell(0, 5, 'BodyMetric - Votre compagnon santé', 0, 1, 'C');
    }

    private function getImcLabel($imc): string
    {
        if (!is_numeric($imc)) {
            return 'Non calculé';
        }

        if ($imc < 18.5) {
            return 'Maigreur';
        }

        if ($imc < 25) {
            return 'Normal';
        }

        if ($imc < 30) {
            return 'Surpoids';
        }

        return 'Obésité';
    }

    private function getImcColorRgb($imc): array
    {
        if (!is_numeric($imc)) {
            return [128, 128, 128];
        }

        if ($imc < 18.5) {
            return [255, 165, 0];
        }

        if ($imc < 25) {
            return [0, 128, 0];
        }

        if ($imc < 30) {
            return [255, 140, 0];
        }

        return [220, 20, 60];
    }
}
