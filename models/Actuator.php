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
     * Trouve un actionneur par son ID et récupère son état le plus récent.
     * @param int $id
     * @return array|false
     */
    public function findById($id) {
        $stmt = $this->db_remote->prepare("
            SELECT 
                a.id, 
                a.nom as name, 
                (SELECT e.etat 
                 FROM etats_actionneurs e 
                 WHERE e.actionneur_id = a.id 
                 ORDER BY e.date_heure DESC 
                 LIMIT 1) as etat
            FROM actionneurs a
            WHERE a.id = ?
        ");

        $stmt->execute([$id]);
        $actuator = $stmt->fetch();

        if ($actuator) {
            $actuator['etat'] = $actuator['etat'] ?? 0;
        }

        return $actuator;
    }

    /**
     * Récupère tous les actionneurs avec leur état le plus récent.
     * @return array
     */
    public function findAll() {
        $stmt = $this->db_remote->query("
            SELECT 
                a.id, 
                a.nom as name, 
                (SELECT e.etat 
                 FROM etats_actionneurs e 
                 WHERE e.actionneur_id = a.id 
                 ORDER BY e.date_heure DESC 
                 LIMIT 1) as etat
            FROM actionneurs a
            ORDER BY a.nom
        ");

        $actuators = $stmt->fetchAll();

        foreach ($actuators as &$actuator) {
            $actuator['etat'] = $actuator['etat'] ?? 0;
        }

        return $actuators;
    }

    /**
     * Récupère tous les actionneurs dont le dernier état est ON.
     * @return array
     */
    public function findAllActive() {
        $stmt = $this->db_remote->query("
            SELECT 
                a.id, 
                a.nom as name, 
                (SELECT e.etat 
                 FROM etats_actionneurs e 
                 WHERE e.actionneur_id = a.id 
                 ORDER BY e.date_heure DESC 
                 LIMIT 1) as etat
            FROM actionneurs a
            HAVING etat = 1
            ORDER BY a.nom
        ");

        return $stmt->fetchAll();
    }

    /**
     * Change l'état d'un actionneur en insérant une nouvelle ligne dans etats_actionneurs
     * et enregistre l'action dans les logs locaux.
     * @return bool
     */
    public function toggleState($actuatorId, $action, $userId) {
        $newState = ($action === 'ON') ? 1 : 0;

        try {
            $this->db_remote->beginTransaction();

            $stmt_remote = $this->db_remote->prepare("
                INSERT INTO etats_actionneurs (actionneur_id, etat) VALUES (?, ?)
            ");
            $stmt_remote->execute([$actuatorId, $newState]);

            $this->db_remote->commit();

            $stmt_local = $this->db_local->prepare("
                INSERT INTO actuator_logs (actionneur_id, action, user_id) VALUES (?, ?, ?)
            ");
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
     * Crée un nouvel actionneur et initialise son état à 0 (OFF).
     * @return bool
     */
    public function create($name) {
        try {
            $this->db_remote->beginTransaction();

            $stmt = $this->db_remote->prepare("INSERT INTO actionneurs (nom) VALUES (?)");
            $stmt->execute([$name]);

            $actuatorId = $this->db_remote->lastInsertId();
            $stmt_etat = $this->db_remote->prepare("INSERT INTO etats_actionneurs (actionneur_id, etat) VALUES (?, 0)");
            $stmt_etat->execute([$actuatorId]);

            $this->db_remote->commit();
            return true;
        } catch (Exception $e) {
            $this->db_remote->rollBack();
            return false;
        }
    }

    /**
     * Met à jour le nom d'un actionneur.
     * @return bool
     */
    public function update($id, $name) {
        $stmt = $this->db_remote->prepare("UPDATE actionneurs SET nom = ? WHERE id = ?");
        return $stmt->execute([$name, $id]);
    }

    /**
     * Supprime un actionneur et tous ses états associés.
     * @return bool
     */
    public function delete($id) {
        try {
            $this->db_remote->beginTransaction();
            $this->db_remote->prepare("DELETE FROM etats_actionneurs WHERE actionneur_id = ?")->execute([$id]);
            $this->db_remote->prepare("DELETE FROM actionneurs WHERE id = ?")->execute([$id]);
            $this->db_remote->commit();
            return true;
        } catch (Exception $e) {
            $this->db_remote->rollBack();
            return false;
        }
    }
}
