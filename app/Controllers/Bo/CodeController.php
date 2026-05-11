<?php

namespace App\Controllers\Bo;

use App\Controllers\BaseController;
use App\Models\CodeModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class CodeController extends BaseController
{
    protected CodeModel $codeModel;

    public function __construct()
    {
        $this->codeModel = new CodeModel();
    }

    public function index(): string
    {
        $data = $this->codeModel->paginateCodes(15);

        return view('bo/codes', [
            'codes' => $data['codes'],
            'pager' => $data['pager'],
            'success' => session()->getFlashdata('success'),
            'errors' => session()->getFlashdata('errors') ?? [],
            'isAdmin' => $this->isAdminUser(1),
            'isConnected' => $this->isUserConnected(1),
        ]);
    }

    public function form(int $id = null): string
    {
        $code = $id ? $this->codeModel->findCode($id) : null;

        if ($id && ! $code) {
            throw PageNotFoundException::forPageNotFound('Code introuvable');
        }

        return view('bo/codes_form', [
            'code' => $code,
            'oldMontant' => old('montant', $code['montant'] ?? ''),
            'oldQuantite' => old('quantite', '1'),
            'errors' => session()->getFlashdata('errors') ?? [],
            'success' => session()->getFlashdata('success'),
            'isAdmin' => $this->isAdminUser(1),
            'isConnected' => $this->isUserConnected(1),
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
            $this->codeModel->createBatch($montant, $quantite);

            return redirect()->to('/bo/codes')->with('success', $quantite . ' code(s) généré(s) avec succès.');
        } catch (\Throwable $e) {
            log_message('error', '[Bo\CodeController] generation failed: ' . $e->getMessage());

            return redirect()->back()->withInput()->with('errors', ['general' => $e->getMessage()]);
        }
    }

    public function update(int $id)
    {
        if (! $this->request->is('post')) {
            return redirect()->to('/bo/codes');
        }

        $code = $this->codeModel->findCode($id);

        if (! $code) {
            return redirect()->to('/bo/codes')->with('errors', ['general' => 'Code introuvable']);
        }

        $rules = [
            'montant' => 'required|decimal|greater_than[0]',
            'statut' => 'required|in_list[actif,utilise]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $payload = [
            'montant' => (float) $this->request->getPost('montant'),
            'statut' => $this->request->getPost('statut'),
        ];

        try {
            $this->codeModel->updateCode($id, $payload);

            return redirect()->to('/bo/codes')->with('success', 'Code mis à jour avec succès.');
        } catch (\Throwable $e) {
            log_message('error', '[Bo\CodeController] update failed: ' . $e->getMessage());

            return redirect()->back()->withInput()->with('errors', ['general' => $e->getMessage()]);
        }
    }

    public function invalidate(int $id)
    {
        if (! $this->request->is('post')) {
            return redirect()->to('/bo/codes');
        }

        $code = $this->codeModel->findCode($id);

        if (! $code) {
            return redirect()->to('/bo/codes')->with('errors', ['general' => 'Code introuvable']);
        }

        try {
            $this->codeModel->invalidateCode($id);

            return redirect()->to('/bo/codes')->with('success', 'Code invalidé avec succès.');
        } catch (\Throwable $e) {
            log_message('error', '[Bo\CodeController] invalidate failed: ' . $e->getMessage());

            return redirect()->to('/bo/codes')->with('errors', ['general' => $e->getMessage()]);
        }
    }

    public function delete(int $id)
    {
        if (! $this->request->is('post')) {
            return redirect()->to('/bo/codes');
        }

        $code = $this->codeModel->findCode($id);

        if (! $code) {
            return redirect()->to('/bo/codes')->with('errors', ['general' => 'Code introuvable']);
        }

        try {
            $this->codeModel->deleteCode($id);

            return redirect()->to('/bo/codes')->with('success', 'Code supprimé avec succès.');
        } catch (\Throwable $e) {
            log_message('error', '[Bo\CodeController] delete failed: ' . $e->getMessage());

            return redirect()->to('/bo/codes')->with('errors', ['general' => $e->getMessage()]);
        }
    }
}
