<?php

namespace App\Controllers;

use App\Models\ActiviteModel;
use App\Models\User;

class SuggestionController extends BaseController
{
    protected $activiteModel;
    protected $userModel;
    protected int $fixedUserId = 1;

    public function __construct()
    {
        $this->activiteModel = new ActiviteModel();
        $this->userModel = new User();
    }

    public function index()
    {
        $objectifType = (string) ($this->request->getGet('objectif') ?? 'maintenir');
        $nombreActivites = (int) ($this->request->getGet('count') ?? 4);

        if ($nombreActivites < 1) {
            $nombreActivites = 4;
        }

        $user = $this->userModel->getById($this->fixedUserId);
        $userProfile = $this->buildUserProfile($user);

        $suggestions = $this->getSuggestedActivites($objectifType, $userProfile, $nombreActivites);

        return $this->response->setJSON([
            'success' => true,
            'objectif' => $objectifType,
            'user_profile' => $userProfile,
            'suggestions' => $suggestions,
            'count' => count($suggestions),
        ]);
    }

    public function view()
    {
        $objectifType = (string) ($this->request->getGet('objectif') ?? 'maintenir');
        $nombreActivites = (int) ($this->request->getGet('count') ?? 4);

        $user = $this->userModel->getById($this->fixedUserId);
        $userProfile = $this->buildUserProfile($user);

        $suggestions = $this->getSuggestedActivites($objectifType, $userProfile, $nombreActivites);

        return view('suggestions/index', [
            'suggestions' => $suggestions,
            'objectif' => $objectifType,
            'userProfile' => $userProfile,
            'isGold' => isset($user['is_gold']) && (int) $user['is_gold'] === 1,
            'isAdmin' => $this->isAdminUser($this->fixedUserId),
            'isConnected' => $this->isUserConnected($this->fixedUserId),
        ]);
    }

    private function buildUserProfile(?array $user): array
    {
        $taille = isset($user['taille']) ? (float) $user['taille'] : 170.0;
        $poids = isset($user['poids']) ? (float) $user['poids'] : 70.0;
        $imc = isset($user['imc']) ? (float) $user['imc'] : 0.0;
        $genre = isset($user['genre']) ? $user['genre'] : 'M';

        if ($imc === 0.0 && $taille > 0 && $poids > 0) {
            $imc = $this->userModel->calculateIMC($poids, $taille);
        }

        $categorieImc = $this->categoriserIMC($imc);

        return [
            'taille' => $taille,
            'poids' => $poids,
            'imc' => $imc,
            'genre' => $genre,
            'categorie_imc' => $categorieImc,
        ];
    }

    private function categoriserIMC(float $imc): string
    {
        if ($imc < 18.5) {
            return 'insuffisance_ponderale';
        } elseif ($imc < 25) {
            return 'normal';
        } elseif ($imc < 30) {
            return 'surpoids';
        } else {
            return 'obesite';
        }
    }

    private function getSuggestedActivites(string $objectif, array $userProfile, int $count): array
    {
        $activites = $this->activiteModel->getActivitesByObjectif($objectif);

        if (empty($activites)) {
            $activites = $this->activiteModel->getAllActivites();
        }

        $activitesPonderees = $this->pondererActivites($activites, $userProfile, $objectif);

        usort($activitesPonderees, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        $selectionnees = array_slice($activitesPonderees, 0, $count);

        return array_map(function ($item) {
            $activite = $item['activite'];
            return [
                'id' => $activite['id'],
                'nom' => $activite['nom'],
                'type' => $activite['type'],
                'intensite' => $activite['intensite'],
                'duree_estimee' => $item['duree_ajustee'],
                'calories_estimees' => $item['calories_estimees'],
                'objectif_associe' => $activite['objectif'],
                'description' => $activite['description'],
                'score_relevance' => round($item['score'], 2),
            ];
        }, $selectionnees);
    }

    private function pondererActivites(array $activites, array $userProfile, string $objectif): array
    {
        $result = [];

        $facteursDuree = [
            'insuffisance_ponderale' => 0.8,
            'normal' => 1.0,
            'surpoids' => 1.2,
            'obesite' => 1.3,
        ];

        $facteurDuree = $facteursDuree[$userProfile['categorie_imc']] ?? 1.0;

        foreach ($activites as $activite) {
            $score = $this->calculerScore($activite, $userProfile, $objectif);
            $dureeAjustee = (int) round($activite['duree_base'] * $facteurDuree);
            $caloriesEstimees = (int) round($activite['calories_min'] * $dureeAjustee * $this->getIntensiteMultiplier($activite['intensite']));

            $result[] = [
                'activite' => $activite,
                'score' => $score,
                'duree_ajustee' => $dureeAjustee,
                'calories_estimees' => $caloriesEstimees,
            ];
        }

        return $result;
    }

    private function calculerScore(array $activite, array $userProfile, string $objectif): float
    {
        $score = 50.0;

        if ($activite['objectif'] === $objectif) {
            $score += 30.0;
        }

        $score += $this->getIntensiteScore($activite['intensite'], $userProfile['categorie_imc']);

        $score += $this->getTypeScore($activite['type'], $objectif);

        if ($userProfile['imc'] > 30 && $activite['type'] === 'cardio' && $activite['intensite'] !== 'elevee') {
            $score += 10;
        }

        if ($userProfile['imc'] < 18.5 && $activite['type'] === 'musculation') {
            $score += 15;
        }

        return $score;
    }

    private function getIntensiteScore(string $intensite, string $categorieImc): float
    {
        $scores = [
            'insuffisance_ponderale' => [
                'faible' => 5,
                'moderee' => 10,
                'moyenne' => 15,
                'elevee' => 20,
            ],
            'normal' => [
                'faible' => 5,
                'moderee' => 10,
                'moyenne' => 10,
                'elevee' => 10,
            ],
            'surpoids' => [
                'faible' => 15,
                'moderee' => 12,
                'moyenne' => 8,
                'elevee' => 5,
            ],
            'obesite' => [
                'faible' => 20,
                'moderee' => 15,
                'moyenne' => 5,
                'elevee' => 0,
            ],
        ];

        return $scores[$categorieImc][$intensite] ?? 5;
    }

    private function getTypeScore(string $type, string $objectif): float
    {
        $scores = [
            'reduire' => [
                'cardio' => 15,
                'sport' => 10,
                'musculation' => 5,
                'flexibilite' => 5,
            ],
            'augmenter' => [
                'musculation' => 20,
                'sport' => 10,
                'cardio' => 5,
                'flexibilite' => 5,
            ],
            'maintenir' => [
                'flexibilite' => 15,
                'cardio' => 10,
                'musculation' => 10,
                'sport' => 10,
            ],
        ];

        return $scores[$objectif][$type] ?? 5;
    }

    private function getIntensiteMultiplier(string $intensite): float
    {
        $multipliers = [
            'faible' => 0.8,
            'moderee' => 1.0,
            'moyenne' => 1.2,
            'elevee' => 1.5,
        ];

        return $multipliers[$intensite] ?? 1.0;
    }

    public function getActivitesByType(string $type)
    {
        $activites = $this->activiteModel->getActivitesByType($type);

        return $this->response->setJSON([
            'success' => true,
            'type' => $type,
            'activites' => $activites,
        ]);
    }

    public function getActivitesByIntensite(string $intensite)
    {
        $activites = $this->activiteModel->getActivitesByIntensite($intensite);

        return $this->response->setJSON([
            'success' => true,
            'intensite' => $intensite,
            'activites' => $activites,
        ]);
    }
}
