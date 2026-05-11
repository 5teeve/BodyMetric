<?php

namespace App\Controllers\Bo;

use App\Controllers\BaseController;

class ParametreController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $parametres = $this->getAllParametres();

        return view('bo/parametres/index', [
            'parametres' => $parametres,
            'isAdmin' => $this->isAdminUser(1),
            'isConnected' => $this->isUserConnected(1),
        ]);
    }

    public function update()
    {
        $data = $this->request->getPost();

        if (empty($data['parametres']) || !is_array($data['parametres'])) {
            return redirect()->back()
                ->with('error', 'Aucun paramètre à mettre à jour');
        }

        $builder = $this->db->table('parametres');

        foreach ($data['parametres'] as $cle => $valeur) {
            $builder->where('cle', $cle);
            $exists = $builder->countAllResults(false) > 0;

            if ($exists) {
                $builder->where('cle', $cle);
                $builder->update(['valeur' => $valeur, 'updated_at' => date('Y-m-d H:i:s')]);
            } else {
                $builder->insert([
                    'cle' => $cle,
                    'valeur' => $valeur,
                    'description' => '',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        return redirect()->to('/bo/parametres')
            ->with('success', 'Paramètres mis à jour avec succès');
    }

    private function getAllParametres(): array
    {
        // Valeurs par défaut
        $defaults = [
            'prix_gold' => [
                'cle' => 'prix_gold',
                'valeur' => '100000',
                'description' => 'Prix de l\'option Gold (en Ariary)',
            ],
            'imc_seuil_maigreur' => [
                'cle' => 'imc_seuil_maigreur',
                'valeur' => '18.5',
                'description' => 'Seuil IMC pour la maigreur',
            ],
            'imc_seuil_surpoids' => [
                'cle' => 'imc_seuil_surpoids',
                'valeur' => '25',
                'description' => 'Seuil IMC pour le surpoids',
            ],
            'imc_seuil_obesite' => [
                'cle' => 'imc_seuil_obesite',
                'valeur' => '30',
                'description' => 'Seuil IMC pour l\'obésité',
            ],
            'remise_gold_pourcent' => [
                'cle' => 'remise_gold_pourcent',
                'valeur' => '15',
                'description' => 'Pourcentage de remise Gold sur les régimes (%)',
            ],
        ];

        // Récupérer les valeurs en base
        $builder = $this->db->table('parametres');
        $result = $builder->get()->getResultArray();

        foreach ($result as $row) {
            if (isset($defaults[$row['cle']])) {
                $defaults[$row['cle']] = array_merge($defaults[$row['cle']], $row);
            }
        }

        return $defaults;
    }
}
