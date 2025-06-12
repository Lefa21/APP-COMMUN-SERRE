<?php
// controllers/ProfileController.php - Version complète
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
                $_SESSION['error_message'] = 'Action non reconnue';
        }
        
        $this->redirectBeforeRender('?controller=profile');
    }
    
    public function activity() {
        $this->requireLogin();
        
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        // Filtres
        $actionFilter = $_GET['action_filter'] ?? '';
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo = $_GET['date_to'] ?? '';
        
        $activities = $this->getUserActivityPaginated($_SESSION['user_id'], $limit, $offset, $actionFilter, $dateFrom, $dateTo);
        $totalActivities = $this->getTotalUserActivities($_SESSION['user_id'], $actionFilter, $dateFrom, $dateTo);
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
        
        // Marquer comme lues si demandé
        if ($_GET['mark_read'] ?? false) {
            $this->markNotificationsAsRead($_SESSION['user_id']);
            $this->redirectBeforeRender('?controller=profile&action=notifications');
        }
        
        $this->render('profile/notifications', [
            'notifications' => $notifications
        ]);
    }
    
    public function markNotificationRead() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Méthode POST requise'], 405);
        }
        
        $notificationId = (int)($_POST['notification_id'] ?? 0);
        
        if (!$notificationId) {
            $this->jsonResponse(['error' => 'ID notification requis'], 400);
        }
        
        try {
            $stmt = $this->db->prepare("
                UPDATE notifications 
                SET is_read = 1, updated_at = NOW()
                WHERE id = ? AND user_id = ?
            ");
            $success = $stmt->execute([$notificationId, $_SESSION['user_id']]);
            
            if ($success) {
                $this->jsonResponse(['success' => true, 'message' => 'Notification marquée comme lue']);
            } else {
                $this->jsonResponse(['error' => 'Notification non trouvée'], 404);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['error' => 'Erreur base de données'], 500);
        }
    }
    
    public function markNotificationUnread() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Méthode POST requise'], 405);
        }
        
        $notificationId = (int)($_POST['notification_id'] ?? 0);
        
        if (!$notificationId) {
            $this->jsonResponse(['error' => 'ID notification requis'], 400);
        }
        
        try {
            $stmt = $this->db->prepare("
                UPDATE notifications 
                SET is_read = 0, updated_at = NOW()
                WHERE id = ? AND user_id = ?
            ");
            $success = $stmt->execute([$notificationId, $_SESSION['user_id']]);
            
            if ($success) {
                $this->jsonResponse(['success' => true, 'message' => 'Notification marquée comme non lue']);
            } else {
                $this->jsonResponse(['error' => 'Notification non trouvée'], 404);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['error' => 'Erreur base de données'], 500);
        }
    }
    
    public function deleteNotification() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Méthode POST requise'], 405);
        }
        
        $notificationId = (int)($_POST['notification_id'] ?? 0);
        
        if (!$notificationId) {
            $this->jsonResponse(['error' => 'ID notification requis'], 400);
        }
        
        try {
            $stmt = $this->db->prepare("
                DELETE FROM notifications 
                WHERE id = ? AND user_id = ?
            ");
            $success = $stmt->execute([$notificationId, $_SESSION['user_id']]);
            
            if ($success) {
                $this->jsonResponse(['success' => true, 'message' => 'Notification supprimée']);
            } else {
                $this->jsonResponse(['error' => 'Notification non trouvée'], 404);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['error' => 'Erreur base de données'], 500);
        }
    }
    
    public function generateTestNotifications() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Méthode POST requise'], 405);
        }
        
        try {
            $notifications = [
                [
                    'type' => 'info',
                    'title' => 'Bienvenue dans le système',
                    'message' => 'Votre compte a été configuré avec succès. Vous pouvez maintenant commencer à utiliser le système de gestion de serres.'
                ],
                [
                    'type' => 'warning',
                    'title' => 'Maintenance prévue',
                    'message' => 'Une maintenance du système est prévue ce weekend de 2h à 6h du matin. Les services pourraient être temporairement indisponibles.'
                ],
                [
                    'type' => 'alert',
                    'title' => 'Alerte capteur température',
                    'message' => 'Le capteur de température de la serre A a détecté une température anormalement élevée (38°C). Vérifiez le système de ventilation.'
                ],
                [
                    'type' => 'success',
                    'title' => 'Action réussie',
                    'message' => 'L\'arrosage automatique a été activé avec succès dans la serre B. Durée prévue: 15 minutes.'
                ],
                [
                    'type' => 'system',
                    'title' => 'Mise à jour système',
                    'message' => 'Le système a été mis à jour vers la version 2.1.0. Nouvelles fonctionnalités: graphiques améliorés et export automatique.'
                ]
            ];
            
            $stmt = $this->db->prepare("
                INSERT INTO notifications (user_id, type, title, message, is_read, created_at)
                VALUES (?, ?, ?, ?, 0, NOW())
            ");
            
            $count = 0;
            foreach ($notifications as $notification) {
                if ($stmt->execute([$_SESSION['user_id'], $notification['type'], $notification['title'], $notification['message']])) {
                    $count++;
                }
            }
            
            $this->jsonResponse(['success' => true, 'message' => "{$count} notifications de test générées"]);
            
        } catch (Exception $e) {
            $this->jsonResponse(['error' => 'Erreur lors de la génération'], 500);
        }
    }
    
    public function saveNotificationSettings() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Méthode POST requise'], 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            $this->jsonResponse(['error' => 'Données invalides'], 400);
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO user_notification_settings (user_id, email_enabled, browser_enabled, sms_enabled, frequency, quiet_start, quiet_end, weekend_quiet, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE
                email_enabled = VALUES(email_enabled),
                browser_enabled = VALUES(browser_enabled),
                sms_enabled = VALUES(sms_enabled),
                frequency = VALUES(frequency),
                quiet_start = VALUES(quiet_start),
                quiet_end = VALUES(quiet_end),
                weekend_quiet = VALUES(weekend_quiet),
                updated_at = NOW()
            ");
            
            $success = $stmt->execute([
                $_SESSION['user_id'],
                $input['email'] ? 1 : 0,
                $input['browser'] ? 1 : 0,
                $input['sms'] ? 1 : 0,
                $input['frequency'],
                $input['quietStart'],
                $input['quietEnd'],
                $input['weekendQuiet'] ? 1 : 0
            ]);
            
            if ($success) {
                $this->jsonResponse(['success' => true, 'message' => 'Paramètres sauvegardés']);
            } else {
                $this->jsonResponse(['error' => 'Erreur lors de la sauvegarde'], 500);
            }
            
        } catch (Exception $e) {
            $this->jsonResponse(['error' => 'Erreur base de données'], 500);
        }
    }
    
    public function exportActivity() {
        $this->requireLogin();
        
        $actionFilter = $_GET['action_filter'] ?? '';
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo = $_GET['date_to'] ?? '';
        $format = $_GET['format'] ?? 'csv';
        
        $activities = $this->getAllUserActivity($_SESSION['user_id'], $actionFilter, $dateFrom, $dateTo);
        
        if ($format === 'json') {
            $this->exportActivityJSON($activities);
        } else {
            $this->exportActivityCSV($activities);
        }
    }
    
    public function deleteAccount() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectBeforeRender('?controller=profile');
        }
        
        $password = $_POST['password'] ?? '';
        $confirmation = $_POST['confirmation'] ?? '';
        
        if ($confirmation !== 'SUPPRIMER') {
            $_SESSION['error_message'] = 'Confirmation incorrecte';
            $this->redirectBeforeRender('?controller=profile');
        }
        
        // Vérifier le mot de passe
        $user = $this->getUserProfile($_SESSION['user_id']);
        if (!password_verify($password, $user['password'])) {
            $_SESSION['error_message'] = 'Mot de passe incorrect';
            $this->redirectBeforeRender('?controller=profile');
        }
        
        // Supprimer le compte (soft delete)
        $this->softDeleteUser($_SESSION['user_id']);
        
        // Déconnexion
        session_destroy();
        $this->redirectBeforeRender('?controller=auth&action=login&message=account_deleted');
    }
    
    // ============ MÉTHODES PRIVÉES ============
    
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
            SELECT al.action, al.timestamp, a.nom as actuator_name, t.name as team_name,
                   'actuator_action' as activity_type
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
    
    private function getUserActivityPaginated($userId, $limit, $offset, $actionFilter = '', $dateFrom = '', $dateTo = '') {
        $sql = "
            SELECT al.action, al.timestamp, a.nom as actuator_name, t.name as team_name,
                   'actuator_action' as activity_type
            FROM actuator_logs al
            JOIN actionneurs a ON al.actionneur_id = a.id
            LEFT JOIN teams t ON a.team_id = t.id
            WHERE al.user_id = ?
        ";
        
        $params = [$userId];
        
        if ($actionFilter) {
            $sql .= " AND al.action = ?";
            $params[] = $actionFilter;
        }
        
        if ($dateFrom) {
            $sql .= " AND DATE(al.timestamp) >= ?";
            $params[] = $dateFrom;
        }
        
        if ($dateTo) {
            $sql .= " AND DATE(al.timestamp) <= ?";
            $params[] = $dateTo;
        }
        
        $sql .= " ORDER BY al.timestamp DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    private function getTotalUserActivities($userId, $actionFilter = '', $dateFrom = '', $dateTo = '') {
        $sql = "
            SELECT COUNT(*) 
            FROM actuator_logs al
            WHERE al.user_id = ?
        ";
        
        $params = [$userId];
        
        if ($actionFilter) {
            $sql .= " AND al.action = ?";
            $params[] = $actionFilter;
        }
        
        if ($dateFrom) {
            $sql .= " AND DATE(al.timestamp) >= ?";
            $params[] = $dateFrom;
        }
        
        if ($dateTo) {
            $sql .= " AND DATE(al.timestamp) <= ?";
            $params[] = $dateTo;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }
    
    private function getAllUserActivity($userId, $actionFilter = '', $dateFrom = '', $dateTo = '') {
        $sql = "
            SELECT al.action, al.timestamp, a.nom as actuator_name, t.name as team_name,
                   'actuator_action' as activity_type
            FROM actuator_logs al
            JOIN actionneurs a ON al.actionneur_id = a.id
            LEFT JOIN teams t ON a.team_id = t.id
            WHERE al.user_id = ?
        ";
        
        $params = [$userId];
        
        if ($actionFilter) {
            $sql .= " AND al.action = ?";
            $params[] = $actionFilter;
        }
        
        if ($dateFrom) {
            $sql .= " AND DATE(al.timestamp) >= ?";
            $params[] = $dateFrom;
        }
        
        if ($dateTo) {
            $sql .= " AND DATE(al.timestamp) <= ?";
            $params[] = $dateTo;
        }
        
        $sql .= " ORDER BY al.timestamp DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    private function getUserNotifications($userId) {
        // Vérifier si la table notifications existe
        $stmt = $this->db->query("SHOW TABLES LIKE 'notifications'");
        if ($stmt->rowCount() === 0) {
            // Créer la table si elle n'existe pas
            $this->createNotificationsTable();
        }
        
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
            UPDATE notifications SET is_read = 1, updated_at = NOW() 
            WHERE user_id = ? AND is_read = 0
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
                $stmt->execute([$firstName, $lastName, $email, $phone, $teamId ?: null, $_SESSION['user_id']]);
            } else {
                // Créer
                $stmt = $this->db->prepare("
                    INSERT INTO user_profiles (user_id, first_name, last_name, email, phone, team_id, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$_SESSION['user_id'], $firstName, $lastName, $email, $phone, $teamId ?: null]);
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
                role_id = NULL
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
    
    private function exportActivityCSV($activities) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="mon_activite_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // En-têtes
        fputcsv($output, [
            'Action',
            'Actionneur',
            'Équipe',
            'Date/Heure'
        ]);
        
        // Données
        foreach ($activities as $activity) {
            fputcsv($output, [
                $activity['action'],
                $activity['actuator_name'],
                $activity['team_name'] ?? 'N/A',
                $activity['timestamp']
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    private function exportActivityJSON($activities) {
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="mon_activite_' . date('Y-m-d') . '.json"');
        
        $export = [
            'export_date' => date('Y-m-d H:i:s'),
            'user_id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'total_activities' => count($activities),
            'activities' => $activities
        ];
        
        echo json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    private function createNotificationsTable() {
        $sql = "
            CREATE TABLE IF NOT EXISTS notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id VARCHAR(36) NOT NULL,
                type ENUM('info', 'warning', 'alert', 'success', 'system') DEFAULT 'info',
                title VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                is_read BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES user(id_user) ON DELETE CASCADE,
                INDEX idx_user_created (user_id, created_at),
                INDEX idx_user_read (user_id, is_read)
            );
            
            CREATE TABLE IF NOT EXISTS user_notification_settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id VARCHAR(36) NOT NULL UNIQUE,
                email_enabled BOOLEAN DEFAULT TRUE,
                browser_enabled BOOLEAN DEFAULT TRUE,
                sms_enabled BOOLEAN DEFAULT FALSE,
                frequency ENUM('immediate', 'hourly', 'daily') DEFAULT 'immediate',
                quiet_start TIME DEFAULT '22:00:00',
                quiet_end TIME DEFAULT '07:00:00',
                weekend_quiet BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES user(id_user) ON DELETE CASCADE
            );
        ";
        
        try {
            $this->db->exec($sql);
        } catch (Exception $e) {
            error_log("Erreur création tables notifications: " . $e->getMessage());
        }
    }
    
    private function getTeams() {
        $stmt = $this->db->query("SELECT id, name FROM teams ORDER BY name");
        return $stmt->fetchAll();
    }
}
?>