<?php
// controllers/ProfileController.php
require_once BASE_PATH . '/controllers/BaseController.php';

class ProfileController extends BaseController {
    
    public function index() {
        $this->requireLogin();
        
        $userProfile = $this->getUserProfile($_SESSION['user_id']);
        $userStats = $this->getUserStats($_SESSION['user_id']);
        $recentActivity = $this->getUserActivity($_SESSION['user_id']);
        $teams = $this->getTeams();
        
        $this->render('profile/index', [
            'user' => $userProfile,
            'stats' => $userStats,
            'recentActivity' => $recentActivity,
            'teams' => $teams,
            'isAdmin' => $this->isAdmin()
        ]);
    }
    
    public function update() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?controller=profile');
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
                $_SESSION['error_message'] = 'Action non reconnue';
        }
        
        $this->redirect('?controller=profile');
    }
    
    public function activity() {
        $this->requireLogin();
        
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $activities = $this->getUserActivityPaginated($_SESSION['user_id'], $limit, $offset);
        $totalActivities = $this->getTotalUserActivities($_SESSION['user_id']);
        $totalPages = ceil($totalActivities / $limit);
        
        $this->render('profile/activity', [
            'activities' => $activities,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalActivities' => $totalActivities
        ]);
    }
    
    public function notifications() {
        $this->requireLogin();
        
        $notifications = $this->getUserNotifications($_SESSION['user_id']);
        
        // Marquer comme lues
        if ($_GET['mark_read'] ?? false) {
            $this->markNotificationsAsRead($_SESSION['user_id']);
            $this->redirect('?controller=profile&action=notifications');
        }
        
        $this->render('profile/notifications', [
            'notifications' => $notifications
        ]);
    }
    
    public function deleteAccount() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?controller=profile');
        }
        
        $password = $_POST['password'] ?? '';
        $confirmation = $_POST['confirmation'] ?? '';
        
        if ($confirmation !== 'SUPPRIMER') {
            $_SESSION['error_message'] = 'Confirmation incorrecte';
            $this->redirect('?controller=profile');
        }
        
        // Vérifier le mot de passe
        $user = $this->getUserProfile($_SESSION['user_id']);
        if (!password_verify($password, $user['password'])) {
            $_SESSION['error_message'] = 'Mot de passe incorrect';
            $this->redirect('?controller=profile');
        }
        
        // Supprimer le compte (soft delete)
        $this->softDeleteUser($_SESSION['user_id']);
        
        // Déconnexion
        session_destroy();
        $this->redirect('?controller=auth&action=login&message=account_deleted');
    }
    
    private function getUserProfile($userId) {
        $stmt = $this->db->prepare("
            SELECT u.id_user, u.username, u.password, r.name as role_name,
                   up.email, up.first_name, up.last_name, up.phone, up.team_id,
                   up.notification_email, up.notification_browser, up.theme_preference,
                   up.created_at, up.updated_at,
                   t.name as team_name
            FROM user u
            LEFT JOIN user_profiles up ON u.id_user = up.user_id
            LEFT JOIN role r ON u.role_id = r.id
            LEFT JOIN teams t ON up.team_id = t.id
            WHERE u.id_user = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
    
    private function getUserStats($userId) {
        // Statistiques sur l'activité de l'utilisateur
        $stats = [
            'total_actions' => 0,
            'sensors_accessed' => 0,
            'actuators_controlled' => 0,
            'last_login' => null,
            'account_age_days' => 0
        ];
        
        // Nombre total d'actions
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM actuator_logs 
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        $stats['total_actions'] = $result['total'] ?? 0;
        
        // Nombre d'actionneurs contrôlés
        $stmt = $this->db->prepare("
            SELECT COUNT(DISTINCT actionneur_id) as total 
            FROM actuator_logs 
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        $stats['actuators_controlled'] = $result['total'] ?? 0;
        
        // Âge du compte
        $user = $this->getUserProfile($userId);
        if ($user && $user['created_at']) {
            $created = new DateTime($user['created_at']);
            $now = new DateTime();
            $stats['account_age_days'] = $created->diff($now)->days;
        }
        
        return $stats;
    }
    
    private function getUserActivity($userId, $limit = 10) {
        $stmt = $this->db->prepare("
            SELECT al.action, al.timestamp, a.nom as actuator_name, t.name as team_name
            FROM actuator_logs al
            JOIN actionneurs a ON al.actionneur_id = a.id
            LEFT JOIN teams t ON a.team_id = t.id
            WHERE al.user_id = ?
            ORDER BY al.timestamp DESC
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }
    
    private function getUserActivityPaginated($userId, $limit, $offset) {
        $stmt = $this->db->prepare("
            SELECT al.action, al.timestamp, a.nom as actuator_name, t.name as team_name,
                   'actuator_action' as activity_type
            FROM actuator_logs al
            JOIN actionneurs a ON al.actionneur_id = a.id
            LEFT JOIN teams t ON a.team_id = t.id
            WHERE al.user_id = ?
            ORDER BY al.timestamp DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll();
    }
    
    private function getTotalUserActivities($userId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM actuator_logs WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }
    
    private function getUserNotifications($userId) {
        $stmt = $this->db->prepare("
            SELECT id, type, title, message, is_read, created_at
            FROM notifications 
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT 50
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    private function markNotificationsAsRead($userId) {
        $stmt = $this->db->prepare("
            UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0
        ");
        $stmt->execute([$userId]);
    }
    
    private function updateUserInfo() {
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $teamId = (int)($_POST['team_id'] ?? 0);
        
        // Validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error_message'] = 'Email invalide';
            return;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Vérifier si le profil existe
            $stmt = $this->db->prepare("SELECT user_id FROM user_profiles WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $exists = $stmt->fetch();
            
            if ($exists) {
                // Mettre à jour
                $stmt = $this->db->prepare("
                    UPDATE user_profiles 
                    SET first_name = ?, last_name = ?, email = ?, phone = ?, team_id = ?, updated_at = NOW()
                    WHERE user_id = ?
                ");
                $stmt->execute([$firstName, $lastName, $email, $phone, $teamId, $_SESSION['user_id']]);
            } else {
                // Créer
                $stmt = $this->db->prepare("
                    INSERT INTO user_profiles (user_id, first_name, last_name, email, phone, team_id, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$_SESSION['user_id'], $firstName, $lastName, $email, $phone, $teamId]);
            }
            
            $this->db->commit();
            $_SESSION['success_message'] = 'Profil mis à jour avec succès';
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error_message'] = 'Erreur lors de la mise à jour du profil';
        }
    }
    
    private function changePassword() {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validation
        if (strlen($newPassword) < 6) {
            $_SESSION['error_message'] = 'Le nouveau mot de passe doit contenir au moins 6 caractères';
            return;
        }
        
        if ($newPassword !== $confirmPassword) {
            $_SESSION['error_message'] = 'Les mots de passe ne correspondent pas';
            return;
        }
        
        // Vérifier le mot de passe actuel
        $user = $this->getUserProfile($_SESSION['user_id']);
        if (!password_verify($currentPassword, $user['password'])) {
            $_SESSION['error_message'] = 'Mot de passe actuel incorrect';
            return;
        }
        
        // Mettre à jour le mot de passe
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE user SET password = ? WHERE id_user = ?");
        
        if ($stmt->execute([$hashedPassword, $_SESSION['user_id']])) {
            $_SESSION['success_message'] = 'Mot de passe modifié avec succès';
        } else {
            $_SESSION['error_message'] = 'Erreur lors de la modification du mot de passe';
        }
    }
    
    private function updatePreferences() {
        $notificationEmail = isset($_POST['notification_email']) ? 1 : 0;
        $notificationBrowser = isset($_POST['notification_browser']) ? 1 : 0;
        $themePreference = $_POST['theme_preference'] ?? 'auto';
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO user_profiles (user_id, notification_email, notification_browser, theme_preference, created_at)
                VALUES (?, ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE
                notification_email = VALUES(notification_email),
                notification_browser = VALUES(notification_browser),
                theme_preference = VALUES(theme_preference),
                updated_at = NOW()
            ");
            
            $stmt->execute([$_SESSION['user_id'], $notificationEmail, $notificationBrowser, $themePreference]);
            $_SESSION['success_message'] = 'Préférences mises à jour';
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Erreur lors de la mise à jour des préférences';
        }
    }
    
    private function softDeleteUser($userId) {
        // Désactiver l'utilisateur au lieu de le supprimer complètement
        $stmt = $this->db->prepare("
            UPDATE user SET 
                username = CONCAT('deleted_', username, '_', UNIX_TIMESTAMP()),
                role_id = NULL,
                deleted_at = NOW()
            WHERE id_user = ?
        ");
        $stmt->execute([$userId]);
        
        // Marquer le profil comme supprimé
        $stmt = $this->db->prepare("
            UPDATE user_profiles SET 
                email = NULL,
                deleted_at = NOW()
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
    }
    
    private function getTeams() {
        $stmt = $this->db->query("SELECT id, name FROM teams ORDER BY name");
        return $stmt->fetchAll();
    }
}
?>