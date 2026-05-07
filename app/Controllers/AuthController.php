<?php

namespace App\Controllers;

use App\Models\User;
use CodeIgniter\Controller;

class AuthController extends Controller
{
    protected $helpers = ['url', 'form'];
    protected $session;
    protected $userModel;

    public function __construct()
    {
        $this->session   = session();
        $this->userModel = new User();
    }

    public function showStep1()
    {
        $this->session->remove('registration');

        return view('inscription/register_step1');
    }

    public function handleStep1()
    {
        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('/inscription/step1');
        }

        $nom    = $this->request->getPost('nom');
        $prenom = $this->request->getPost('prenom');
        $email  = $this->request->getPost('email');
        $genre  = $this->request->getPost('genre');

        // Validation 1
        $validationRules = [
            'nom'    => 'required|string|min_length[2]|max_length[255]',
            'prenom' => 'required|string|min_length[2]|max_length[255]',
            'email'  => 'required|valid_email|is_unique[users.email]',
            'genre'  => 'required|in_list[H,F]',
        ];

        $validationMessages = [
            'nom'    => [
                'required'   => 'Le nom est requis',
                'min_length' => 'Le nom doit avoir au moins 2 caractères',
            ],
            'prenom' => [
                'required'   => 'Le prénom est requis',
                'min_length' => 'Le prénom doit avoir au moins 2 caractères',
            ],
            'email'  => [
                'required'    => 'L\'email est requis',
                'valid_email' => 'Email invalide',
                'is_unique'   => 'Cet email est déjà utilisé',
            ],
            'genre'  => [
                'required' => 'Le genre est requis',
            ],
        ];

        if (!$this->validate($validationRules, $validationMessages)) {
            return redirect()->back()->withInput()->with('validation_errors', $this->validator->getErrors());
        }

        // Mise en session etape 1
        $registrationData = [
            'nom'    => trim($nom),
            'prenom' => trim($prenom),
            'email'  => strtolower(trim($email)),
            'genre'  => $genre,
        ];

        $this->session->set('registration', $registrationData);

        // Redirect step 2
        return redirect()->to('/inscription/step2');
    }

    public function showStep2()
    {
        // Step 1: nety ve ?
        if (!$this->session->has('registration')) {
            return redirect()->to('/inscription/step1')->with('warning', 'Veuillez compléter l\'étape 1 d\'abord');
        }

        $data['registration'] = $this->session->get('registration');

        return view('inscription/register_step2', $data);
    }


    public function handleStep2()
    {
        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('/inscription/step2');
        }

        if (!$this->session->has('registration')) {
            return redirect()->to('/inscription/step1')->with('error', 'Session expirée. Veuillez recommencer.');
        }

        $registrationData = $this->session->get('registration');

        $taille   = $this->request->getPost('taille');
        $poids    = $this->request->getPost('poids');
        $mdp      = $this->request->getPost('mdp');
        $mdpConf  = $this->request->getPost('mdp_confirm');

        $validationRules = [
            'taille'       => 'required|numeric|greater_than[50]|less_than[250]',
            'poids'        => 'required|numeric|greater_than[20]|less_than[300]',
            'mdp'          => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/]',
            'mdp_confirm'  => 'required|matches[mdp]',
        ];

        $validationMessages = [
            'taille'  => [
                'required'     => 'La taille est requise',
                'numeric'      => 'La taille doit être un nombre',
                'greater_than' => 'La taille doit être supérieure à 50 cm',
                'less_than'    => 'La taille doit être inférieure à 250 cm',
            ],
            'poids'   => [
                'required'     => 'Le poids est requis',
                'numeric'      => 'Le poids doit être un nombre',
                'greater_than' => 'Le poids doit être supérieur à 20 kg',
                'less_than'    => 'Le poids doit être inférieur à 300 kg',
            ],
            'mdp'    => [
                'required'     => 'Le mot de passe est requis',
                'min_length'   => 'Le mot de passe doit avoir au moins 8 caractères',
                'regex_match'  => 'Le mot de passe doit contenir une majuscule, une minuscule et un chiffre',
            ],
            'mdp_confirm' => [
                'required' => 'Veuillez confirmer le mot de passe',
                'matches'  => 'Les mots de passe ne correspondent pas',
            ],
        ];

        if (!$this->validate($validationRules, $validationMessages)) {
            return redirect()->back()->withInput()->with('validation_errors', $this->validator->getErrors());
        }

        $userData = array_merge($registrationData, [
            'taille' => floatval($taille),
            'poids'  => floatval($poids),
            'mdp'    => password_hash($mdp, PASSWORD_BCRYPT),
        ]);

        //IMC
        $userData['imc'] = $this->userModel->calculateIMC($userData['poids'], $userData['taille']);

        // Insertion database
        try {
            if ($this->userModel->insert($userData)) {
                $this->session->remove('registration');

                return redirect()->to('/')->with('success', 'Inscription réussie! Vous pouvez maintenant vous connecter.');
            } else {
                return redirect()->back()->withInput()->with('error', 'Erreur lors de l\'inscription. Veuillez réessayer.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Registration error: ' . $e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Une erreur est survenue lors de l\'inscription.');
        }
    }
}
