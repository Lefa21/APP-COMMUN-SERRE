<?php
// models/User.php

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Trouve un utilisateur par son nom d'utilisateur.
     * Utilisé principalement pour la connexion.
     * @param string $username
     * @return array|false
     */
    public function findByUsername($username) {
        $stmt = $this->db->prepare("
            SELECT u.id_user, u.username, u.password, r.name as role_name, up.team_id
            FROM user u
            LEFT JOIN role r ON u.role_id = r.id
            LEFT JOIN user_profiles up ON u.id_user = up.user_id
            WHERE u.username = ? AND (up.deleted_at IS NULL OR up.deleted_at = '0000-00-00 00:00:00')
        ");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    /**
     * Trouve un utilisateur et son profil complet par son ID.
     * @param string $userId
     * @return array|false
     */
    public function findById($userId) {
         $stmt = $this->db->prepare("
            SELECT u.id_user, u.username, u.password, u.role_id, r.name as role_name,
                   up.email, up.first_name, up.last_name, up.phone, up.team_id,
                   up.notification_email, up.notification_browser, up.theme_preference,
                   up.created_at, up.updated_at, up.deleted_at, t.name as team_name
            FROM user u
            LEFT JOIN user_profiles up ON u.id_user = up.user_id
            LEFT JOIN role r ON u.role_id = r.id
            LEFT JOIN teams t ON up.team_id = t.id
            WHERE u.id_user = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    /**
     * Authentifie un utilisateur et retourne ses informations de session.
     * @param string $username
     * @param string $password
     * @return array|false
     */
    public function authenticate($username, $password) {
        $user = $this->findByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            return [
                'id' => $user['id_user'],
                'username' => $user['username'],
                'role' => ($user['role_name'] === 'admin') ? 'admin' : 'user',
                'team_id' => $user['team_id']
            ];
        }
        return false;
    }

    /**
     * Vérifie si un nom d'utilisateur ou un email existe déjà.
     * @param string $username
     * @param string $email
     * @return bool
     */
    public function exists($username, $email) {
        $stmt = $this->db->prepare("
            SELECT COUNT(u.id_user) FROM user u
            LEFT JOIN user_profiles up ON u.id_user = up.user_id
            WHERE u.username = ? OR up.email = ?
        ");
        $stmt->execute([$username, $email]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Crée un nouvel utilisateur et son profil.
     * @param string $username
     * @param string $email
     * @param string $password
     * @param int|null $teamId
     * @param string $firstName
     * @param string $lastName
     * @return string|false L'ID de l'utilisateur créé ou false en cas d'échec.
     */
    public function create($username, $email, $password, $teamId, $firstName = '', $lastName = '') {
        try {
            $this->db->beginTransaction();
            
            $userId = $this->generateUUID();
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $defaultRoleId = 1; // Rôle "etudiant"

            $stmt = $this->db->prepare("INSERT INTO user (id_user, username, password, role_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$userId, $username, $hashedPassword, $defaultRoleId]);

            $stmt = $this->db->prepare("INSERT INTO user_profiles (user_id, email, first_name, last_name, team_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $email, $firstName, $lastName, $teamId ?: null]);
            
            $this->db->commit();
            return $userId;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("User::create error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Met à jour les informations d'un utilisateur (table user et user_profiles).
     * @param string $userId
     * @param array $data Données contenant username, role_id, email, first_name, last_name, team_id.
     * @return bool
     */
    public function update($userId, $data) {
        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("UPDATE user SET username = ?, role_id = ? WHERE id_user = ?");
            $stmt->execute([$data['username'], $data['role_id'], $userId]);
            
            $stmt = $this->db->prepare("UPDATE user_profiles SET email = ?, first_name = ?, last_name = ?, team_id = ? WHERE user_id = ?");
            $stmt->execute([$data['email'], $data['first_name'], $data['last_name'], $data['team_id'] ?: null, $userId]);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Met à jour uniquement le mot de passe d'un utilisateur.
     * @param string $userId
     * @param string $newPassword
     * @return bool
     */
    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE user SET password = ? WHERE id_user = ?");
        return $stmt->execute([$hashedPassword, $userId]);
    }
    
    /**
     * Récupère tous les utilisateurs avec leurs informations de profil pour l'affichage admin.
     * @return array
     */
    public function findAllWithProfiles() {
        $stmt = $this->db->query("
            SELECT u.id_user, u.username, u.role_id, r.name as role_name,
                   up.email, up.first_name, up.last_name, up.phone,
                   up.created_at, up.updated_at, up.deleted_at,
                   t.name as team_name, t.id as team_id,
                   CASE WHEN up.deleted_at IS NULL OR up.deleted_at = '0000-00-00 00:00:00' THEN 1 ELSE 0 END as is_active,
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
    
    /**
     * Désactive un compte utilisateur (soft delete).
     * @param string $userId
     * @return bool
     */
    public function softDelete($userId) {
        $stmt = $this->db->prepare("UPDATE user_profiles SET deleted_at = NOW() WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }

    /**
     * Réactive un compte utilisateur.
     * @param string $userId
     * @return bool
     */
    public function reactivate($userId) {
        $stmt = $this->db->prepare("UPDATE user_profiles SET deleted_at = NULL WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }
    
    /**
     * Active plusieurs utilisateurs en lot.
     * @param array $userIds
     * @return int Nombre de lignes affectées.
     */
    public function activateMultiple($userIds) {
        if (empty($userIds)) return 0;
        $placeholders = str_repeat('?,', count($userIds) - 1) . '?';
        $stmt = $this->db->prepare("UPDATE user_profiles SET deleted_at = NULL WHERE user_id IN ({$placeholders})");
        $stmt->execute($userIds);
        return $stmt->rowCount();
    }
    
    /**
     * Désactive plusieurs utilisateurs en lot.
     * @param array $userIds
     * @return int Nombre de lignes affectées.
     */
    public function deactivateMultiple($userIds) {
        if (empty($userIds)) return 0;
        $placeholders = str_repeat('?,', count($userIds) - 1) . '?';
        $stmt = $this->db->prepare("UPDATE user_profiles SET deleted_at = NOW() WHERE user_id IN ({$placeholders})");
        $stmt->execute($userIds);
        return $stmt->rowCount();
    }

    /**
     * Récupère les statistiques globales sur les utilisateurs.
     * @return array
     */
    public function getStats() {
        return [
            'total_users' => $this->db->query("SELECT COUNT(*) FROM user")->fetchColumn(),
            'active_users' => $this->db->query("SELECT COUNT(*) FROM user_profiles WHERE deleted_at IS NULL OR deleted_at = '0000-00-00 00:00:00'")->fetchColumn(),
            'new_users_month' => $this->db->query("SELECT COUNT(*) FROM user_profiles WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)")->fetchColumn(),
            'admin_users' => $this->db->query("SELECT COUNT(*) FROM user u JOIN role r ON u.role_id = r.id WHERE r.name = 'admin'")->fetchColumn()
        ];
    }
    
    /**
     * Génère un identifiant unique universel (UUID v4).
     * @return string
     */
    private function generateUUID() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
     /**
     * Récupère l'historique d'activité d'un utilisateur.
     * @param string $userId
     * @param int|null $limit
     * @return array
     */
 public function getActivity($userId, $limit = null) {
        // --- CORRECTION APPLIQUÉE ICI ---
        // On ajoute la colonne statique 'actuator_action' AS activity_type
        // pour que la vue puisse l'utiliser.
        $sql = "
            SELECT 
                al.action, 
                al.timestamp, 
                a.nom as actuator_name, 
                t.name as team_name,
                'actuator_action' as activity_type 
            FROM actuator_logs al
            JOIN actionneurs a ON al.actionneur_id = a.id
            LEFT JOIN teams t ON a.team_id = t.id
            WHERE al.user_id = ?
            ORDER BY al.timestamp DESC
        ";
        
        $params = [$userId];
        if ($limit) {
            $sql .= " LIMIT ?";
            $params[] = (int) $limit;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Récupère les statistiques d'un utilisateur.
     * @param string $userId
     * @return array
     */
    public function getUserStats($userId) {
        $stats = ['total_actions' => 0, 'actuators_controlled' => 0, 'account_age_days' => 0];
        
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM actuator_logs WHERE user_id = ?");
        $stmt->execute([$userId]);
        $stats['total_actions'] = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(DISTINCT actionneur_id) FROM actuator_logs WHERE user_id = ?");
        $stmt->execute([$userId]);
        $stats['actuators_controlled'] = (int) $stmt->fetchColumn();
        
        $user = $this->findById($userId);
        if ($user && !empty($user['created_at'])) {
            try {
                $created = new DateTime($user['created_at']);
                $stats['account_age_days'] = $created->diff(new DateTime())->days;
            } catch (Exception $e) {
                // Gérer le cas où la date est invalide
                $stats['account_age_days'] = 0;
            }
        }
        return $stats;
    }

    /**
     * Récupère les notifications pour un utilisateur.
     * @param string $userId
     * @return array
     */
    public function getNotifications($userId) {
        $stmt = $this->db->prepare("
            SELECT id, type, title, message, is_read, created_at
            FROM notifications WHERE user_id = ?
            ORDER BY created_at DESC LIMIT 50
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Marque toutes les notifications d'un utilisateur comme lues.
     * @param string $userId
     * @return bool
     */
    public function markAllNotificationsAsRead($userId) {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
        return $stmt->execute([$userId]);
    }

    /**
     * Marque une notification spécifique comme lue.
     * @param int $notificationId
     * @param string $userId
     * @return bool
     */
    public function markNotificationRead($notificationId, $userId) {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
        return $stmt->execute([$notificationId, $userId]);
    }

    /**
     * Supprime une notification spécifique.
     * @param int $notificationId
     * @param string $userId
     * @return bool
     */
    public function deleteNotification($notificationId, $userId) {
        $stmt = $this->db->prepare("DELETE FROM notifications WHERE id = ? AND user_id = ?");
        return $stmt->execute([$notificationId, $userId]);
    }
    
     public function updateProfile($userId, $data) {
        $stmt = $this->db->prepare("
            UPDATE user_profiles
            SET first_name = ?, last_name = ?, email = ?, phone = ?, team_id = ?
            WHERE user_id = ?
        ");
        return $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['phone'],
            $data['team_id'] ?: null,
            $userId
        ]);
    }

    /**
     * Met à jour les préférences de l'utilisateur.
     * @param string $userId
     * @param array $prefs
     * @return bool
     */
    public function updatePreferences($userId, $prefs) {
        $stmt = $this->db->prepare("
            UPDATE user_profiles 
            SET notification_email = ?, notification_browser = ?, theme_preference = ?
            WHERE user_id = ?
        ");
        return $stmt->execute([
            $prefs['notification_email'],
            $prefs['notification_browser'],
            $prefs['theme_preference'],
            $userId
        ]);
    }
}