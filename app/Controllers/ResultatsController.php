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

        return view('resultats/index', [
            'regimes' => $regimes,
            'activities' => $activities,
        ]);
    }
}
