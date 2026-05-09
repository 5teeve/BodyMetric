<?php

namespace App\Controllers;

use App\Models\User;

class WalletController extends BaseController
{
    protected User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function index(): string
    {
        $session = session();
        $balance = 0.00;
        $displayName = 'Votre portefeuille';

        $userId = $session->get('user_id');

        if ($userId) {
            $user = $this->userModel->find($userId);

            if (is_array($user)) {
                $balance = (float) ($user['wallet'] ?? 0);
                $displayName = trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?: $displayName;
            }
        } elseif ($session->has('registration')) {
            $registration = $session->get('registration');
            if (is_array($registration)) {
                $displayName = trim(($registration['prenom'] ?? '') . ' ' . ($registration['nom'] ?? '')) ?: $displayName;
            }
        }

        $history = [];

        return view('wallet/wallet', [
            'balance' => $balance,
            'displayName' => $displayName,
            'history' => $history,
            'lastUpdated' => date('d/m/Y à H:i'),
        ]);
    }
}