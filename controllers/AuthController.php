<?php
// controllers/AuthController.php
require_once BASE_PATH . '/controllers/BaseController.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/Team.php';

class AuthController extends BaseController {

    private $userModel;
    private $teamModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->teamModel = new Team();
    }
    
    public function login() {
        if ($this->isLoggedIn()) {
            $this->redirect('?controller=home');
        }
        
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (empty($username) || empty($password)) {
                $error = 'Veuillez remplir tous les champs';
            } else {
                // Utilisation du modèle pour l'authentification
                $user = $this->userModel->authenticate($username, $password);
                
                if ($user) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['team_id'] = $user['team_id'];
                    
                    $this->redirect('?controller=home');
                } else {
                    $error = 'Identifiants incorrects ou compte inactif';
                }
            }
        }
        
        $this->render('auth/login', ['error' => $error]);
    }
    
    public function register() {
        if ($this->isLoggedIn()) {
            $this->redirect('?controller=home');
        }
        
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $teamId = (int)($_POST['team_id'] ?? 1);
            
            // Validation (inchangée)
            if (empty($username) || empty($email) || empty($password)) {
                $error = 'Veuillez remplir tous les champs';
            } elseif ($password !== $confirmPassword) {
                $error = 'Les mots de passe ne correspondent pas';
            } elseif (strlen($password) < 6) {
                $error = 'Le mot de passe doit contenir au moins 6 caractères';
            } elseif (!$this->validateEmail($email)) {
                $error = 'Email invalide';
            } else {
                // Utilisation du modèle pour vérifier si l'utilisateur existe
                if ($this->userModel->exists($username, $email)) {
                    $error = 'Un utilisateur avec ce nom ou cet email existe déjà';
                } else {
                    // Utilisation du modèle pour créer l'utilisateur
                    if ($this->userModel->create($username, $email, $password, $teamId)) {
                        $success = 'Compte créé avec succès. Vous pouvez maintenant vous connecter.';
                    } else {
                        $error = 'Erreur lors de la création du compte';
                    }
                }
            }
        }
        
        // Utilisation du modèle pour récupérer les équipes
        $teams = $this->teamModel->findAll();
        
        $this->render('auth/register', [
            'error' => $error, 
            'success' => $success,
            'teams' => $teams
        ]);
    }
    
    public function logout() {
        session_destroy();
        $this->redirect('?controller=auth&action=login');
    }
}