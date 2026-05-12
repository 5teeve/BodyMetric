<?php

namespace App\Controllers\Bo;

use App\Controllers\BaseController;
use App\Models\ActiviteModel;
use App\Models\RegimeActiviteModel;
use App\Models\RegimeModel;

class RegimeController extends BaseController
{
    protected $regimeModel;
    protected $activiteModel;
    protected $pivotModel;

    public function __construct()
    {
        $this->regimeModel = new RegimeModel();
        $this->activiteModel = new ActiviteModel();
        $this->pivotModel = new RegimeActiviteModel();
    }

    public function index()
    {
        $objectif = $this->request->getGet('objectif');
        $regimes = $this->regimeModel->getAllRegimes();

        if ($objectif === 'augmenter') {
            $regimes = array_filter($regimes, fn($r) => (float) ($r['delta_poids'] ?? 0) > 0);
        } elseif ($objectif === 'reduire') {
            $regimes = array_filter($regimes, fn($r) => (float) ($r['delta_poids'] ?? 0) < 0);
        } elseif ($objectif === 'equilibre') {
            $regimes = array_filter($regimes, fn($r) => abs((float) ($r['delta_poids'] ?? 0)) <= 0.5);
        }

        return view('bo/regimes/index', [
            'regimes' => $regimes,
            'objectif' => $objectif,
            'isAdmin' => $this->isAdminUser(),
            'isConnected' => $this->isUserConnected(),
        ]);
    }

    public function form(?int $id = null)
    {
        $regime = null;
        if ($id !== null) {
            $regime = $this->regimeModel->getRegimeById($id);
        }

        $activites = $this->activiteModel->getAllActivites();
        $selectedActivites = $id !== null
            ? $this->pivotModel->getActiviteIdsForRegime($id)
            : [];

        return view('bo/regimes/form', [
            'regime' => $regime,
            'isEditing' => $id !== null,
            'activites' => $activites,
            'selectedActivites' => $selectedActivites,
            'isAdmin' => $this->isAdminUser(),
            'isConnected' => $this->isUserConnected(),
        ]);
    }

    public function store()
    {
        $data = $this->request->getPost();
        $activiteIds = (array) ($this->request->getPost('activites') ?? []);
        unset($data['activites']);

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

        $this->pivotModel->syncForRegime((int) $result['id'], $activiteIds);

        return redirect()->to('/bo/regimes')
            ->with('success', 'Régime créé avec succès');
    }

    public function update(int $id)
    {
        $data = $this->request->getPost();
        $activiteIds = (array) ($this->request->getPost('activites') ?? []);
        unset($data['activites']);

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

        $this->pivotModel->syncForRegime($id, $activiteIds);

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
