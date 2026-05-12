<?php

namespace App\Controllers\Bo;

use App\Controllers\BaseController;
use App\Models\ActiviteModel;
use App\Models\RegimeActiviteModel;

class ActiviteController extends BaseController
{
    protected $activiteModel;
    protected $pivotModel;

    public function __construct()
    {
        $this->activiteModel = new ActiviteModel();
        $this->pivotModel = new RegimeActiviteModel();
    }

    public function index()
    {
        $activites = $this->activiteModel->getAllActivites();

        return view('bo/activites/index', [
            'activites' => $activites,
            'isAdmin' => $this->isAdminUser(),
            'isConnected' => $this->isUserConnected(),
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
            'isAdmin' => $this->isAdminUser(),
            'isConnected' => $this->isUserConnected(),
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
        if ($this->pivotModel->isActiviteLinked($id)) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer: activité liée à un régime.');
        }

        $result = $this->activiteModel->deleteActivite($id);

        if (!$result['success']) {
            return redirect()->back()
                ->with('error', $result['error'] ?? 'Erreur lors de la suppression');
        }

        return redirect()->to('/bo/activites')
            ->with('success', 'Activité supprimée avec succès');
    }
}
