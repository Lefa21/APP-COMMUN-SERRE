<?php
// controllers/AdminController.php
require_once BASE_PATH . '/controllers/BaseController.php';
// On charge tous les modèles nécessaires
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/Role.php';
// Pour la gestion des logs, on pourrait aussi créer un LogModel

class AdminController extends BaseController {
    
    private $userModel;
    private $roleModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->roleModel = new Role();
    }
    
    public function users() {
        $this->requireAdmin();
        
        // Les appels sont simples et directs vers les modèles
        $users = $this->userModel->findAllWithProfiles();
        $roles = $this->roleModel->findAll();
        $stats = $this->userModel->getStats();
        
        $this->render('admin/users', [
            'users' => $users,
            'roles' => $roles,
            'stats' => $stats
        ]);
    }
    
    public function manageUser() {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectBeforeRender('?controller=admin&action=users');
        }
        
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'create':
                $this->createUser();
                break;
            case 'update':
                $this->updateUser();
                break;
            case 'delete': // Note: deleteUser() est maintenant softDelete() dans le modèle
                $this->softDeleteUser();
                break;
            case 'reset_password':
                $this->resetUserPassword();
                break;
            case 'toggle_status':
                $this->toggleUserStatus();
                break;
            default:
                $this->setMessage('Action non reconnue', 'error');
        }
        
        $this->redirectBeforeRender('?controller=admin&action=users');
    }
    
    public function bulkActions() {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectBeforeRender('?controller=admin&action=users');
        }
        
        $action = $_POST['bulk_action'] ?? '';
        $userIds = $_POST['user_ids'] ?? [];
        
        // Empêcher l'auto-modification
        $userIds = array_filter($userIds, fn($id) => $id !== $_SESSION['user_id']);
        
        if (empty($userIds)) {
            $this->setMessage('Aucun utilisateur valide sélectionné pour l\'action.', 'error');
            $this->redirectBeforeRender('?controller=admin&action=users');
        }
        
        switch ($action) {
            case 'activate':
                $count = $this->userModel->activateMultiple($userIds);
                $this->setMessage("$count utilisateur(s) activé(s)", 'success');
                break;
            case 'deactivate':
                $count = $this->userModel->deactivateMultiple($userIds);
                $this->setMessage("$count utilisateur(s) désactivé(s)", 'success');
                break;
            case 'delete':
                $count = $this->userModel->deactivateMultiple($userIds); // La suppression en lot est une désactivation
                $this->setMessage("$count utilisateur(s) supprimé(s)", 'success');
                break;
            case 'reset_passwords':
                // Cette logique reste dans le contrôleur car elle gère la session
                $resetPasswords = [];
                foreach ($userIds as $userId) {
                    $newPassword = $this->generateRandomPassword();
                    if ($this->userModel->updatePassword($userId, $newPassword)) {
                        $resetPasswords[$userId] = $newPassword;
                    }
                }
                $_SESSION['reset_passwords'] = $resetPasswords;
                $this->setMessage(count($resetPasswords) . " mot(s) de passe réinitialisé(s)", 'success');
                break;
        }
        
        $this->redirectBeforeRender('?controller=admin&action=users');
    }

        public function exportUsers() {
        $this->requireAdmin();

        $format = strtolower($_GET['format'] ?? 'csv');
        $users = $this->userModel->findAllWithProfiles();

        if ($format === 'json') {
            $this->exportUsersToJson($users);
        } else {
            $this->exportUsersToCsv($users);
        }
    }

    /**
     * Méthode privée pour générer un fichier CSV avec la liste des utilisateurs.
     * @param array $users
     */
    private function exportUsersToCsv($users) {
        $filename = "export_utilisateurs_" . date('Y-m-d') . ".csv";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Écriture de la ligne d'en-tête
        fputcsv($output, [
            'ID', 
            'Nom d\'utilisateur', 
            'Email', 
            'Prénom', 
            'Nom', 
            'Rôle', 
            'Statut'
        ]);

        // Écriture des données pour chaque utilisateur
        foreach ($users as $user) {
            fputcsv($output, [
                $user['id_user'],
                $user['username'],
                $user['email'],
                $user['first_name'],
                $user['last_name'],
                $user['role_name'],
                ($user['is_active'] ?? 1) ? 'Actif' : 'Inactif'
            ]);
        }

        fclose($output);
        exit();
    }

    /**
     * Méthode privée pour générer un fichier JSON avec la liste des utilisateurs.
     * @param array $users
     */
    private function exportUsersToJson($users) {
        $filename = "export_utilisateurs_" . date('Y-m-d') . ".json";

        // Préparation de la structure des données pour l'export
        $exportData = [
            'export_date' => date('c'), // Format ISO 8601
            'user_count' => count($users),
            'users' => []
        ];

        foreach ($users as $user) {
            // On ne sélectionne que les données pertinentes pour l'export
            $exportData['users'][] = [
                'id' => $user['id_user'],
                'username' => $user['username'],
                'email' => $user['email'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'role' => $user['role_name'],
                'status' => ($user['is_active'] ?? 1) ? 'Actif' : 'Inactif'
            ];
        }

        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        echo json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }

    // --- Méthodes privées du contrôleur (logique applicative) ---

private function createUser() {
        // 1. Récupération et validation des données POST
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $roleId = (int)($_POST['role_id'] ?? 1); // Rôle 'etudiant' par défaut
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');

        if (empty($username) || !$this->validateEmail($email) || strlen($password) < 6) {
            $this->setMessage('Données de création invalides.', 'error');
            return;
        }

        if ($this->userModel->exists($username, $email)) {
            $this->setMessage('Ce nom d\'utilisateur ou cet email est déjà pris.', 'error');
            return;
        }

        // 2. Appel unique au modèle avec toutes les informations
        // La méthode du modèle gère maintenant l'assignation du rôle.
        $userId = $this->userModel->create(
            $username,
            $email,
            $password,
            $roleId, // On passe le rôle directement
            $firstName,
            $lastName
        );

        // 3. Affichage du message de résultat
        if ($userId) {
            $this->setMessage('Utilisateur créé avec succès.', 'success');
        } else {
            $this->setMessage('Erreur lors de la création de l\'utilisateur.', 'error');
        }
    }
    
    private function updateUser() {
        $userId = $_POST['user_id'] ?? '';
        $data = [
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'role_id' => (int)($_POST['role_id'] ?? 1),
        ];

        if (empty($userId) || empty($data['username']) || !$this->validateEmail($data['email'])) {
            $this->setMessage('Données de mise à jour invalides', 'error');
            return;
        }
        
        if ($this->userModel->update($userId, $data)) {
            $this->setMessage('Utilisateur mis à jour', 'success');
        } else {
            $this->setMessage('Erreur lors de la mise à jour', 'error');
        }
    }

    private function softDeleteUser() {
        $userId = $_POST['user_id'] ?? '';
        if ($userId && $userId !== $_SESSION['user_id']) {
            if ($this->userModel->softDelete($userId)) {
                $this->setMessage('Utilisateur supprimé (soft delete)', 'success');
            } else {
                $this->setMessage('Erreur lors de la suppression', 'error');
            }
        } else {
            $this->setMessage('Impossible de supprimer cet utilisateur', 'error');
        }
    }

    private function resetUserPassword() {
        $userId = $_POST['user_id'] ?? '';
        if ($userId) {
            $newPassword = $this->generateRandomPassword();
            if ($this->userModel->updatePassword($userId, $newPassword)) {
                // Stocker le mdp en session pour l'afficher une seule fois
                $_SESSION['reset_passwords'] = [$userId => $newPassword];
                $this->setMessage('Mot de passe réinitialisé', 'success');
            } else {
                $this->setMessage('Erreur lors de la réinitialisation', 'error');
            }
        }
    }
    
    private function toggleUserStatus() {
        $userId = $_POST['user_id'] ?? '';
        if (!$userId || $userId === $_SESSION['user_id']) {
            $this->setMessage('Action impossible', 'error');
            return;
        }

        $user = $this->userModel->findById($userId);
        if (!$user) {
            $this->setMessage('Utilisateur non trouvé', 'error');
            return;
        }

        if ($user['deleted_at'] && $user['deleted_at'] !== '0000-00-00 00:00:00') {
            if ($this->userModel->reactivate($userId)) {
                $this->setMessage('Utilisateur réactivé', 'success');
            } else {
                $this->setMessage('Erreur de réactivation', 'error');
            }
        } else {
            if ($this->userModel->softDelete($userId)) {
                $this->setMessage('Utilisateur désactivé', 'success');
            } else {
                $this->setMessage('Erreur de désactivation', 'error');
            }
        }
    }

    private function generateRandomPassword($length = 8) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $password;
    }
}