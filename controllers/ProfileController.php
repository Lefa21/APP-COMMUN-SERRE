<?php
// controllers/ProfileController.php
require_once BASE_PATH . '/controllers/BaseController.php';
require_once BASE_PATH . '/models/User.php';
// Note: Le modèle Notification est maintenant intégré dans le modèle User pour plus de simplicité.

class ProfileController extends BaseController {
    
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }
    
    /**
     * Affiche la page principale du profil utilisateur.
     */
    public function index() {
        $this->requireLogin();
        
        $userId = $_SESSION['user_id'];
        
        $user = $this->userModel->findById($userId);
        $stats = $this->userModel->getUserStats($userId); // Assumant que cette méthode existe dans User Model
        
        $this->render('profile/index', [
            'user' => $user,
            'stats' => $stats,
        ]);
    }
    
    /**
     * Gère les mises à jour du profil (infos, mot de passe, préférences).
     */
    public function update() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectBeforeRender('?controller=profile');
        }
        
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'update_info':
                $this->updateUserInfo();
                break;
            case 'change_password':
                $this->changePassword();
                break;
            case 'update_preferences':
                $this->updatePreferences();
                break;
            default:
                $this->setMessage('Action non reconnue', 'error');
        }
        
        $this->redirectBeforeRender('?controller=profile');
    }
    
    /**
     * Affiche l'historique complet de l'activité de l'utilisateur.
     */
    public function activity() {
        $this->requireLogin();
        
        $userId = $_SESSION['user_id'];
        $activities = $this->userModel->getActivity($userId); // Récupère toute l'activité
        
        $this->render('profile/activity', [
            'activities' => $activities,
            'totalActivities' => count($activities),
            // La pagination pourrait être ajoutée ici si nécessaire
            'currentPage' => 1,
            'totalPages' => 1
        ]);
    }
    
    /**
     * Affiche les notifications de l'utilisateur.
     */
    public function notifications() {
        $this->requireLogin();
        
        $userId = $_SESSION['user_id'];
        
        if (isset($_GET['mark_read'])) {
            $this->userModel->markAllNotificationsAsRead($userId);
            $this->redirectBeforeRender('?controller=profile&action=notifications');
        }
        
        $notifications = $this->userModel->getNotifications($userId);
        
        $this->render('profile/notifications', [
            'notifications' => $notifications
        ]);
    }

    /**
     * Gère la suppression du compte de l'utilisateur.
     */
    public function deleteAccount() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectBeforeRender('?controller=profile');
        }
        
        $password = $_POST['password'] ?? '';
        $confirmation = $_POST['confirmation'] ?? '';
        
        if ($confirmation !== 'SUPPRIMER') {
            $this->setMessage('Confirmation incorrecte', 'error');
            $this->redirectBeforeRender('?controller=profile');
        }
        
        $user = $this->userModel->findById($_SESSION['user_id']);
        if (!password_verify($password, $user['password'])) {
            $this->setMessage('Mot de passe incorrect', 'error');
            $this->redirectBeforeRender('?controller=profile');
        }
        
        // Soft delete du compte
        $this->userModel->softDelete($_SESSION['user_id']);
        
        // Déconnexion
        session_destroy();
        $this->redirectBeforeRender('?controller=auth&action=login&message=account_deleted');
    }

    // --- Actions API pour les notifications (appelées en JS) ---

    public function markNotificationRead() {
        $this->requireLogin();
        $notificationId = (int)($_POST['notification_id'] ?? 0);
        if ($this->userModel->markNotificationRead($notificationId, $_SESSION['user_id'])) {
            $this->jsonResponse(['success' => true]);
        } else {
            $this->jsonResponse(['success' => false], 400);
        }
    }

    public function deleteNotification() {
        $this->requireLogin();
        $notificationId = (int)($_POST['notification_id'] ?? 0);
        if ($this->userModel->deleteNotification($notificationId, $_SESSION['user_id'])) {
            $this->jsonResponse(['success' => true]);
        } else {
            $this->jsonResponse(['success' => false], 400);
        }
    }


    // --- Méthodes privées du contrôleur ---

    private function updateUserInfo() {
        $data = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
        ];

        if (!$this->validateEmail($data['email'])) {
            $this->setMessage('Email invalide', 'error');
            return;
        }

        if ($this->userModel->updateProfile($_SESSION['user_id'], $data)) {
            $this->setMessage('Profil mis à jour avec succès', 'success');
        } else {
            $this->setMessage('Erreur lors de la mise à jour du profil', 'error');
        }
    }

    private function changePassword() {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (strlen($newPassword) < 6) {
            $this->setMessage('Le nouveau mot de passe doit faire au moins 6 caractères', 'error');
            return;
        }
        if ($newPassword !== $confirmPassword) {
            $this->setMessage('Les mots de passe ne correspondent pas', 'error');
            return;
        }

        $user = $this->userModel->findById($_SESSION['user_id']);
        if (!password_verify($currentPassword, $user['password'])) {
            $this->setMessage('Mot de passe actuel incorrect', 'error');
            return;
        }

        if ($this->userModel->updatePassword($_SESSION['user_id'], $newPassword)) {
            $this->setMessage('Mot de passe modifié avec succès', 'success');
        } else {
            $this->setMessage('Erreur lors de la modification du mot de passe', 'error');
        }
    }

    private function updatePreferences() {
        $prefs = [
            'notification_email' => isset($_POST['notification_email']) ? 1 : 0,
            'notification_browser' => isset($_POST['notification_browser']) ? 1 : 0,
            'theme_preference' => $_POST['theme_preference'] ?? 'auto',
        ];

        if ($this->userModel->updatePreferences($_SESSION['user_id'], $prefs)) {
            $this->setMessage('Préférences mises à jour', 'success');
        } else {
            $this->setMessage('Erreur lors de la mise à jour des préférences', 'error');
        }
    }
}