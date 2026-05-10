<?php

namespace App\Controllers;

use App\Models\RegimeModel;

class RegimeController extends BaseController
{
    protected $regimeModel;

    public function __construct()
    {
        $this->regimeModel = new RegimeModel();
    }

    /**
     * Afficher la liste de tous les régimes
     */
    public function index()
    {
        $regimes = $this->regimeModel->findAll();

        return view('regime/list', [
            'regimes' => $regimes,
        ]);
    }

    /**
     * Afficher un régime spécifique
     */
    public function show($id = null)
    {
        $regime = $this->regimeModel->find($id);

        if (!$regime) {
            return redirect()->to('/regimes')->with('error', 'Régime non trouvé');
        }

        return view('regime/show', [
            'regime' => $regime,
        ]);
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('regime/form', [
            'regime' => null,
            'errors' => [],
        ]);
    }

    /**
     * Stocker un nouveau régime
     */
    public function store()
    {
        $data = [
            'nom'          => $this->request->getPost('nom'),
            'pct_viande'   => $this->request->getPost('pct_viande'),
            'pct_poisson'  => $this->request->getPost('pct_poisson'),
            'pct_volaille' => $this->request->getPost('pct_volaille'),
            'duree'        => $this->request->getPost('duree'),
            'prix'         => $this->request->getPost('prix'),
            'delta_poids'  => $this->request->getPost('delta_poids'),
        ];

        if (!$this->regimeModel->validate($data)) {
            return redirect()->back()->withInput()->with('errors', $this->regimeModel->errors());
        }

        if ($this->regimeModel->insert($data)) {
            return redirect()->to('/regimes')->with('success', 'Régime créé avec succès');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la création du régime');
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit($id = null)
    {
        $regime = $this->regimeModel->find($id);

        if (!$regime) {
            return redirect()->to('/regimes')->with('error', 'Régime non trouvé');
        }

        return view('regime/form', [
            'regime' => $regime,
            'errors' => [],
        ]);
    }

    /**
     * Mettre à jour un régime
     */
    public function update($id = null)
    {
        $regime = $this->regimeModel->find($id);

        if (!$regime) {
            return redirect()->to('/regimes')->with('error', 'Régime non trouvé');
        }

        $data = [
            'nom'          => $this->request->getPost('nom'),
            'pct_viande'   => $this->request->getPost('pct_viande'),
            'pct_poisson'  => $this->request->getPost('pct_poisson'),
            'pct_volaille' => $this->request->getPost('pct_volaille'),
            'duree'        => $this->request->getPost('duree'),
            'prix'         => $this->request->getPost('prix'),
            'delta_poids'  => $this->request->getPost('delta_poids'),
        ];

        if (!$this->regimeModel->validate($data)) {
            return redirect()->back()->withInput()->with('errors', $this->regimeModel->errors());
        }

        if ($this->regimeModel->update($id, $data)) {
            return redirect()->to('/regimes')->with('success', 'Régime mis à jour avec succès');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour du régime');
    }

    /**
     * Supprimer un régime
     */
    public function delete($id = null)
    {
        $regime = $this->regimeModel->find($id);

        if (!$regime) {
            return redirect()->to('/regimes')->with('error', 'Régime non trouvé');
        }

        if ($this->regimeModel->delete($id)) {
            return redirect()->to('/regimes')->with('success', 'Régime supprimé avec succès');
        }

        return redirect()->back()->with('error', 'Erreur lors de la suppression du régime');
    }
}
