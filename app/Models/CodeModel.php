<?php

namespace App\Models;

use CodeIgniter\Model;

class CodeModel extends Model
{
    protected $table = 'codes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['code', 'montant', 'statut', 'user_id', 'date_utilisation', 'created_at', 'updated_at'];
    protected $useTimestamps = false;

    public function getPaginated(int $perPage = 20)
    {
        return $this->orderBy('created_at', 'DESC')->paginate($perPage);
    }

    public function getPaginatedWithPager(int $perPage = 20): array
    {
        return [
            'codes' => $this->getPaginated($perPage),
            'pager' => $this->pager,
        ];
    }

    public function generateUniqueCode(): string
    {
        do {
            $code = 'BM-' . strtoupper(bin2hex(random_bytes(4))) . '-' . strtoupper(bin2hex(random_bytes(4)));
        } while ($this->where('code', $code)->first());

        return $code;
    }

    public function createBatch(float $montant, int $quantite): void
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            for ($i = 0; $i < $quantite; $i++) {
                $db->table($this->table)->insert([
                    'code' => $this->generateUniqueCode(),
                    'montant' => $montant,
                    'statut' => 'actif',
                    'user_id' => null,
                    'date_utilisation' => null,
                ]);
            }

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Impossible de générer les codes');
            }

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            throw $e;
        }
    }
}
