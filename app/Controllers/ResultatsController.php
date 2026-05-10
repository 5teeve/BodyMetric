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

        foreach ($regimes as $index => $regime) {
            $count = $activityCount > 0 ? (1 + ($index % min(3, $activityCount))) : 0;
            $offset = $activityCount > 0 ? ($index % $activityCount) : 0;

            $comboActivities = $activityCount > 0
                ? array_slice(array_merge($activities, $activities), $offset, $count)
                : [];

            $combos[] = [
                'regime' => $regime,
                'activities' => $comboActivities,
            ];
        }

        return view('resultats/index', [
            'combos' => $combos,
        ]);
    }
}
