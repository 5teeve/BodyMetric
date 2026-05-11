<?php

namespace App\Controllers;

use App\Models\User;
use CodeIgniter\Controller;

class AuthController extends Controller
{
    protected $helpers = ['url', 'form', 'security'];
    protected $session;
    protected $userModel;

    protected array $step1Rules = [
        'nom'    => 'required|string|min_length[2]|max_length[255]|regex_match[/^[a-zA-Z\s\-\']+$/]',
        'prenom' => 'required|string|min_length[2]|max_length[255]|regex_match[/^[a-zA-Z\s\-\']+$/]',
        'email'  => 'required|valid_email|is_unique[users.email]',
        'genre'  => 'required|in_list[M,F]',
    ];

    protected array $step2Rules = [
        'taille' => 'required|numeric|greater_than[30]|less_than[350]',
        'poids'  => 'required|numeric|greater_than[20]|less_than[500]',
        'mdp'    => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/]',
    ];

    protected array $validationMessages = [
        'nom'    => [
            'required'    => 'Le nom est requis',
            'min_length'  => 'Le nom doit avoir au moins 2 caractères',
            'max_length'  => 'Le nom ne doit pas dépasser 255 caractères',
            'regex_match' => 'Le nom contient des caractères invalides',
        ],
        'prenom' => [
            'required'    => 'Le prénom est requis',
            'min_length'  => 'Le prénom doit avoir au moins 2 caractères',
            'max_length'  => 'Le prénom ne doit pas dépasser 255 caractères',
            'regex_match' => 'Le prénom contient des caractères invalides',
        ],
        'email'  => [
            'required'    => 'L\'email est requis',
            'valid_email' => 'Email invalide',
            'is_unique'   => 'Cet email est déjà utilisé',
        ],
        'genre'  => [
            'required' => 'Le genre est requis',
            'in_list'  => 'Genre invalide',
        ],
        'taille'  => [
            'required'     => 'La taille est requise',
            'numeric'      => 'La taille doit être un nombre',
            'greater_than' => 'La taille doit être supérieure à 30 cm',
            'less_than'    => 'La taille doit être inférieure à 350 cm',
        ],
        'poids'   => [
            'required'     => 'Le poids est requis',
            'numeric'      => 'Le poids doit être un nombre',
            'greater_than' => 'Le poids doit être supérieur à 20 kg',
            'less_than'    => 'Le poids doit être inférieur à 500 kg',
        ],
        'mdp'    => [
            'required'    => 'Le mot de passe est requis',
            'min_length'  => 'Le mot de passe doit avoir au moins 8 caractères',
            'regex_match' => 'Le mot de passe doit contenir une majuscule, une minuscule et un chiffre',
        ],
    ];

    public function __construct()
    {
        $this->session   = session();
        $this->userModel = new User();
    }

    public function showLogin()
    {
        if ($this->session->has('user_id')) {
            return redirect()->to('/');
        }

        return view('auth/login');
    }

    public function handleLogin()
    {
        if (!$this->request->is('post')) {
            return redirect()->to('/connexion');
        }

        $rules = [
            'email' => 'required|valid_email',
            'mdp'   => 'required',
        ];

        $messages = [
            'email' => [
                'required'    => 'L\'email est requis',
                'valid_email' => 'Email invalide',
            ],
            'mdp'   => [
                'required' => 'Le mot de passe est requis',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Veuillez corriger les erreurs ci-dessous')
                ->with('validation_errors', $this->validator->getErrors());
        }

        $email = strtolower(trim((string) $this->request->getPost('email')));
        $mdp   = (string) $this->request->getPost('mdp');

        $user = $this->userModel
            ->select('id, nom, prenom, email, mdp, wallet, is_gold')
            ->where('email', $email)
            ->first();

        if (!$user || !password_verify($mdp, (string) $user['mdp'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Email ou mot de passe incorrect.');
        }

        $this->session->set([
            'user_id'    => $user['id'],
            'user_prenom'=> $user['prenom'] ?? '',
            'user_email' => $user['email'],
            'user_nom'   => $user['nom'],
            'wallet'     => (float) ($user['wallet'] ?? 0),
            'is_gold'    => (int) ($user['is_gold'] ?? 0),
        ]);

        // Redirection intelligente après connexion
        // Vérifier si profil complet
        $fullUser = $this->userModel->getById($user['id']);
        if (empty($fullUser['taille']) || empty($fullUser['poids'])) {
            return redirect()->to('/profil')
                ->with('warning', 'Veuillez compléter votre profil pour accéder aux fonctionnalités.');
        }

        // Si objectif non défini → objectif
        if (empty($fullUser['objectif'])) {
            return redirect()->to('/objectif');
        }

        // Tout est complet → résultats
        return redirect()->to('/resultats');
    }

    public function logout()
    {
        $this->session->destroy();

        return redirect()->to('/connexion')->with('success', 'Déconnexion réussie.');
    }

    public function showStep1()
    {
        // Nettoyer la session si on revient au début
        $this->session->remove('registration');
        return view('inscription/register_step1');
        $data['registration'] = $this->session->get('registration');
        $data['isAdmin'] = false;
        $data['isConnected'] = false;
        return view('inscription/register_step1', $data);
    }

    public function handleStep1()
    {
        if (!$this->request->is('post')) {
            return redirect()->to('/inscription/step1');
        }

        // Validation
        if (!$this->validate($this->step1Rules, $this->validationMessages)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Veuillez corriger les erreurs ci-dessous')
                ->with('validation_errors', $this->validator->getErrors());
        }

        // Nettoyage et stockage des données
        $registrationData = [
            'nom'    => htmlspecialchars(trim($this->request->getPost('nom'))),
            'prenom' => htmlspecialchars(trim($this->request->getPost('prenom'))),
            'email'  => strtolower(filter_var(trim($this->request->getPost('email')), FILTER_SANITIZE_EMAIL)),
            'genre'  => $this->request->getPost('genre'),
        ];

        $this->session->set('registration', $registrationData);

        // Force redirect avec exit pour éviter les problèmes de buffer
        return redirect()->to('/inscription/step2')->withCookies();
    }

    public function showStep2()
    {
        if (!$this->session->has('registration')) {
            return redirect()->to('/inscription/step1')
                ->with('error', 'Veuillez compléter l\'étape 1 d\'abord');
        }

        $data['registration'] = $this->session->get('registration');
        $data['isAdmin'] = false;
        $data['isConnected'] = false;
        return view('inscription/register_step2', $data);
    }

    public function handleStep2()
    {
        if (!$this->request->is('post')) {
            return redirect()->to('/inscription/step2');
        }

        if (!$this->session->has('registration')) {
            return redirect()->to('/inscription/step1')
                ->with('error', 'Session expirée. Veuillez recommencer.');
        }

        $registrationData = $this->session->get('registration');

        // Validation
        if (!$this->validate($this->step2Rules, $this->validationMessages)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Veuillez corriger les erreurs ci-dessous')
                ->with('validation_errors', $this->validator->getErrors());
        }

        // Préparation des données utilisateur
        $userData = array_merge($registrationData, [
            'taille' => (float) $this->request->getPost('taille'),
            'poids'  => (float) $this->request->getPost('poids'),
            'mdp'    => password_hash($this->request->getPost('mdp'), PASSWORD_BCRYPT),
            'imc'    => $this->calculateIMC(
                (float) $this->request->getPost('poids'),
                (float) $this->request->getPost('taille')
            )
        ]);

        // Insertion avec transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $userId = $this->userModel->insert($userData);

            if (!$userId) {
                throw new \RuntimeException('Échec de l\'insertion utilisateur');
            }

            $db->transComplete();
            $this->session->remove('registration');

            // Auto-login après inscription
            $this->session->set([
                'user_id'     => $userId,
                'user_prenom' => $userData['prenom'] ?? '',
                'user_email'  => $userData['email'],
                'user_nom'    => $userData['nom'],
                'wallet'      => 0.0,
                'is_gold'     => 0,
            ]);

            // Redirection vers choix d'objectif après inscription
            return redirect()->to('/objectif')
                ->with('success', 'Inscription réussie ! Choisissez maintenant votre objectif.');

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', '[Auth] Registration failed: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.');
        }
    }

    protected function calculateIMC(float $poids, float $taille): float
    {
        $tailleEnMetre = $taille / 100;
        return round($poids / ($tailleEnMetre ** 2), 2);
    }

    public function calculateImcAjax()
    {
        $taille = $this->request->getGet('taille');
        $poids  = $this->request->getGet('poids');

        if (!is_numeric($taille) || !is_numeric($poids) || $taille <= 0 || $poids <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'error'   => 'Valeurs invalides'
            ])->setStatusCode(400);
        }

        $imc = $this->calculateIMC((float) $poids, (float) $taille);

        // Déterminer la catégorie
        $category = 'unknown';
        $color    = '#666';

        if ($imc < 18.5) {
            $category = 'underweight';
            $color    = '#3b82f6';
        } elseif ($imc < 25) {
            $category = 'normal';
            $color    = '#22c55e';
        } elseif ($imc < 30) {
            $category = 'overweight';
            $color    = '#eab308';
        } else {
            $category = 'obese';
            $color    = '#ef4444';
        }

        return $this->response->setJSON([
            'success'  => true,
            'imc'      => $imc,
            'category' => $category,
            'color'    => $color,
            'label'    => match($category) {
                'underweight' => 'Maigreur',
                'normal'      => 'Normal',
                'overweight'  => 'Surpoids',
                'obese'       => 'Obésité',
                default       => 'Inconnu'
            }
        ]);
    }
}
