<?php

namespace App\Controllers\Bo;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index(): string
    {
        $db = \Config\Database::connect();

        $usersRow = $db->query('SELECT COUNT(*) AS total FROM users')->getRowArray();
        $usersTotal = (int) ($usersRow['total'] ?? 0);

        $codesRow = $db->query("SELECT COUNT(*) AS total, COALESCE(SUM(montant), 0) AS ca FROM codes WHERE statut = 'utilise'")
            ->getRowArray();
        $codesUsed = (int) ($codesRow['total'] ?? 0);
        $caTotal = (float) ($codesRow['ca'] ?? 0);

        $monthlyData = $this->getMonthlyRegistrations($db);
        $objectivesData = $this->getObjectivesDistribution($db);

        return view('bo/dashboard', [
            'usersTotal' => $usersTotal,
            'codesUsed' => $codesUsed,
            'caTotal' => $caTotal,
            'monthlyLabels' => $monthlyData['labels'],
            'monthlyData' => $monthlyData['data'],
            'objectivesLabels' => $objectivesData['labels'],
            'objectivesData' => $objectivesData['data'],
            'objectivesColors' => $objectivesData['colors'],
            'isAdmin' => $this->isAdminUser(1),
            'isConnected' => $this->isUserConnected(1),
        ]);
    }

    private function getMonthlyRegistrations($db): array
    {
        $query = "
            SELECT
                DATE_FORMAT(created_at, '%Y-%m') AS month,
                COUNT(*) AS count
            FROM users
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
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
            'idc' => '#8b5cf6',        // Violet
            'non_defini' => '#94a3b8', // Gray
        ];

        $labelMap = [
            'reduire' => 'Réduire le poids',
            'augmenter' => 'Augmenter le poids',
            'maintenir' => 'Maintenir le poids',
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
