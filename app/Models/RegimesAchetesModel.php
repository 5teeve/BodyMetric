<?php

namespace App\Models;

use CodeIgniter\Model;

class RegimesAchetesModel extends Model
{
    protected $table = 'regimes_achetes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['user_id', 'regime_id', 'prix_paye', 'date_achat', 'duree_jours', 'date_fin', 'status'];
    protected $useTimestamps = false;

    /**
     * Ajouter un régime acheté
     */
    public function addRegime($userId, $regimeId, $prixPaye, $dureeJours = 30)
    {
        $dateFin = date('Y-m-d', strtotime('+' . $dureeJours . ' days'));

        return $this->insert([
            'user_id' => $userId,
            'regime_id' => $regimeId,
            'prix_paye' => $prixPaye,
            'duree_jours' => $dureeJours,
            'date_fin' => $dateFin,
            'status' => 'actif'
        ]);
    }

    /**
     * Obtenir tous les régimes actifs d'un utilisateur
     */
    public function getActiveByUser($userId)
    {
        return $this->where('user_id', $userId)
            ->where('status', 'actif')
            ->findAll();
    }

    /**
     * Obtenir tous les régimes d'un utilisateur (peu importe le statut)
     */
    public function getAllByUser($userId)
    {
        return $this->where('user_id', $userId)
            ->orderBy('date_achat', 'DESC')
            ->findAll();
    }

    /**
     * Obtenir les détails d'un régime acheté avec ses données
     */
    public function getDetailsByUser($userId)
    {
        return $this->select('regimes_achetes.*, regimes.nom, regimes.pct_viande, regimes.pct_poisson, regimes.pct_volaille, regimes.duree, regimes.prix as prix_original, regimes.delta_poids')
            ->join('regimes', 'regimes.id = regimes_achetes.regime_id')
            ->where('regimes_achetes.user_id', $userId)
            ->orderBy('regimes_achetes.date_achat', 'DESC')
            ->findAll();
    }

    /**
     * Vérifier si un utilisateur a déjà acheté ce régime
     */
    public function hasUserBought($userId, $regimeId)
    {
        return $this->where('user_id', $userId)
            ->where('regime_id', $regimeId)
            ->where('status', 'actif')
            ->first() !== null;
    }

    /**
     * Obtenir un régime acheté spécifique
     */
    public function getById($id)
    {
        return $this->find($id);
    }

    /**
     * Mettre à jour le statut d'un régime
     */
    public function updateStatus($id, $status)
    {
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Compter les régimes actifs d'un utilisateur
     */
    public function countActiveByUser($userId)
    {
        return $this->where('user_id', $userId)
            ->where('status', 'actif')
            ->countAllResults();
    }
}
