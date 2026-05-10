<?php

namespace App\Controllers;

use App\Models\RegimeModel;
use App\Models\User;

class ResultatsController extends BaseController
{
    protected $regimeModel;
    protected $userModel;
    protected int $fixedUserId = 1;

    public function __construct()
    {
        $this->regimeModel = new RegimeModel();
        $this->userModel = new User();
    }

    public function index()
    {
        $regimes = $this->regimeModel->getAllRegimes();

        $user = $this->userModel->getById($this->fixedUserId);
        $taille = isset($user['taille']) ? (float) $user['taille'] : 0.0;
        $poids = isset($user['poids']) ? (float) $user['poids'] : 0.0;

        $objectifType = (string) ($this->request->getGet('objectif') ?? 'ideal');
        $objectifPoids = (float) ($this->request->getGet('objectif_poids') ?? 0);

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
        ]);
    }

    private function selectRegimeCombos(array $regimes, float $desiredDelta, int $maxCombos): array
    {
        $combos = [];
        $count = count($regimes);

        if ($count === 0) {
            return $combos;
        }

        $targets = $desiredDelta;

        $generate = function (int $start, array $current, float $sum) use (&$generate, $regimes, $count, $desiredDelta, &$combos) {
            if (!empty($current)) {
                $combos[] = [
                    'regimes' => $current,
                    'sum' => $sum,
                    'diff' => abs($sum - $desiredDelta),
                ];
            }

            for ($i = $start; $i < $count; $i++) {
                $delta = (float) ($regimes[$i]['delta_poids'] ?? 0);
                $next = $current;
                $next[] = $regimes[$i];
                $generate($i + 1, $next, $sum + $delta);
            }
        };

        $generate(0, [], 0.0);

        usort($combos, function ($a, $b) {
            if ($a['diff'] === $b['diff']) {
                return count($a['regimes']) <=> count($b['regimes']);
            }
            return $a['diff'] <=> $b['diff'];
        });

        $unique = [];
        $seen = [];

        foreach ($combos as $combo) {
            $ids = array_map(static function ($regime) {
                return (string) ($regime['id'] ?? '');
            }, $combo['regimes']);
            $key = implode('-', $ids);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $unique[] = $combo['regimes'];
            if (count($unique) >= $maxCombos) {
                break;
            }
        }

        return $unique;
    }
}
