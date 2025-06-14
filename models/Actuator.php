<?php
// models/Actuator.php

class Actuator {
    private $db_remote; // Pour la table 'actionneurs'
    private $db_local;  // Pour la table 'actuator_logs'

    public function __construct() {
        $this->db_remote = Database::getConnection('remote');
        $this->db_local = Database::getConnection('local');
    }

    /**
     * Trouve un actionneur par son ID depuis la BD distante.
     * @param int $id
     * @return array|false
     */
    public function findById($id) {
        $stmt = $this->db_remote->prepare("
            SELECT id, nom as name, type, is_active, current_state
            FROM actionneurs
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Récupère tous les actionneurs depuis la BD distante.
     * @return array
     */
    public function findAll() {
        $stmt = $this->db_remote->query("
            SELECT id, nom as name, type, is_active, current_state
            FROM actionneurs
            ORDER BY nom
        ");
        return $stmt->fetchAll();
    }

    /**
     * Récupère tous les actionneurs actifs depuis la BD distante.
     * @return array
     */
    public function findAllActive() {
        $stmt = $this->db_remote->query("
            SELECT id, nom as name, type, is_active, current_state
            FROM actionneurs
            WHERE is_active = 1
            ORDER BY nom
        ");
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère l'activité récente en interrogeant les deux bases de données.
     * @param int $limit
     * @return array
     */

    /**
     * Change l'état d'un actionneur (BD distante) et enregistre l'action (BD locale).
     * @return bool
     */
    public function toggleState($actuatorId, $action, $userId) {
        try {
            // 1. Mettre à jour la BD distante
            $this->db_remote->beginTransaction();
            $newState = ($action === 'ON') ? 1 : 0;
            $stmt_remote = $this->db_remote->prepare("UPDATE actionneurs SET current_state = ? WHERE id = ?");
            $stmt_remote->execute([$newState, $actuatorId]);
            $this->db_remote->commit();
            
            // 2. Journaliser dans la BD locale
            $stmt_local = $this->db_local->prepare("INSERT INTO actuator_logs (actionneur_id, action, user_id) VALUES (?, ?, ?)");
            $stmt_local->execute([$actuatorId, $action, $userId]);

            return true;
        } catch (Exception $e) {
            if ($this->db_remote->inTransaction()) {
                $this->db_remote->rollBack();
            }
            error_log("Erreur toggleState multi-BD: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Crée un nouvel actionneur dans la BD distante.
     * @return bool
     */
    public function create($name, $type) {
        $stmt = $this->db_remote->prepare("INSERT INTO actionneurs (nom, type) VALUES (?, ?)");
        return $stmt->execute([$name, $type]);
    }

    /**
     * Met à jour un actionneur dans la BD distante.
     * @return bool
     */
    public function update($id, $name, $isActive) {
        $stmt = $this->db_remote->prepare("UPDATE actionneurs SET nom = ?, is_active = ? WHERE id = ?");
        return $stmt->execute([$name, $isActive, $id]);
    }

    /**
     * Supprime un actionneur de la BD distante.
     * Note : Les logs dans la BD locale ne seront pas supprimés, ce qui est souvent le comportement souhaité.
     * @return bool
     */
    public function delete($id) {
        try {
            $stmt = $this->db_remote->prepare("DELETE FROM actionneurs WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            return false;
        }
    }
}