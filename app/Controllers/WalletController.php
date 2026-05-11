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

        $adminUserId = $userId ? (int) $userId : 1;

        return view('wallet/wallet', [
            'balance' => $balance,
            'displayName' => $displayName,
            'history' => $history,
            'lastUpdated' => date('d/m/Y à H:i'),
            'isAdmin' => $this->isAdminUser($adminUserId),
        ]);
    }

    public function validateCodeAjax()
    {
        if (! $this->request->is('post')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Méthode non autorisée',
            ])->setStatusCode(405);
        }

        if (! $this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Requête AJAX attendue',
            ])->setStatusCode(400);
        }

        $session = session();
        // $userId = $session->get('user_id');
        $userId = 1;

        if (! $userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Vous devez être connecté pour créditer votre portefeuille',
            ])->setStatusCode(401);
        }

        $code = strtoupper(trim((string) $this->request->getPost('code')));

        if ($code === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Le code est requis',
            ])->setStatusCode(422);
        }

        if (! preg_match('/^[A-Z0-9\-]{6,32}$/', $code)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Format de code invalide',
            ])->setStatusCode(422);
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $codeRow = $db->query(
                'SELECT id, code, montant, statut, user_id, date_utilisation FROM codes WHERE code = ? LIMIT 1 FOR UPDATE',
                [$code]
            )->getRowArray();

            if (! $codeRow) {
                throw new \RuntimeException('Code introuvable');
            }

            if (($codeRow['statut'] ?? '') !== 'actif') {
                throw new \RuntimeException('Ce code a déjà été utilisé');
            }

            $amount = (float) ($codeRow['montant'] ?? 0);

            if ($amount <= 0) {
                throw new \RuntimeException('Montant de code invalide');
            }

            $user = $this->userModel->find($userId);

            if (! is_array($user)) {
                throw new \RuntimeException('Utilisateur introuvable');
            }

            $newBalance = round(((float) ($user['wallet'] ?? 0)) + $amount, 2);

            $userUpdated = $this->userModel->update($userId, [
                'wallet' => $newBalance,
            ]);

            if (! $userUpdated) {
                throw new \RuntimeException('Impossible de créditer le solde');
            }

            $codeUpdated = $db->table('codes')
                ->where('id', $codeRow['id'])
                ->update([
                    'statut' => 'utilise',
                    'user_id' => $userId,
                    'date_utilisation' => date('Y-m-d H:i:s'),
                ]);

            if (! $codeUpdated) {
                throw new \RuntimeException('Impossible de marquer le code comme utilisé');
            }

            $db->transCommit();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Portefeuille crédité avec succès',
                'amount' => $amount,
                'balance' => $newBalance,
                'code' => $code,
                'historyItem' => [
                    'label' => 'Recharge via code ' . $code,
                    'date' => date('d/m/Y à H:i'),
                    'amount' => '+' . number_format($amount, 2, ',', ' ') . ' Ar',
                    'type' => 'credit',
                ],
            ]);
        } catch (\Throwable $e) {
            $db->transRollback();

            log_message('error', '[Wallet] Code validation failed: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage(),
            ])->setStatusCode(422);
        }
    }
}