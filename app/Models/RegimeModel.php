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
        'pct_volaille'=> 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
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
}
