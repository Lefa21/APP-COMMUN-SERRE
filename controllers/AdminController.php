<?php
// controllers/AdminController.php
require_once BASE_PATH . '/controllers/BaseController.php';

class AdminController extends BaseController {
    
    public function users() {
        $this->requireAdmin();
        
        $users = $this->getAllUsers();
        $teams = $this->getTeams();
        $roles = $this->getRoles();
        $stats = $this->getUsersStats();
        
        $this->render('admin/users', [
            'users' => $users,
            'teams' => $teams,
            'roles' => $roles,
            'stats' => $stats
        ]);
    }
    
    public function manageUser() {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?controller=admin&action=users');
        }
        
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'create':
                $this->createUser();
                break;
            case 'update':
                $this->updateUser();
                break;
            case 'delete':
                $this->deleteUser();
                break;
            case 'reset_password':
                $this->resetUserPassword();
                break;
            case 'toggle_status':
                $this->toggleUserStatus();
                break;
            default:
                $_SESSION['error_message'] = 'Action non reconnue';
        }
        
        $this->redirect('?controller=admin&action=users');
    }
    
    public function systemLogs() {
        $this->requireAdmin();
        
        $page = (int)($_GET['page'] ?? 1);
        $filter = $_GET['filter'] ?? 'all';
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $logs = $this->getSystemLogs($filter, $limit, $offset);
        $totalLogs = $this->getTotalLogs($filter);
        $totalPages = ceil($totalLogs / $limit);
        
        $this->render('admin/logs', [
            'logs' => $logs,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'filter' => $filter,
            'totalLogs' => $totalLogs
        ]);
    }
    
    public function dashboard() {
        $this->requireAdmin();
        
        $systemStats = $this->getSystemStats();
        $recentUsers = $this->getRecentUsers();
        $systemAlerts = $this->getSystemAlerts();
        $performanceMetrics = $this->getPerformanceMetrics();
        
        $this->render('admin/dashboard', [
            'stats' => $systemStats,
            'recentUsers' => $recentUsers,
            'alerts' => $systemAlerts,
            'metrics' => $performanceMetrics
        ]);
    }
    
    public function exportUsers() {
        $this->requireAdmin();
        
        $format = $_GET['format'] ?? 'csv';
        $users = $this->getAllUsersForExport();
        
        if ($format === 'json') {
            $this->exportUsersJSON($users);
        } else {
            $this->exportUsersCSV($users);
        }
    }
    
    public function bulkActions() {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?controller=admin&action=users');
        }
        
        $action = $_POST['bulk_action'] ?? '';
        $userIds = $_POST['user_ids'] ?? [];
        
        if (empty($userIds)) {
            $_SESSION['error_message'] = 'Aucun utilisateur sélectionné';
            $this->redirect('?controller=admin&action=users');
        }
        
        switch ($action) {
            case 'activate':
                $this->bulkActivateUsers($userIds);
                break;
            case 'deactivate':
                $this->bulkDeactivateUsers($userIds);
                break;
            case 'delete':
                $this->bulkDeleteUsers($userIds);
                break;
            case 'reset_passwords':
                $this->bulkResetPasswords($userIds);
                break;
        }
        
        $this->redirect('?controller=admin&action=users');
    }
    
    private function getAllUsers() {
        $stmt = $this->db->query("
            SELECT u.id_user, u.username, r.name as role_name,
                   up.email, up.first_name, up.last_name, up.phone,
                   up.created_at, up.updated_at, up.deleted_at,
                   t.name as team_name, t.id as team_id,
                   CASE WHEN up.deleted_at IS NULL THEN 1 ELSE 0 END as is_active,
                   (SELECT COUNT(*) FROM actuator_logs al WHERE al.user_id = u.id_user) as total_actions,
                   (SELECT MAX(timestamp) FROM actuator_logs al WHERE al.user_id = u.id_user) as last_activity
            FROM user u
            LEFT JOIN user_profiles up ON u.id_user = up.user_id
            LEFT JOIN role r ON u.role_id = r.id
            LEFT JOIN teams t ON up.team_id = t.id
            ORDER BY up.created_at DESC
        ");
        return $stmt->fetchAll();
    }
    
    private function getAllUsersForExport() {
        $stmt = $this->db->query("
            SELECT u.id_user, u.username, r.name as role_name,
                   up.email, up.first_name, up.last_name, up.phone,
                   up.created_at, up.updated_at,
                   t.name as team_name,
                   CASE WHEN up.deleted_at IS NULL THEN 'Actif' ELSE 'Inactif' END as status
            FROM user u
            LEFT JOIN user_profiles up ON u.id_user = up.user_id
            LEFT JOIN role r ON u.role_id = r.id
            LEFT JOIN teams t ON up.team_id = t.id
            ORDER BY up.created_at DESC
        ");
        return $stmt->fetchAll();
    }
    
    private function getTeams() {
        $stmt = $this->db->query("SELECT id, name FROM teams ORDER BY name");
        return $stmt->fetchAll();
    }
    
    private function getRoles() {
        $stmt = $this->db->query("SELECT id, name FROM role ORDER BY name");
        return $stmt->fetchAll();
    }
    
    private function getUsersStats() {
        $stats = [];
        
        // Total des utilisateurs
        $stmt = $this->db->query("SELECT COUNT(*) FROM user");
        $stats['total_users'] = $stmt->fetchColumn();
        
        // Utilisateurs actifs
        $stmt = $this->db->query("
            SELECT COUNT(*) FROM user u
            LEFT JOIN user_profiles up ON u.id_user = up.user_id
            WHERE up.deleted_at IS NULL
        ");
        $stats['active_users'] = $stmt->fetchColumn();
        
        // Nouveaux utilisateurs ce mois
        $stmt = $this->db->query("
            SELECT COUNT(*) FROM user_profiles 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
        ");
        $stats['new_users_month'] = $stmt->fetchColumn();
        
        // Administrateurs
        $stmt = $this->db->query("
            SELECT COUNT(*) FROM user u
            JOIN role r ON u.role_id = r.id
            WHERE r.name = 'admin'
        ");
        $stats['admin_users'] = $stmt->fetchColumn();
        
        return $stats;
    }
    
    private function createUser() {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $roleId = (int)($_POST['role_id'] ?? 1);
        $teamId = (int)($_POST['team_id'] ?? 0);
        $password = $_POST['password'] ?? '';
        
        // Validation
        if (empty($username) || empty($email) || empty($password)) {
            $_SESSION['error_message'] = 'Tous les champs obligatoires doivent être remplis';
            return;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error_message'] = 'Email invalide';
            return;
        }
        
        if (strlen($password) < 6) {
            $_SESSION['error_message'] = 'Le mot de passe doit contenir au moins 6 caractères';
            return;
        }
        
        // Vérifier l'unicité
        if ($this->userExists($username, $email)) {
            $_SESSION['error_message'] = 'Un utilisateur avec ce nom ou cet email existe déjà';
            return;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Créer l'utilisateur
            $userId = $this->generateUUID();
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $this->db->prepare("
                INSERT INTO user (id_user, username, password, role_id) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$userId, $username, $hashedPassword, $roleId]);
            
            // Créer le profil
            $stmt = $this->db->prepare("
                INSERT INTO user_profiles (user_id, email, first_name, last_name, team_id, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$userId, $email, $firstName, $lastName, $teamId ?: null]);
            
            $this->db->commit();
            $_SESSION['success_message'] = 'Utilisateur créé avec succès';
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error_message'] = 'Erreur lors de la création de l\'utilisateur';
        }
    }
    
    private function updateUser() {
        $userId = $_POST['user_id'] ?? '';
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $roleId = (int)($_POST['role_id'] ?? 1);
        $teamId = (int)($_POST['team_id'] ?? 0);
        
        if (empty($userId) || empty($username) || empty($email)) {
            $_SESSION['error_message'] = 'Données invalides';
            return;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Mettre à jour l'utilisateur
            $stmt = $this->db->prepare("
                UPDATE user SET username = ?, role_id = ? WHERE id_user = ?
            ");
            $stmt->execute([$username, $roleId, $userId]);
            
            // Mettre à jour le profil
            $stmt = $this->db->prepare("
                UPDATE user_profiles 
                SET email = ?, first_name = ?, last_name = ?, team_id = ?, updated_at = NOW()
                WHERE user_id = ?
            ");
            $stmt->execute([$email, $firstName, $lastName, $teamId ?: null, $userId]);
            
            $this->db->commit();
            $_SESSION['success_message'] = 'Utilisateur mis à jour avec succès';
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error_message'] = 'Erreur lors de la mise à jour de l\'utilisateur';
        }
    }
    
    private function deleteUser() {
        $userId = $_POST['user_id'] ?? '';
        
        if (empty($userId)) {
            $_SESSION['error_message'] = 'ID utilisateur manquant';
            return;
        }
        
        // Empêcher la suppression de son propre compte
        if ($userId === $_SESSION['user_id']) {
            $_SESSION['error_message'] = 'Vous ne pouvez pas supprimer votre propre compte';
            return;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Soft delete
            $stmt = $this->db->prepare("
                UPDATE user_profiles SET deleted_at = NOW() WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            
            $stmt = $this->db->prepare("
                UPDATE user SET role_id = NULL WHERE id_user = ?
            ");
            $stmt->execute([$userId]);
            
            $this->db->commit();
            $_SESSION['success_message'] = 'Utilisateur supprimé avec succès';
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error_message'] = 'Erreur lors de la suppression de l\'utilisateur';
        }
    }
    
    private function resetUserPassword() {
        $userId = $_POST['user_id'] ?? '';
        $newPassword = $this->generateRandomPassword();
        
        if (empty($userId)) {
            $_SESSION['error_message'] = 'ID utilisateur manquant';
            return;
        }
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE user SET password = ? WHERE id_user = ?");
        
        if ($stmt->execute([$hashedPassword, $userId])) {
            $_SESSION['success_message'] = "Mot de passe réinitialisé. Nouveau mot de passe : {$newPassword}";
        } else {
            $_SESSION['error_message'] = 'Erreur lors de la réinitialisation du mot de passe';
        }
    }
    
    private function toggleUserStatus() {
        $userId = $_POST['user_id'] ?? '';
        
        if (empty($userId)) {
            $_SESSION['error_message'] = 'ID utilisateur manquant';
            return;
        }
        
        // Vérifier le statut actuel
        $stmt = $this->db->prepare("
            SELECT deleted_at FROM user_profiles WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $profile = $stmt->fetch();
        
        if ($profile['deleted_at']) {
            // Réactiver
            $stmt = $this->db->prepare("
                UPDATE user_profiles SET deleted_at = NULL WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            $_SESSION['success_message'] = 'Utilisateur réactivé';
        } else {
            // Désactiver
            $stmt = $this->db->prepare("
                UPDATE user_profiles SET deleted_at = NOW() WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            $_SESSION['success_message'] = 'Utilisateur désactivé';
        }
    }
    
    private function getSystemLogs($filter, $limit, $offset) {
        $whereClause = '';
        $params = [$limit, $offset];
        
        if ($filter !== 'all') {
            $whereClause = "WHERE log_type = ?";
            array_unshift($params, $filter);
        }
        
        $stmt = $this->db->prepare("
            SELECT sl.*, u.username
            FROM system_logs sl
            LEFT JOIN user u ON sl.user_id = u.id_user
            {$whereClause}
            ORDER BY sl.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    private function getTotalLogs($filter) {
        if ($filter === 'all') {
            $stmt = $this->db->query("SELECT COUNT(*) FROM system_logs");
            return $stmt->fetchColumn();
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM system_logs WHERE log_type = ?");
            $stmt->execute([$filter]);
            return $stmt->fetchColumn();
        }
    }
    
    private function getSystemStats() {
        return [
            'total_sensors' => $this->getCount('capteurs'),
            'active_sensors' => $this->getCount('capteurs', 'is_active = 1'),
            'total_actuators' => $this->getCount('actionneurs'),
            'active_actuators' => $this->getCount('actionneurs', 'current_state = 1'),
            'total_users' => $this->getCount('user'),
            'active_users' => $this->getCount('user_profiles', 'deleted_at IS NULL'),
            'total_teams' => $this->getCount('teams'),
            'total_measurements' => $this->getCount('mesures'),
            'measurements_today' => $this->getCount('mesures', 'DATE(date_heure) = CURDATE()'),
            'alerts_active' => $this->getActiveAlertsCount()
        ];
    }
    
    private function getRecentUsers($limit = 10) {
        $stmt = $this->db->prepare("
            SELECT u.username, up.email, up.first_name, up.last_name, 
                   up.created_at, t.name as team_name
            FROM user u
            JOIN user_profiles up ON u.id_user = up.user_id
            LEFT JOIN teams t ON up.team_id = t.id
            WHERE up.deleted_at IS NULL
            ORDER BY up.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    private function getSystemAlerts() {
        // Récupérer les alertes système importantes
        $alerts = [];
        
        // Vérifier les capteurs inactifs
        $stmt = $this->db->query("
            SELECT COUNT(*) as count FROM capteurs 
            WHERE is_active = 0
        ");
        $inactiveSensors = $stmt->fetchColumn();
        if ($inactiveSensors > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$inactiveSensors} capteur(s) inactif(s)",
                'action' => 'Vérifier les capteurs'
            ];
        }
        
        // Vérifier les données anciennes
        $stmt = $this->db->query("
            SELECT COUNT(DISTINCT capteur_id) as count 
            FROM mesures 
            WHERE date_heure < DATE_SUB(NOW(), INTERVAL 1 HOUR)
            AND capteur_id IN (SELECT id FROM capteurs WHERE is_active = 1)
        ");
        $oldData = $stmt->fetchColumn();
        if ($oldData > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "{$oldData} capteur(s) sans données récentes",
                'action' => 'Vérifier la connectivité'
            ];
        }
        
        return $alerts;
    }
    
    private function getPerformanceMetrics() {
        return [
            'avg_response_time' => '0.15s', // Simulé
            'uptime' => '99.9%', // Simulé
            'memory_usage' => '45%', // Simulé
            'disk_usage' => '60%', // Simulé
            'database_size' => $this->getDatabaseSize()
        ];
    }
    
    private function bulkActivateUsers($userIds) {
        $placeholders = str_repeat('?,', count($userIds) - 1) . '?';
        $stmt = $this->db->prepare("
            UPDATE user_profiles SET deleted_at = NULL 
            WHERE user_id IN ({$placeholders})
        ");
        $stmt->execute($userIds);
        $_SESSION['success_message'] = count($userIds) . ' utilisateur(s) activé(s)';
    }
    
    private function bulkDeactivateUsers($userIds) {
        // Empêcher la désactivation de son propre compte
        $userIds = array_filter($userIds, function($id) {
            return $id !== $_SESSION['user_id'];
        });
        
        if (empty($userIds)) {
            $_SESSION['error_message'] = 'Aucun utilisateur valide sélectionné';
            return;
        }
        
        $placeholders = str_repeat('?,', count($userIds) - 1) . '?';
        $stmt = $this->db->prepare("
            UPDATE user_profiles SET deleted_at = NOW() 
            WHERE user_id IN ({$placeholders})
        ");
        $stmt->execute($userIds);
        $_SESSION['success_message'] = count($userIds) . ' utilisateur(s) désactivé(s)';
    }
    
    private function bulkDeleteUsers($userIds) {
        // Empêcher la suppression de son propre compte
        $userIds = array_filter($userIds, function($id) {
            return $id !== $_SESSION['user_id'];
        });
        
        if (empty($userIds)) {
            $_SESSION['error_message'] = 'Aucun utilisateur valide sélectionné';
            return;
        }
        
        try {
            $this->db->beginTransaction();
            
            $placeholders = str_repeat('?,', count($userIds) - 1) . '?';
            
            // Soft delete des profils
            $stmt = $this->db->prepare("
                UPDATE user_profiles SET deleted_at = NOW() 
                WHERE user_id IN ({$placeholders})
            ");
            $stmt->execute($userIds);
            
            // Retirer les rôles
            $stmt = $this->db->prepare("
                UPDATE user SET role_id = NULL 
                WHERE id_user IN ({$placeholders})
            ");
            $stmt->execute($userIds);
            
            $this->db->commit();
            $_SESSION['success_message'] = count($userIds) . ' utilisateur(s) supprimé(s)';
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error_message'] = 'Erreur lors de la suppression en lot';
        }
    }
    
    private function bulkResetPasswords($userIds) {
        $resetPasswords = [];
        
        try {
            $this->db->beginTransaction();
            
            foreach ($userIds as $userId) {
                $newPassword = $this->generateRandomPassword();
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                
                $stmt = $this->db->prepare("UPDATE user SET password = ? WHERE id_user = ?");
                $stmt->execute([$hashedPassword, $userId]);
                
                $resetPasswords[$userId] = $newPassword;
            }
            
            $this->db->commit();
            
            // Stocker les mots de passe en session pour affichage
            $_SESSION['reset_passwords'] = $resetPasswords;
            $_SESSION['success_message'] = count($userIds) . ' mot(s) de passe réinitialisé(s)';
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error_message'] = 'Erreur lors de la réinitialisation en lot';
        }
    }
    
    private function exportUsersCSV($users) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="utilisateurs_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // En-têtes
        fputcsv($output, [
            'ID',
            'Nom d\'utilisateur',
            'Email',
            'Prénom',
            'Nom',
            'Rôle',
            'Équipe',
            'Statut',
            'Date de création',
            'Dernière modification'
        ]);
        
        // Données
        foreach ($users as $user) {
            fputcsv($output, [
                $user['id_user'],
                $user['username'],
                $user['email'],
                $user['first_name'],
                $user['last_name'],
                $user['role_name'],
                $user['team_name'],
                $user['status'],
                $user['created_at'],
                $user['updated_at']
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    private function exportUsersJSON($users) {
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="utilisateurs_' . date('Y-m-d') . '.json"');
        
        $export = [
            'export_date' => date('Y-m-d H:i:s'),
            'total_users' => count($users),
            'users' => $users
        ];
        
        echo json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Méthodes utilitaires
    private function userExists($username, $email) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM user u
            LEFT JOIN user_profiles up ON u.id_user = up.user_id
            WHERE u.username = ? OR up.email = ?
        ");
        $stmt->execute([$username, $email]);
        return $stmt->fetchColumn() > 0;
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
    
    private function generateRandomPassword($length = 8) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $password;
    }
    
    private function getCount($table, $condition = '1=1') {
        $stmt = $this->db->query("SELECT COUNT(*) FROM {$table} WHERE {$condition}");
        return $stmt->fetchColumn();
    }
    
    private function getActiveAlertsCount() {
        // Simuler le comptage des alertes actives
        $stmt = $this->db->query("
            SELECT COUNT(*) FROM mesures m
            JOIN capteurs c ON m.capteur_id = c.id
            WHERE m.date_heure >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
            AND (
                (c.type = 'temperature' AND (m.valeur < 15 OR m.valeur > 35)) OR
                (c.type = 'humidity' AND m.valeur < 25) OR
                (c.type = 'soil_moisture' AND m.valeur < 20)
            )
        ");
        return $stmt->fetchColumn();
    }
    
    private function getDatabaseSize() {
        try {
            $stmt = $this->db->query("
                SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) AS size_mb
                FROM information_schema.tables 
                WHERE table_schema = DATABASE()
            ");
            $size = $stmt->fetchColumn();
            return $size ? $size . ' MB' : 'N/A';
        } catch (Exception $e) {
            return 'N/A';
        }
    }
}
?>