<?php
// controllers/AuthController.php
require_once BASE_PATH . '/controllers/BaseController.php';

class AuthController extends BaseController {
    
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
                $user = $this->authenticateUser($username, $password);
                if ($user) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['team_id'] = $user['team_id'];
                    
                    $this->redirect('?controller=home');
                } else {
                    $error = 'Identifiants incorrects';
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
            
            // Validation
            if (empty($username) || empty($email) || empty($password)) {
                $error = 'Veuillez remplir tous les champs';
            } elseif ($password !== $confirmPassword) {
                $error = 'Les mots de passe ne correspondent pas';
            } elseif (strlen($password) < 6) {
                $error = 'Le mot de passe doit contenir au moins 6 caractères';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Email invalide';
            } else {
                // Vérifier si l'utilisateur existe déjà
                if ($this->userExists($username, $email)) {
                    $error = 'Un utilisateur avec ce nom ou cet email existe déjà';
                } else {
                    if ($this->createUser($username, $email, $password, $teamId)) {
                        $success = 'Compte créé avec succès. Vous pouvez maintenant vous connecter.';
                    } else {
                        $error = 'Erreur lors de la création du compte';
                    }
                }
            }
        }
        
        // Récupérer les équipes pour le formulaire
        $teams = $this->getTeams();
        
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
    
    private function authenticateUser($username, $password) {
        $stmt = $this->db->prepare("
            SELECT u.id_user as id, u.username, u.password, r.name as role_name, 1 as team_id
            FROM user u
            LEFT JOIN role r ON u.role_id = r.id 
            WHERE u.username = ?
        ");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Adapter le rôle au système
            $user['role'] = ($user['role_name'] === 'admin') ? 'admin' : 'user';
            return $user;
        }
        
        return false;
    }
    
    private function userExists($username, $email) {
        $stmt = $this->db->prepare("
            SELECT id_user FROM user WHERE username = ?
        ");
        $stmt->execute([$username]);
        return $stmt->fetch() !== false;
    }
    
    private function createUser($username, $email, $password, $teamId) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $userId = $this->generateUUID();
        
        $stmt = $this->db->prepare("
            INSERT INTO user (id_user, username, password, role_id) 
            VALUES (?, ?, ?, 1)
        ");
        
        return $stmt->execute([$userId, $username, $hashedPassword]);
    }
    
    private function generateUUID() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
    
    private function getTeams() {
        $stmt = $this->db->query("SELECT id, name FROM teams ORDER BY name");
        return $stmt->fetchAll();
    }
}
?>