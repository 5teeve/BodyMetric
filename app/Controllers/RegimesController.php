<?php

namespace App\Controllers;

use App\Models\RegimeModel;
use App\Models\RegimesAchetesModel;
use App\Models\User;

class RegimesController extends BaseController
{
    protected $regimeModel;
    protected $regimesAchetesModel;
    protected $userModel;

    public function __construct()
    {
        $this->regimeModel = new RegimeModel();
        $this->regimesAchetesModel = new RegimesAchetesModel();
        $this->userModel = new User();
    }

    /**
     * Page "Mes Régimes" - Afficher les régimes achetés
     */
    public function myRegimes()
    {
        $userId = (int) session()->get('user_id');

        if ($userId <= 0) {
            return redirect()->to('/connexion');
        }

        $user = $this->userModel->getById($userId);
        $regimesAchetes = $this->regimesAchetesModel->getDetailsByUser($userId);

        // Formater les données pour l'affichage
        $regimes = array_map(function ($regime) {
            return [
                'id' => $regime['id'],
                'regime_id' => $regime['regime_id'],
                'nom' => $regime['nom'],
                'pct_viande' => $regime['pct_viande'],
                'pct_poisson' => $regime['pct_poisson'],
                'pct_volaille' => $regime['pct_volaille'],
                'duree' => $regime['duree'],
                'prix_paye' => (float) $regime['prix_paye'],
                'prix_original' => (float) $regime['prix_original'],
                'delta_poids' => $regime['delta_poids'],
                'date_achat' => $regime['date_achat'],
                'date_fin' => $regime['date_fin'],
                'status' => $regime['status'],
                'duree_jours' => $regime['duree_jours'],
                'remise_appliquee' => (float) $regime['prix_paye'] < (float) $regime['prix_original']
            ];
        }, $regimesAchetes);

        $data = [
            'user' => $user,
            'regimes' => $regimes,
            'totalRegimes' => count($regimes),
            'regimesActifs' => count(array_filter($regimes, fn($r) => $r['status'] === 'actif')),
        ];

        return view('regimes/my_regimes', $data);
    }

    /**
     * Acheter/Choisir un régime
     */ public function choisir()
    {
        $userId = (int) session()->get('user_id');

        if ($userId <= 0) {
            return redirect()->to('/connexion');
        }

        $json = $this->request->getJSON();
        $regimeId = (int) ($json->regime_id ?? 0);

        if ($regimeId <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'ID régime invalide']);
        }

        // Vérifier que le régime existe
        $regime = $this->regimeModel->find($regimeId);
        if (!$regime) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Régime non trouvé']);
        }

        $user = $this->userModel->getById($userId);
        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Utilisateur non trouvé']);
        }

        // Vérifier si déjà acheté
        if ($this->regimesAchetesModel->hasUserBought($userId, $regimeId)) {
            return $this->response->setStatusCode(409)->setJSON(['error' => 'Vous avez déjà ce régime']);
        }

        $prix = (float) $regime['prix'];
        $isGold = (int) $user['is_gold'] === 1;
        $prixFinal = $isGold ? $prix * 0.85 : $prix;

        // Vérifier le solde du portefeuille
        $wallet = (float) $user['wallet'];
        if ($wallet < $prixFinal) {
            return $this->response->setStatusCode(402)->setJSON([
                'error' => 'Solde insuffisant',
                'solde_actuel' => $wallet,
                'prix_requis' => $prixFinal
            ]);
        }

        // Effectuer l'achat
        try {
            $this->regimesAchetesModel->addRegime($userId, $regimeId, $prixFinal);

            $nouveauSolde = $wallet - $prixFinal;
            $this->userModel->update($userId, ['wallet' => $nouveauSolde]);

            session()->set('wallet', $nouveauSolde);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Régime choisi avec succès!',
                'prix_paye' => $prixFinal,
                'nouveau_solde' => $nouveauSolde
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Erreur lors du choix du régime: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Erreur serveur']);
        }
    }

    /**
     * Acheter un combo de régimes en un seul clic
     */
    public function choisirCombo()
    {
        $userId = (int) session()->get('user_id');

        if ($userId <= 0) {
            return redirect()->to('/connexion');
        }

        $json = $this->request->getJSON();
        $regimeIds = is_array($json->regime_ids ?? null) ? array_map('intval', $json->regime_ids) : [];
        $regimeIds = array_values(array_filter(array_unique($regimeIds), fn($id) => $id > 0));

        if (empty($regimeIds)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Aucun régime sélectionné']);
        }

        $user = $this->userModel->getById($userId);
        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Utilisateur non trouvé']);
        }

        $isGold = (int) $user['is_gold'] === 1;
        $wallet = (float) $user['wallet'];

        $regimes = [];
        foreach ($regimeIds as $regimeId) {
            $regime = $this->regimeModel->find($regimeId);
            if (!$regime) {
                return $this->response->setStatusCode(404)->setJSON(['error' => "Régime {$regimeId} non trouvé"]);
            }
            if ($this->regimesAchetesModel->hasUserBought($userId, $regimeId)) {
                return $this->response->setStatusCode(409)->setJSON(['error' => 'Vous avez déjà acheté un des régimes de ce pack']);
            }

            $regimes[] = $regime;
        }

        $totalPrice = 0.0;
        foreach ($regimes as $regime) {
            $prix = (float) $regime['prix'];
            $totalPrice += $isGold ? $prix * 0.85 : $prix;
        }

        if ($wallet < $totalPrice) {
            return $this->response->setStatusCode(402)->setJSON([
                'error' => 'Solde insuffisant',
                'solde_actuel' => $wallet,
                'prix_requis' => $totalPrice
            ]);
        }

        try {
            foreach ($regimes as $regime) {
                $prix = (float) $regime['prix'];
                $prixFinal = $isGold ? $prix * 0.85 : $prix;
                $this->regimesAchetesModel->addRegime($userId, $regime['id'], $prixFinal);
            }

            $nouveauSolde = $wallet - $totalPrice;
            $this->userModel->update($userId, ['wallet' => $nouveauSolde]);
            session()->set('wallet', $nouveauSolde);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pack de régimes choisi avec succès!',
                'prix_paye' => $totalPrice,
                'nouveau_solde' => $nouveauSolde
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Erreur lors du choix du pack de régimes: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Erreur serveur']);
        }
    }
    /**
     * Détail d'un régime acheté (AJAX)
     */
    public function detail($id)
    {
        $userId = (int) session()->get('user_id');

        if ($userId <= 0) {
            return redirect()->to('/connexion');
        }

        $regimeAchete = $this->regimesAchetesModel->getById($id);

        if (!$regimeAchete || (int) $regimeAchete['user_id'] !== $userId) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Régime non trouvé']);
        }

        return $this->response->setJSON($regimeAchete);
    }

    /**
     * Annuler un régime
     */
    public function cancel($id)
    {
        if ($this->request->getMethod() !== 'post') {
            return $this->response->setStatusCode(405)->setJSON(['error' => 'Méthode non autorisée']);
        }

        $userId = (int) session()->get('user_id');

        if ($userId <= 0) {
            return redirect()->to('/connexion');
        }

        $regimeAchete = $this->regimesAchetesModel->getById($id);

        if (!$regimeAchete || (int) $regimeAchete['user_id'] !== $userId) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Régime non trouvé']);
        }

        try {
            $this->regimesAchetesModel->updateStatus($id, 'annule');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Régime annulé'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Erreur lors de l\'annulation: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Erreur serveur']);
        }
    }
}
