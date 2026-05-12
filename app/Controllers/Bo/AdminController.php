<?php

namespace App\Controllers\Bo;

use App\Controllers\BaseController;
use App\Models\User;

class AdminController extends BaseController
{
    protected User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function showLogin()
    {
        if ($this->isAdminUser()) {
            return redirect()->to('/bo/dashboard');
        }

        return view('bo/login', [
            'error' => session()->getFlashdata('error'),
        ]);
    }

    public function handleLogin()
    {
        if (!$this->request->is('post')) {
            return redirect()->to('/bo/login');
        }

        $rules = [
            'email' => 'required|valid_email',
            'mdp' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Veuillez renseigner un email et un mot de passe valides.');
        }

        $email = strtolower(trim((string) $this->request->getPost('email')));
        $mdp = (string) $this->request->getPost('mdp');

        $user = $this->userModel
            ->select('id, nom, prenom, email, mdp')
            ->where('email', $email)
            ->first();

        if (!$user || !password_verify($mdp, (string) $user['mdp']) || (int) $user['id'] !== 1) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Identifiants admin invalides.');
        }

        session()->set([
            'admin_id' => (int) $user['id'],
            'admin_nom' => trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')),
            'is_admin' => true,
        ]);

        return redirect()->to('/bo/dashboard');
    }

    public function logout()
    {
        session()->remove(['admin_id', 'admin_nom', 'is_admin']);

        return redirect()->to('/bo/login')->with('success', 'Déconnexion réussie.');
    }
}
