<?php

namespace App\Controllers\Bo;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        if (!$this->isAdminUser()) {
            return redirect()->to('/connexion');
        }

        $db = \Config\Database::connect();

        $usersRow = $db->query('SELECT COUNT(*) AS total FROM users')->getRowArray();
        $usersTotal = (int) ($usersRow['total'] ?? 0);

        $goldRow = $db->query('SELECT COUNT(*) AS total FROM users WHERE is_gold = 1')->getRowArray();
        $goldMembers = (int) ($goldRow['total'] ?? 0);

        $codesTotalRow = $db->query('SELECT COUNT(*) AS total FROM codes')->getRowArray();
        $codesTotal = (int) ($codesTotalRow['total'] ?? 0);

        $codesRow = $db->query("SELECT COUNT(*) AS total, COALESCE(SUM(montant), 0) AS ca FROM codes WHERE statut = 'utilise'")
            ->getRowArray();
        $codesUsed = (int) ($codesRow['total'] ?? 0);
        $caTotal = (float) ($codesRow['ca'] ?? 0);

        $regimesSoldRow = $db->query('SELECT COUNT(*) AS total FROM regimes_achetes')->getRowArray();
        $regimesSold = (int) ($regimesSoldRow['total'] ?? 0);

        $topRegimes = $db->query(
            "SELECT r.nom, COUNT(*) AS total, COALESCE(SUM(ra.prix_paye), 0) AS ca
             FROM regimes_achetes ra
             JOIN regimes r ON r.id = ra.regime_id
             GROUP BY r.id, r.nom
             ORDER BY total DESC
             LIMIT 5"
        )->getResultArray();

        $monthlyData = $this->getMonthlyRegistrations($db);
        $objectivesData = $this->getObjectivesDistribution($db);

        return view('bo/dashboard', [
            'usersTotal' => $usersTotal,
            'goldMembers' => $goldMembers,
            'codesTotal' => $codesTotal,
            'codesUsed' => $codesUsed,
            'caTotal' => $caTotal,
            'regimesSold' => $regimesSold,
            'topRegimes' => $topRegimes,
            'monthlyLabels' => $monthlyData['labels'],
            'monthlyData' => $monthlyData['data'],
            'objectivesLabels' => $objectivesData['labels'],
            'objectivesData' => $objectivesData['data'],
            'objectivesColors' => $objectivesData['colors'],
            'isAdmin' => $this->isAdminUser(),
            'isConnected' => $this->isUserConnected(),
        ]);
    }

    private function getMonthlyRegistrations($db): array
    {
        $query = "
            SELECT
                DATE_FORMAT(created_at, '%Y-%m') AS month,
                COUNT(*) AS count
            FROM users
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC
        ";

        $results = $db->query($query)->getResultArray();

        $labels = [];
        $data = [];

        foreach ($results as $row) {
            $monthLabel = $this->formatMonthLabel($row['month']);
            $labels[] = $monthLabel;
            $data[] = (int) $row['count'];
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    private function formatMonthLabel(string $month): string
    {
        $months = [
            '01' => 'Jan', '02' => 'Fév', '03' => 'Mar', '04' => 'Avr',
            '05' => 'Mai', '06' => 'Juin', '07' => 'Juil', '08' => 'Août',
            '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Déc',
        ];

        $parts = explode('-', $month);
        $year = $parts[0] ?? '';
        $monthNum = $parts[1] ?? '';

        return ($months[$monthNum] ?? $monthNum) . ' ' . substr($year, 2);
    }

    private function getObjectivesDistribution($db): array
    {
        $query = "
            SELECT
                COALESCE(objectif, 'non_defini') AS objectif,
                COUNT(*) AS count
            FROM users
            GROUP BY objectif
            ORDER BY count DESC
        ";

        $results = $db->query($query)->getResultArray();

        $labels = [];
        $data = [];
        $colors = [];

        $colorMap = [
            'reduire' => '#f59e0b',    // Orange
            'augmenter' => '#3b82f6',  // Blue
            'maintenir' => '#22c55e',  // Green
            'ideal' => '#22c55e',      // Green
            'imc-ideal' => '#22c55e',  // Green
            'idc' => '#8b5cf6',        // Violet
            'non_defini' => '#94a3b8', // Gray
        ];

        $labelMap = [
            'reduire' => 'Réduire le poids',
            'augmenter' => 'Augmenter le poids',
            'maintenir' => 'Maintenir le poids',
            'ideal' => 'IMC idéal',
            'imc-ideal' => 'IMC idéal',
            'idc' => 'IMC idéal',
            'non_defini' => 'Non défini',
        ];

        foreach ($results as $row) {
            $objectif = $row['objectif'];
            $labels[] = $labelMap[$objectif] ?? $objectif;
            $data[] = (int) $row['count'];
            $colors[] = $colorMap[$objectif] ?? '#94a3b8';
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors,
        ];
    }
}
