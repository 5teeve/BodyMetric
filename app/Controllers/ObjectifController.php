<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\User;

class ObjectifController extends Controller
{
    public function index()
    {
        return view('objectif');
    }

    public function store()
    {
        $session = session();

        // Require authentication
        if (!$session->has('user_id')) {
            return redirect()->to('/connexion');
        }

        $objectif = $this->request->getPost('objectif');
        if (empty($objectif)) {
            return redirect()->back()->with('error', 'Objectif invalide');
        }

        $userId = (int) $session->get('user_id');

        // Save to session
        $session->set('objectif', $objectif);

        // Save to database
        $userModel = new User();
        $userModel->update($userId, ['objectif' => $objectif]);

        // Redirect to suggestions/results
        return redirect()->to('/resultats?objectif=' . urlencode($objectif));
    }

    public function distribution()
    {
        $db = \Config\Database::connect();

        $rows = [];

        // Prefer a dedicated table 'user_objectifs' if present
        if ($db->tableExists('user_objectifs')) {
            $builder = $db->table('user_objectifs');
            $rows = $builder->select('objectif, COUNT(*) AS cnt')->groupBy('objectif')->get()->getResultArray();
        } else {
            // Fallback: check if `users` table has an `objectif` column
            $colCheck = $db->query("SHOW COLUMNS FROM users LIKE 'objectif'");
            if (count($colCheck->getResultArray()) > 0) {
                $builder = $db->table('users');
                $rows = $builder->select('objectif, COUNT(*) AS cnt')->groupBy('objectif')->get()->getResultArray();
            } else {
                // No data source found — return example data (zero-cost fallback)
                $rows = [
                    ['objectif' => 'augmenter', 'cnt' => 10],
                    ['objectif' => 'reduire', 'cnt' => 15],
                    ['objectif' => 'imc-ideal', 'cnt' => 5],
                ];
            }
        }

        $labels = [];
        $data = [];
        foreach ($rows as $r) {
            $labels[] = (string) ($r['objectif'] ?? 'inconnu');
            $data[] = (int) ($r['cnt'] ?? 0);
        }

        return $this->response->setJSON(['labels' => $labels, 'data' => $data]);
    }
}
