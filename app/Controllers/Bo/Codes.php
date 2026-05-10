<?php

namespace App\Controllers\Bo;

use App\Controllers\BaseController;
use App\Models\CodeModel;

class Codes extends BaseController
{
    public function index(): string
    {
        $model = new CodeModel();
        $data = $model->getPaginatedWithPager(15);

        return view('bo/codes', [
            'codes' => $data['codes'],
            'pager' => $data['pager'],
        ]);
    }

    public function form(): string
    {
        return view('bo/codes_form', [
            'oldMontant' => old('montant'),
            'oldQuantite' => old('quantite', '1'),
            'errors' => session()->getFlashdata('errors') ?? [],
            'success' => session()->getFlashdata('success'),
        ]);
    }

    public function store()
    {
        if (! $this->request->is('post')) {
            return redirect()->to('/bo/codes/form');
        }

        $rules = [
            'montant' => 'required|decimal|greater_than[0]',
            'quantite' => 'required|integer|greater_than[0]|less_than_equal_to[100]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $montant = (float) $this->request->getPost('montant');
        $quantite = (int) $this->request->getPost('quantite');

        try {
            (new CodeModel())->createBatch($montant, $quantite);

            return redirect()->to('/bo/codes')->with('success', $quantite . ' code(s) généré(s) avec succès.');
        } catch (\Throwable $e) {
            log_message('error', '[Bo\Codes] generation failed: ' . $e->getMessage());

            return redirect()->back()->withInput()->with('errors', ['general' => $e->getMessage()]);
        }
    }
}
