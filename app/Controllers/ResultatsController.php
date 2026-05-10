<?php

namespace App\Controllers;

use App\Models\RegimeModel;

class ResultatsController extends BaseController
{
    protected $regimeModel;

    public function __construct()
    {
        $this->regimeModel = new RegimeModel();
    }

    public function index()
    {
        $regimes = $this->regimeModel->getAllRegimes();

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
        $regimeCount = count($regimes);

        $comboTotal = max(1, (int) ceil($regimeCount / 2));

        for ($i = 0; $i < $comboTotal; $i++) {
            $regimeOffset = $regimeCount > 0 ? ($i % $regimeCount) : 0;
            $regimeSlice = $regimeCount > 0
                ? array_slice(array_merge($regimes, $regimes), $regimeOffset, min(2, $regimeCount))
                : [];

            $activitySliceCount = $activityCount > 0 ? (1 + ($i % min(3, $activityCount))) : 0;
            $activityOffset = $activityCount > 0 ? ($i % $activityCount) : 0;
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
}
