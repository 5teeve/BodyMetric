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

        return view('bo/dashboard', [
            'usersTotal' => $usersTotal,
            'codesUsed' => $codesUsed,
            'caTotal' => $caTotal,
            'isAdmin' => $this->isAdminUser(1),
            'isConnected' => $this->isUserConnected(1),
        ]);
    }
}
