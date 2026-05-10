<?php

namespace App\Controllers;

use App\Models\User;

class ProfilController extends BaseController
{
    protected $userModel;
    protected int $fixedUserId = 1;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function index()
    {
        $user = $this->userModel->getById($this->fixedUserId);
        $imc  = $user['imc'] ?? null;

        return view('profil/profil', [
            'user' => $user,
            'imcLabel' => $this->getImcLabel($imc),
        ]);
    }


    public function updatePersonal()
    {
        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405)->setJSON([
                'success' => false,
                'message' => 'Methode non autorisee.'
            ]);
        }

        $data = [
            'nom'    => trim($this->request->getPost('nom')),
            'prenom' => trim($this->request->getPost('prenom')),
            'email'  => strtolower(trim($this->request->getPost('email'))),
            'genre'  => $this->request->getPost('genre'),
        ];

        if (!$this->userModel->updatePersonal($this->fixedUserId, $data)) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Impossible de mettre a jour les informations personnelles.'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Informations personnelles mises a jour.'
        ]);
    }


    public function updateHealth()
    {
        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405)->setJSON([
                'success' => false,
                'message' => 'Methode non autorisee.'
            ]);
        }

        $taille = (float) $this->request->getPost('taille');
        $poids  = (float) $this->request->getPost('poids');

        if (!$this->userModel->updateHealth($this->fixedUserId, $taille, $poids)) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Impossible de mettre a jour les donnees de sante.'
            ]);
        }

        $imc = $this->userModel->calculateIMC($poids, $taille);

        return $this->response->setJSON([
            'success'  => true,
            'message'  => 'Donnees de sante mises a jour.',
            'imc'      => $imc,
            'imcLabel' => $this->getImcLabel($imc),
        ]);
    }

    private function getImcLabel($imc): string
    {
        if (!is_numeric($imc)) {
            return 'Non calcule';
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

        return 'Obesite';
    }
}
