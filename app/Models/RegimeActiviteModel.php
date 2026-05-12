<?php

namespace App\Models;

use CodeIgniter\Model;

class RegimeActiviteModel extends Model
{
    protected $table = 'regime_activite';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['regime_id', 'activite_id'];
    protected $useTimestamps = false;

    public function getActiviteIdsForRegime(int $regimeId): array
    {
        return array_map(
            static fn($row) => (int) $row['activite_id'],
            $this->select('activite_id')
                ->where('regime_id', $regimeId)
                ->findAll()
        );
    }

    public function syncForRegime(int $regimeId, array $activiteIds): void
    {
        $activiteIds = array_values(array_unique(array_map('intval', $activiteIds)));

        $this->where('regime_id', $regimeId)->delete();

        if (empty($activiteIds)) {
            return;
        }

        $rows = array_map(static fn($id) => [
            'regime_id' => $regimeId,
            'activite_id' => $id,
        ], $activiteIds);

        $this->insertBatch($rows);
    }

    public function isActiviteLinked(int $activiteId): bool
    {
        return $this->where('activite_id', $activiteId)->countAllResults() > 0;
    }
}
