<?php

namespace App\Models;

use CodeIgniter\Model;

class CodeModel extends Model
{
    protected $table = 'codes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['code', 'montant', 'statut', 'user_id', 'date_utilisation', 'created_at', 'updated_at'];
    protected $useTimestamps = false;

    public function paginateCodes(int $perPage = 20, ?string $statut = null): array
    {
        $builder = $this->select('codes.*, users.nom, users.prenom, users.email')
            ->join('users', 'users.id = codes.user_id', 'left')
            ->orderBy('codes.created_at', 'DESC');

        if ($statut !== null && in_array($statut, ['actif', 'utilise'], true)) {
            $builder->where('codes.statut', $statut);
        }

        return [
            'codes' => $builder->paginate($perPage),
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

    public function findCode(int $id): ?array
    {
        $code = $this->find($id);

        return is_array($code) ? $code : null;
    }

    public function updateCode(int $id, array $data): bool
    {
        if (($data['statut'] ?? null) === 'utilise' && empty($data['date_utilisation'])) {
            $data['date_utilisation'] = date('Y-m-d H:i:s');
        }

        return (bool) $this->update($id, $data);
    }

    public function invalidateCode(int $id): bool
    {
        return (bool) $this->update($id, [
            'statut' => 'utilise',
            'date_utilisation' => date('Y-m-d H:i:s'),
        ]);
    }

    public function deleteCode(int $id): bool
    {
        return (bool) $this->delete($id);
    }
}
