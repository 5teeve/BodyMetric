<?php

namespace App\Controllers;

use App\Models\RegimeModel;
use App\Models\User;

class ResultatsController extends BaseController
{
    protected $regimeModel;
    protected $userModel;

    public function __construct()
    {
        $this->regimeModel = new RegimeModel();
        $this->userModel = new User();
    }

    public function index()
    {
        $userId = (int) session()->get('user_id');

        if ($userId <= 0) {
            return redirect()->to('/connexion');
        }

        $regimes = $this->regimeModel->getAllRegimes();

        $user = $this->userModel->getById($userId);
        $taille = isset($user['taille']) ? (float) $user['taille'] : 0.0;
        $poids = isset($user['poids']) ? (float) $user['poids'] : 0.0;
        $isGold = isset($user['is_gold']) && (int) $user['is_gold'] === 1;

        // Accepter les paramètres GET ou POST
        $objectifType = (string) ($this->request->getPost('objectif') ?? $this->request->getGet('objectif') ?? 'ideal');
        $objectifPoids = (float) ($this->request->getPost('objectif_poids') ?? $this->request->getGet('objectif_poids') ?? 0);

        $targetPoids = 0.0;
        if ($objectifType === 'ideal' && $taille > 0) {
            $tailleM = $taille / 100;
            $targetPoids = 22 * ($tailleM ** 2);
        } elseif ($objectifPoids > 0 && $poids > 0) {
            $targetPoids = $objectifType === 'augmenter'
                ? $poids + $objectifPoids
                : $poids - $objectifPoids;
        } elseif ($poids > 0) {
            $targetPoids = $objectifType === 'augmenter' ? $poids + 1 : $poids - 1;
        }

        $desiredDelta = $targetPoids > 0 && $poids > 0 ? ($targetPoids - $poids) : 0.0;

        $filteredRegimes = array_filter($regimes, function ($regime) use ($objectifType) {
            $delta = isset($regime['delta_poids']) ? (float) $regime['delta_poids'] : null;

            if ($delta === null) {
                return false;
            }

            if ($objectifType === 'augmenter') {
                return $delta > 0;
            }

            if ($objectifType === 'reduire') {
                return $delta < 0;
            }

            return true;
        });

        usort($filteredRegimes, function ($a, $b) use ($desiredDelta) {
            $deltaA = (float) ($a['delta_poids'] ?? 0);
            $deltaB = (float) ($b['delta_poids'] ?? 0);

            return abs($deltaA - $desiredDelta) <=> abs($deltaB - $desiredDelta);
        });

        $maxCombos = (int) ($this->request->getGet('combo_count') ?? 3);
        if ($maxCombos < 1) {
            $maxCombos = 1;
        }

        $selectedRegimes = $this->selectRegimeCombos($filteredRegimes, $desiredDelta, $maxCombos);

        $activities = [
            [
                'title' => 'Marche rapide',
                'duration' => '30 min',
                'intensity' => 'Moderee',
                'goal' => 'Cardio leger',
            ],
            [
                'title' => 'Renforcement',
                'duration' => '25 min',
                'intensity' => 'Moyenne',
                'goal' => 'Tonus musculaire',
            ],
            [
                'title' => 'Yoga',
                'duration' => '20 min',
                'intensity' => 'Douce',
                'goal' => 'Souplesse',
            ],
            [
                'title' => 'Corde a sauter',
                'duration' => '15 min',
                'intensity' => 'Elevee',
                'goal' => 'Brulage rapide',
            ],
        ];

        $combos = [];
        $activityCount = count($activities);

        foreach ($selectedRegimes as $index => $regimeSlice) {
            $activitySliceCount = $activityCount > 0 ? (1 + ($index % min(3, $activityCount))) : 0;
            $activityOffset = $activityCount > 0 ? ($index % $activityCount) : 0;
            $comboActivities = $activityCount > 0
                ? array_slice(array_merge($activities, $activities), $activityOffset, $activitySliceCount)
                : [];

            $combos[] = [
                'regimes' => $regimeSlice,
                'activities' => $comboActivities,
            ];
        }

        return view('resultats/index', [
            'combos' => $combos,
            'isGold' => $isGold,
            'isAdmin' => $this->isAdminUser(),
            'isConnected' => $this->isUserConnected(),
        ]);
    }

    private function selectRegimeCombos(array $regimes, float $desiredDelta, int $maxCombos): array
    {
        $count = count($regimes);
        if ($count === 0 || $maxCombos <= 0) {
            return [];
        }

        // Filtrer les régimes avec des deltas pertinents (même direction que l'objectif)
        $validRegimes = array_values(array_filter($regimes, function ($regime) use ($desiredDelta) {
            $delta = (float) ($regime['delta_poids'] ?? 0);
            return ($desiredDelta > 0 && $delta > 0) || ($desiredDelta < 0 && $delta < 0);
        }));

        if (empty($validRegimes)) {
            return [];
        }

        // Trier par proximité avec l'objectif (les plus proches d'abord)
        usort($validRegimes, function ($a, $b) use ($desiredDelta) {
            $diffA = abs((float) ($a['delta_poids'] ?? 0) - $desiredDelta);
            $diffB = abs((float) ($b['delta_poids'] ?? 0) - $desiredDelta);
            return $diffA <=> $diffB;
        });

        $results = [];
        $usedSignatures = [];

        // 1. Stratégie gloutonne : meilleur régime individuel
        if (!empty($validRegimes)) {
            $bestSingle = [$validRegimes[0]];
            $this->addUniqueCombo($results, $usedSignatures, $bestSingle, $desiredDelta, $maxCombos);
        }

        // 2. Stratégie de combinaison optimisée : limiter à top N régimes pour éviter explosion combinatoire
        $topN = min(8, count($validRegimes)); // Limiter pour performance O(N²) au lieu de O(2^N)
        $candidates = array_slice($validRegimes, 0, $topN);

        // Générer des combinaisons 2 à 2 (meilleur compromis qualité/performance)
        for ($i = 0; $i < $topN && count($results) < $maxCombos; $i++) {
            for ($j = $i + 1; $j < $topN && count($results) < $maxCombos; $j++) {
                $combo = [$candidates[$i], $candidates[$j]];
                $this->addUniqueCombo($results, $usedSignatures, $combo, $desiredDelta, $maxCombos);
            }
        }

        // 3. Combinaisons 3 à 3 uniquement si nécessaire et avec contrainte stricte
        if (count($results) < $maxCombos && $topN >= 3) {
            for ($i = 0; $i < min(5, $topN) && count($results) < $maxCombos; $i++) {
                for ($j = $i + 1; $j < min(6, $topN) && count($results) < $maxCombos; $j++) {
                    for ($k = $j + 1; $k < min(7, $topN) && count($results) < $maxCombos; $k++) {
                        $combo = [$candidates[$i], $candidates[$j], $candidates[$k]];
                        $sum = (float) ($candidates[$i]['delta_poids'] ?? 0)
                             + (float) ($candidates[$j]['delta_poids'] ?? 0)
                             + (float) ($candidates[$k]['delta_poids'] ?? 0);
                        // Ne garder que si la combinaison est proche de l'objectif
                        if (abs($sum - $desiredDelta) <= abs($desiredDelta * 0.5)) {
                            $this->addUniqueCombo($results, $usedSignatures, $combo, $desiredDelta, $maxCombos);
                        }
                    }
                }
            }
        }

        // 4. Recherche ciblée : compléter avec le régime qui rapproche le plus de l'objectif
        if (count($results) < $maxCombos && count($validRegimes) > 1) {
            foreach ($validRegimes as $regime) {
                if (count($results) >= $maxCombos) break;
                $remaining = $desiredDelta - (float) ($regime['delta_poids'] ?? 0);
                $bestMatch = $this->findBestComplement($validRegimes, $regime, $remaining);
                if ($bestMatch !== null) {
                    $combo = [$regime, $bestMatch];
                    $this->addUniqueCombo($results, $usedSignatures, $combo, $desiredDelta, $maxCombos);
                }
            }
        }

        // Trier les résultats par qualité (différence avec l'objectif, puis par nombre de régimes)
        usort($results, function ($a, $b) use ($desiredDelta) {
            $sumA = $this->sumDeltas($a);
            $sumB = $this->sumDeltas($b);
            $diffA = abs($sumA - $desiredDelta);
            $diffB = abs($sumB - $desiredDelta);

            if ($diffA === $diffB) {
                return count($a) <=> count($b); // Préférer moins de régimes si égalité
            }
            return $diffA <=> $diffB;
        });

        return array_slice($results, 0, $maxCombos);
    }

    private function addUniqueCombo(array &$results, array &$usedSignatures, array $combo, float $desiredDelta, int $maxCombos): void
    {
        if (count($results) >= $maxCombos || empty($combo)) {
            return;
        }

        // Créer une signature unique basée sur les IDs triés
        $ids = array_map(fn($r) => (int) ($r['id'] ?? 0), $combo);
        sort($ids);
        $signature = implode('-', $ids);

        if (isset($usedSignatures[$signature])) {
            return;
        }

        // Vérifier que la combinaison est pertinente (même direction que l'objectif)
        $sum = $this->sumDeltas($combo);
        if (($desiredDelta > 0 && $sum <= 0) || ($desiredDelta < 0 && $sum >= 0)) {
            return;
        }

        $usedSignatures[$signature] = true;
        $results[] = $combo;
    }

    private function sumDeltas(array $regimes): float
    {
        return array_sum(array_map(fn($r) => (float) ($r['delta_poids'] ?? 0), $regimes));
    }

    private function findBestComplement(array $regimes, array $excludeRegime, float $targetDelta): ?array
    {
        $bestMatch = null;
        $bestDiff = PHP_FLOAT_MAX;

        foreach ($regimes as $regime) {
            if ($regime['id'] === $excludeRegime['id']) {
                continue;
            }

            $delta = (float) ($regime['delta_poids'] ?? 0);
            $diff = abs($delta - $targetDelta);

            if ($diff < $bestDiff) {
                $bestDiff = $diff;
                $bestMatch = $regime;
            }
        }

        return $bestMatch;
    }
}
