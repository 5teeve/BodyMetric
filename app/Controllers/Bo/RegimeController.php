<?php

namespace App\Controllers\Bo;

use App\Controllers\BaseController;
use App\Models\RegimeModel;

class RegimeController extends BaseController
{
    protected $regimeModel;

    public function __construct()
    {
        $this->regimeModel = new RegimeModel();
    }

    public function index()
    {
        $regimes = $this->regimeModel->getAllRegimes();

        return view('bo/regimes/index', [
            'regimes' => $regimes,
            'isAdmin' => $this->isAdminUser(1),
            'isConnected' => $this->isUserConnected(1),
        ]);
    }

    public function form(?int $id = null)
    {
        $regime = null;
        if ($id !== null) {
            $regime = $this->regimeModel->getRegimeById($id);
        }

        return view('bo/regimes/form', [
            'regime' => $regime,
            'isEditing' => $id !== null,
            'isAdmin' => $this->isAdminUser(1),
            'isConnected' => $this->isUserConnected(1),
        ]);
    }

    public function store()
    {
        $data = $this->request->getPost();

        // Validation personnalisée pour la somme des pourcentages
        $pctSum = (float) ($data['pct_viande'] ?? 0) + (float) ($data['pct_poisson'] ?? 0) + (float) ($data['pct_volaille'] ?? 0);

        if (abs($pctSum - 100) > 0.01) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'La somme des pourcentages (viande + poisson + volaille) doit être égale à 100%');
        }

        $result = $this->regimeModel->createRegime($data);

        if (!$result['success']) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $result['errors'] ?? ['Erreur lors de la création']);
        }

        return redirect()->to('/bo/regimes')
            ->with('success', 'Régime créé avec succès');
    }

    public function update(int $id)
    {
        $data = $this->request->getPost();

        // Validation personnalisée pour la somme des pourcentages
        $pctSum = (float) ($data['pct_viande'] ?? 0) + (float) ($data['pct_poisson'] ?? 0) + (float) ($data['pct_volaille'] ?? 0);

        if (abs($pctSum - 100) > 0.01) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'La somme des pourcentages (viande + poisson + volaille) doit être égale à 100%');
        }

        $result = $this->regimeModel->updateRegime($id, $data);

        if (!$result['success']) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $result['errors'] ?? ['Erreur lors de la mise à jour']);
        }

        return redirect()->to('/bo/regimes')
            ->with('success', 'Régime mis à jour avec succès');
    }

    public function delete(int $id)
    {
        $result = $this->regimeModel->deleteRegime($id);

        if (!$result['success']) {
            return redirect()->back()
                ->with('error', $result['error'] ?? 'Erreur lors de la suppression');
        }

        return redirect()->to('/bo/regimes')
            ->with('success', 'Régime supprimé avec succès');
    }
}
