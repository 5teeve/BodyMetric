<?php

namespace App\Models;

use CodeIgniter\Model;

class User extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'nom',
        'prenom',
        'email',
        'mdp',
        'genre',
        'taille',
        'poids',
        'imc',
        'wallet',
        'is_gold'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules    = [
        'nom'     => 'required|string|min_length[2]|max_length[255]',
        'prenom'  => 'required|string|min_length[2]|max_length[255]',
        'email'   => 'required|valid_email|is_unique[users.email]',
        'mdp'     => 'required|min_length[8]',
        'genre'   => 'required|in_list[M,F]',
        'taille'  => 'required|numeric|greater_than[50]|less_than[250]',
        'poids'   => 'required|numeric|greater_than[20]|less_than[300]',
    ];

    protected $validationMessages = [
        'nom'     => [
            'required'   => 'Le nom est requis',
            'min_length' => 'Le nom doit avoir au moins 2 caractères',
            'max_length' => 'Le nom ne doit pas dépasser 255 caractères',
        ],
        'prenom'  => [
            'required'   => 'Le prénom est requis',
            'min_length' => 'Le prénom doit avoir au moins 2 caractères',
            'max_length' => 'Le prénom ne doit pas dépasser 255 caractères',
        ],
        'email'   => [
            'required'    => 'L\'email est requis',
            'valid_email' => 'Email invalide',
            'is_unique'   => 'Cet email est déjà utilisé',
        ],
        'mdp'     => [
            'required'   => 'Le mot de passe est requis',
            'min_length' => 'Le mot de passe doit avoir au moins 8 caractères',
        ],
        'genre'   => [
            'required' => 'Le genre est requis',
            'in_list'  => 'Genre invalide',
        ],
        'taille'  => [
            'required'     => 'La taille est requise',
            'numeric'      => 'La taille doit être un nombre',
            'greater_than' => 'La taille doit être supérieure à 50 cm',
            'less_than'    => 'La taille doit être inférieure à 250 cm',
        ],
        'poids'   => [
            'required'     => 'Le poids est requis',
            'numeric'      => 'Le poids doit être un nombre',
            'greater_than' => 'Le poids doit être supérieur à 20 kg',
            'less_than'    => 'Le poids doit être inférieur à 300 kg',
        ],
    ];

    public function calculateIMC(float $poids, float $taille): float
    {
        $tailleEnMetre = $taille / 100;
        return round($poids / ($tailleEnMetre ** 2), 2);
    }

    public function getById(int $id): ?array
    {
        return $this->find($id);
    }

    public function updatePersonal(int $id, array $data): bool
    {
        return (bool) $this->skipValidation(true)->update($id, $data);
    }

    public function updateHealth(int $id, float $taille, float $poids): bool
    {
        $imc = $this->calculateIMC($poids, $taille);

        return (bool) $this->skipValidation(true)->update($id, [
            'taille' => $taille,
            'poids'  => $poids,
            'imc'    => $imc,
        ]);
    }

    public function createUserWithTransaction(array $userData): int
    {
        $this->db->transStart();

        try {
            $userId = $this->insert($userData, true);

            if (!$userId) {
                throw new \RuntimeException('Echec de l\'insertion utilisateur');
            }

            $this->db->transComplete();

            return (int) $userId;
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', '[User] createUserWithTransaction failed: ' . $e->getMessage());
            return 0;
        }
    }

    public function upgradeToGold(int $id, float $price): array
    {
        $user = $this->find($id);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Utilisateur introuvable.'
            ];
        }

        $isGold = isset($user['is_gold']) && (int) $user['is_gold'] === 1;
        if ($isGold) {
            return [
                'success' => true,
                'message' => 'Compte deja Gold.',
                'wallet' => (float) ($user['wallet'] ?? 0),
                'is_gold' => 1,
                'already' => true,
            ];
        }

        $wallet = (float) ($user['wallet'] ?? 0);
        if ($wallet < $price) {
            return [
                'success' => false,
                'message' => 'Solde insuffisant pour passer Gold.'
            ];
        }

        $newWallet = $wallet - $price;

        $updated = $this->skipValidation(true)->update($id, [
            'wallet' => $newWallet,
            'is_gold' => 1,
        ]);

        if (!$updated) {
            return [
                'success' => false,
                'message' => 'Impossible de mettre a jour le compte.'
            ];
        }

        return [
            'success' => true,
            'message' => 'Passage Gold reussi.',
            'wallet' => $newWallet,
            'is_gold' => 1,
        ];
    }
}
