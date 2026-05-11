<?php

namespace App\Models;

use CodeIgniter\Model;

class RegimeModel extends Model
{
    protected $table            = 'regimes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nom',
        'pct_viande',
        'pct_poisson',
        'pct_volaille',
        'duree',
        'prix',
        'delta_poids',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'nom'         => 'required|string|max_length[100]',
        'pct_viande'  => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
        'pct_poisson' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
        'pct_volaille'=> 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]|valid_percentage_sum',
        'duree'       => 'required|integer|greater_than[0]',
        'prix'        => 'required|numeric|greater_than[0]',
        'delta_poids' => 'numeric',
    ];

    protected $validationMessages   = [
        'nom' => [
            'required'   => 'Le nom du régime est obligatoire',
            'max_length' => 'Le nom ne doit pas dépasser 100 caractères',
        ],
        'pct_viande' => [
            'required' => 'Le pourcentage de viande est obligatoire',
            'numeric'  => 'Le pourcentage doit être un nombre',
        ],
        'pct_poisson' => [
            'required' => 'Le pourcentage de poisson est obligatoire',
            'numeric'  => 'Le pourcentage doit être un nombre',
        ],
        'pct_volaille' => [
            'required' => 'Le pourcentage de volaille est obligatoire',
            'numeric'  => 'Le pourcentage doit être un nombre',
            'valid_percentage_sum' => 'La somme des pourcentages (viande + poisson + volaille) doit être égale à 100%',
        ],
        'duree' => [
            'required'     => 'La durée est obligatoire',
            'greater_than' => 'La durée doit être supérieure à 0',
        ],
        'prix' => [
            'required'     => 'Le prix est obligatoire',
            'greater_than' => 'Le prix doit être supérieur à 0',
        ],
    ];

    protected $skipValidation       = false;

    public function validPercentageSum($value = null, $data = null): bool
    {
        if (!is_array($data)) {
            return false;
        }

        $viande = (float) ($data['pct_viande'] ?? 0);
        $poisson = (float) ($data['pct_poisson'] ?? 0);
        $volaille = (float) ($data['pct_volaille'] ?? 0);

        $somme = $viande + $poisson + $volaille;

        return abs($somme - 100) < 0.01; // Tolérance de 0.01% pour les arrondis
    }

    public function createRegime($data)
    {
        if (!$this->validate($data)) {
            return ['success' => false, 'errors' => $this->errors()];
        }

        $id = $this->insert($data);
        return ['success' => true, 'id' => $id];
    }

    public function getAllRegimes()
    {
        return $this->findAll();
    }

    public function getRegimeById($id)
    {
        return $this->find($id);
    }

    public function updateRegime($id, $data)
    {
        if (!$this->validate($data)) {
            return ['success' => false, 'errors' => $this->errors()];
        }

        if (!$this->find($id)) {
            return ['success' => false, 'error' => 'Régime non trouvé'];
        }

        $this->update($id, $data);
        return ['success' => true];
    }

    public function deleteRegime($id)
    {
        if (!$this->find($id)) {
            return ['success' => false, 'error' => 'Régime non trouvé'];
        }

        $this->delete($id);
        return ['success' => true];
    }
}
