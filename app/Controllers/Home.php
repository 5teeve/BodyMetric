<?php

namespace App\Controllers;

use App\Models\User;

class Home extends BaseController
{
    public function index()
    {
        // Si non connecté → connexion
        if (!$this->isUserConnected()) {
            return redirect()->to('/connexion');
        }

        // Si connecté, vérifier si profil complet
        $userId = (int) session()->get('user_id');
        $userModel = new User();
        $user = $userModel->getById($userId);

        // Si profil incomplet (pas de taille/poids) → profil
        if (empty($user['taille']) || empty($user['poids'])) {
            return redirect()->to('/profil')
                ->with('warning', 'Veuillez compléter votre profil pour continuer.');
        }

        // Si objectif non défini → page objectif
        if (empty($user['objectif'])) {
            return redirect()->to('/objectif');
        }

        // Profil complet avec objectif → résultats/suggestions
        return redirect()->to('/resultats');
    }
}
