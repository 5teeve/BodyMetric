<?php

namespace App\Models;

use CodeIgniter\Model;

class ActiviteModel extends Model
{
    protected $table            = 'activites';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nom',
        'type',
        'intensite',
        'duree_base',
        'calories_min',
        'objectif',
        'description',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'nom'          => 'required|string|max_length[100]',
        'type'         => 'required|in_list[cardio,musculation,flexibilite,sport]',
        'intensite'    => 'required|in_list[faible,moderee,moyenne,elevee]',
        'duree_base'   => 'required|integer|greater_than[0]',
        'calories_min' => 'required|integer|greater_than[0]',
        'objectif'     => 'required|in_list[reduire,augmenter,maintenir]',
    ];

    protected $validationMessages = [
        'nom' => [
            'required'   => 'Le nom de l\'activité est obligatoire',
            'max_length' => 'Le nom ne doit pas dépasser 100 caractères',
        ],
        'type' => [
            'required' => 'Le type d\'activité est obligatoire',
            'in_list'  => 'Type d\'activité invalide',
        ],
        'intensite' => [
            'required' => 'L\'intensité est obligatoire',
            'in_list'  => 'Intensité invalide',
        ],
        'duree_base' => [
            'required'     => 'La durée de base est obligatoire',
            'greater_than' => 'La durée doit être supérieure à 0',
        ],
        'calories_min' => [
            'required'     => 'Les calories brûlées sont obligatoires',
            'greater_than' => 'Les calories doivent être supérieures à 0',
        ],
        'objectif' => [
            'required' => 'L\'objectif est obligatoire',
            'in_list'  => 'Objectif invalide',
        ],
    ];

    protected $skipValidation = false;

    public function getAllActivites(): array
    {
        return $this->findAll();
    }

    public function getActiviteById(int $id): ?array
    {
        return $this->find($id);
    }

    public function getActivitesByObjectif(string $objectif): array
    {
        return $this->where('objectif', $objectif)->findAll();
    }

    public function getActivitesByType(string $type): array
    {
        return $this->where('type', $type)->findAll();
    }

    public function getActivitesByIntensite(string $intensite): array
    {
        return $this->where('intensite', $intensite)->findAll();
    }

    public function createActivite(array $data): array
    {
        if (!$this->validate($data)) {
            return ['success' => false, 'errors' => $this->errors()];
        }

        $id = $this->insert($data);
        return ['success' => true, 'id' => $id];
    }

    public function updateActivite(int $id, array $data): array
    {
        if (!$this->validate($data)) {
            return ['success' => false, 'errors' => $this->errors()];
        }

        if (!$this->find($id)) {
            return ['success' => false, 'error' => 'Activité non trouvée'];
        }

        $this->update($id, $data);
        return ['success' => true];
    }

    public function deleteActivite(int $id): array
    {
        if (!$this->find($id)) {
            return ['success' => false, 'error' => 'Activité non trouvée'];
        }

        $this->delete($id);
        return ['success' => true];
    }
}
