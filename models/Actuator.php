<?php
// models/Actuator.php
class Actuator {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Trouve un actionneur par son ID.
     * @param int $id
     * @return array|false
     */
    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT id, nom as name, type, team_id, is_active, current_state
            FROM actionneurs
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Récupère tous les actionneurs, actifs ou non.
     * @return array
     */
    public function findAll() {
        $stmt = $this->db->query("
            SELECT a.id, a.nom as name, a.type, a.team_id, a.is_active, a.current_state, t.name as team_name
            FROM actionneurs a
            LEFT JOIN teams t ON a.team_id = t.id
            ORDER BY t.name, a.nom
        ");
        return $stmt->fetchAll();
    }

    /**
     * Récupère tous les actionneurs qui sont marqués comme actifs dans le système.
     * @return array
     */
    public function findAllActive() {
        $stmt = $this->db->query("
            SELECT a.id, a.nom as name, a.type, a.team_id, a.is_active, a.current_state, t.name as team_name
            FROM actionneurs a
            LEFT JOIN teams t ON a.team_id = t.id
            WHERE a.is_active = 1
            ORDER BY t.name, a.nom
        ");
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère les dernières actions effectuées sur les actionneurs.
     * @param int $limit
     * @return array
     */
    public function getRecentActivity($limit = 10) {
        $stmt = $this->db->prepare("
            SELECT
                al.action, al.timestamp,
                a.nom as actuator_name,
                u.username,
                t.name as team_name
            FROM actuator_logs al
            JOIN actionneurs a ON al.actionneur_id = a.id
            JOIN user u ON al.user_id = u.id_user
            LEFT JOIN teams t ON a.team_id = t.id
            ORDER BY al.timestamp DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Change l'état d'un actionneur et enregistre l'action.
     * @param int $actuatorId
     * @param string $action 'ON' ou 'OFF'
     * @param string $userId
     * @return bool
     */
    public function toggleState($actuatorId, $action, $userId) {
        try {
            $this->db->beginTransaction();

            $newState = ($action === 'ON') ? 1 : 0;
            
            $stmt = $this->db->prepare("UPDATE actionneurs SET current_state = ? WHERE id = ?");
            $stmt->execute([$newState, $actuatorId]);

            $stmt = $this->db->prepare("INSERT INTO actuator_logs (actionneur_id, action, user_id) VALUES (?, ?, ?)");
            $stmt->execute([$actuatorId, $action, $userId]);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Erreur dans ActuatorModel::toggleState: " . $e->getMessage());
            return false;
        }
    }
    
    public function create($name, $type, $teamId) {
        $stmt = $this->db->prepare("INSERT INTO actionneurs (nom, type, team_id) VALUES (?, ?, ?)");
        return $stmt->execute([$name, $type, $teamId]);
    }

    public function update($id, $name, $isActive) {
        $stmt = $this->db->prepare("UPDATE actionneurs SET nom = ?, is_active = ? WHERE id = ?");
        return $stmt->execute([$name, $isActive, $id]);
    }

    public function delete($id) {
         try {
            $this->db->beginTransaction();
            // La suppression en cascade (ON DELETE CASCADE) dans la BDD devrait gérer les logs
            $stmt = $this->db->prepare("DELETE FROM actionneurs WHERE id = ?");
            $success = $stmt->execute([$id]);
            $this->db->commit();
            return $success;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}