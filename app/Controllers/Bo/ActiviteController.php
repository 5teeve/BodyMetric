<?php

namespace App\Controllers\Bo;

use App\Controllers\BaseController;
use App\Models\ActiviteModel;

class ActiviteController extends BaseController
{
    protected $activiteModel;

    public function __construct()
    {
        $this->activiteModel = new ActiviteModel();
    }

    public function index()
    {
        $activites = $this->activiteModel->getAllActivites();

        return view('bo/activites/index', [
            'activites' => $activites,
            'isAdmin' => $this->isAdminUser(1),
            'isConnected' => $this->isUserConnected(1),
        ]);
    }

    public function form(?int $id = null)
    {
        $activite = null;
        if ($id !== null) {
            $activite = $this->activiteModel->getActiviteById($id);
        }

        return view('bo/activites/form', [
            'activite' => $activite,
            'isEditing' => $id !== null,
            'isAdmin' => $this->isAdminUser(1),
            'isConnected' => $this->isUserConnected(1),
        ]);
    }

    public function store()
    {
        $data = $this->request->getPost();

        $result = $this->activiteModel->createActivite($data);

        if (!$result['success']) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $result['errors'] ?? ['Erreur lors de la création']);
        }

        return redirect()->to('/bo/activites')
            ->with('success', 'Activité créée avec succès');
    }

    public function update(int $id)
    {
        $data = $this->request->getPost();

        $result = $this->activiteModel->updateActivite($id, $data);

        if (!$result['success']) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $result['errors'] ?? ['Erreur lors de la mise à jour']);
        }

        return redirect()->to('/bo/activites')
            ->with('success', 'Activité mise à jour avec succès');
    }

    public function delete(int $id)
    {
        $result = $this->activiteModel->deleteActivite($id);

        if (!$result['success']) {
            return redirect()->back()
                ->with('error', $result['error'] ?? 'Erreur lors de la suppression');
        }

        return redirect()->to('/bo/activites')
            ->with('success', 'Activité supprimée avec succès');
    }
}
