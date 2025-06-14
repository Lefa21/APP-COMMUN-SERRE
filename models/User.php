<?php
// models/User.php

class User {
    private $db;

   public function __construct() {
        // Ce modèle gère les utilisateurs, qui sont sur la BD locale.
        $this->db = Database::getConnection('local');
    }

    /**
     * Trouve un utilisateur par son nom d'utilisateur.
     * Utilisé principalement pour la connexion.
     * @param string $username
     * @return array|false
     */
public function findByUsername($username) {
    $stmt = $this->db->prepare("
        SELECT u.id_user, u.username, u.password, r.name as role_name
        FROM user u
        LEFT JOIN role r ON u.role_id = r.id
        LEFT JOIN user_profiles up ON u.id_user = up.user_id
        WHERE u.username = ? AND (up.deleted_at IS NULL OR up.deleted_at = '0000-00-00 00:00:00')
    ");
    $stmt->execute([$username]);
    return $stmt->fetch();
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
        
        // La colonne team_id a été retirée de la requête
        $stmt = $this->db->prepare("UPDATE user_profiles SET email = ?, first_name = ?, last_name = ? WHERE user_id = ?");
        $stmt->execute([$data['email'], $data['first_name'], $data['last_name'], $userId]);
        
        $this->db->commit();
        return true;
    } catch (Exception $e) {
        $this->db->rollBack();
        return false;
    }
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
     * Trouve un utilisateur et son profil complet par son ID.
     * @param string $userId
     * @return array|false
     */
    public function findById($userId) {
        $stmt = $this->db->prepare("
            SELECT u.id_user, u.username, u.password, u.role_id, r.name as role_name,
                   up.email, up.first_name, up.last_name, up.phone,
                   up.notification_email, up.notification_browser, up.theme_preference,
                   up.created_at, up.updated_at, up.deleted_at
            FROM user u
            LEFT JOIN user_profiles up ON u.id_user = up.user_id
            LEFT JOIN role r ON u.role_id = r.id
            WHERE u.id_user = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
    
    /**
     * Authentifie un utilisateur.
     * @param string $username
     * @param string $password
     * @return array|false
     */
  public function authenticate($username, $password) {
    $user = $this->findByUsername($username); // Appelle la version corrigée
    if ($user && password_verify($password, $user['password'])) {
        return [
            'id' => $user['id_user'],
            'username' => $user['username'],
            'role' => ($user['role_name'] === 'admin') ? 'admin' : 'user',
        ];
    }
    return false;
}

    /**
     * Crée un nouvel utilisateur et son profil.
     * @return string|false
     */
     /**
     * Crée un nouvel utilisateur et son profil avec un rôle spécifique.
     * @param string $username
     * @param string $email
     * @param string $password
     * @param int    $roleId L'ID du rôle à assigner.
     * @param string $firstName
     * @param string $lastName
     * @return string|false L'ID de l'utilisateur créé ou false.
     */
      /**
     * Crée un nouvel utilisateur avec le rôle "étudiant" par défaut.
     * Le paramètre $roleId a été supprimé.
     * * @return string|false L'ID de l'utilisateur créé ou false.
     */
    public function create($username, $email, $password, $firstName = '', $lastName = '') {
        try {
            $this->db->beginTransaction();
            
            $userId = $this->generateUUID();
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $defaultRoleId = 1; // Rôle "étudiant" assigné par défaut.

            $stmtUser = $this->db->prepare("INSERT INTO user (id_user, username, password, role_id) VALUES (?, ?, ?, ?)");
            $stmtUser->execute([$userId, $username, $hashedPassword, $defaultRoleId]);

            $stmtProfile = $this->db->prepare("INSERT INTO user_profiles (user_id, email, first_name, last_name) VALUES (?, ?, ?, ?)");
            $stmtProfile->execute([$userId, $email, $firstName, $lastName]);
            
            $this->db->commit();
            return $userId;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("User::create error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Met à jour le profil d'un utilisateur.
     * @return bool
     */
    public function updateProfile($userId, $data) {
        $stmt = $this->db->prepare("
            UPDATE user_profiles
            SET first_name = ?, last_name = ?, email = ?, phone = ?
            WHERE user_id = ?
        ");
        return $stmt->execute([
            $data['first_name'], $data['last_name'],
            $data['email'], $data['phone'], $userId
        ]);
    }

    /**
     * Met à jour le mot de passe d'un utilisateur.
     * @return bool
     */
    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE user SET password = ? WHERE id_user = ?");
        return $stmt->execute([$hashedPassword, $userId]);
    }
    
    /**
     * Récupère tous les utilisateurs avec leurs profils pour l'interface d'administration.
     * @return array
     */
    public function findAllWithProfiles() {
        $stmt = $this->db->query("
            SELECT u.id_user, u.username, u.role_id, r.name as role_name,
                   up.email, up.first_name, up.last_name, up.phone,
                   up.created_at, up.deleted_at,
                   (CASE WHEN up.deleted_at IS NULL THEN 1 ELSE 0 END) as is_active,
                   (SELECT COUNT(*) FROM actuator_logs al WHERE al.user_id = u.id_user) as total_actions,
                   (SELECT MAX(timestamp) FROM actuator_logs al WHERE al.user_id = u.id_user) as last_activity
            FROM user u
            LEFT JOIN user_profiles up ON u.id_user = up.user_id
            LEFT JOIN role r ON u.role_id = r.id
            ORDER BY up.created_at DESC
        ");
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère l'historique d'activité d'un utilisateur.
     * @return array
     */
    public function getActivity($userId, $limit = null) {
        $sql = "
            SELECT 
                al.action, 
                al.timestamp, 
                -- Note: actuator_name doit être récupéré depuis la BD distante, ce qui complexifie la requête.
                -- Pour l'instant, nous affichons l'ID. Une solution plus avancée serait nécessaire.
                al.actionneur_id, 
                'actuator_action' as activity_type 
            FROM actuator_logs al
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
                $stats['account_age_days'] = 0;
            }
        }
        return $stats;
    }

    /**
     * Récupère les notifications pour un utilisateur.
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
     * @return bool
     */
    public function markAllNotificationsAsRead($userId) {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
        return $stmt->execute([$userId]);
    }

    /**
     * Marque une notification spécifique comme lue.
     * @return bool
     */
    public function markNotificationRead($notificationId, $userId) {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
        return $stmt->execute([$notificationId, $userId]);
    }

    /**
     * Supprime une notification spécifique.
     * @return bool
     */
    public function deleteNotification($notificationId, $userId) {
        $stmt = $this->db->prepare("DELETE FROM notifications WHERE id = ? AND user_id = ?");
        return $stmt->execute([$notificationId, $userId]);
    }
    
    /**
     * Met à jour les préférences de l'utilisateur.
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

    /**
     * Désactive un compte utilisateur (soft delete).
     * @return bool
     */
    public function softDelete($userId) {
        $stmt = $this->db->prepare("UPDATE user_profiles SET deleted_at = NOW() WHERE user_id = ?");
        return $stmt->execute([$userId]);
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
}