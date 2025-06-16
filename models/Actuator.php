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

        // Si l'actionneur est trouvé, on s'assure que l'état n'est pas nul
        if ($actuator) {
            $actuator['etat'] = $actuator['etat'] ?? 0; // Par défaut à 0 (OFF)
        }

        return $actuator;
    }

   /**
     * Récupère tous les actionneurs avec leur état le plus récent.
     * La clé retournée est maintenant 'etat' pour correspondre à la base de données.
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
                 LIMIT 1) as etat -- On utilise 'etat' comme nom de clé
            FROM actionneurs a
            ORDER BY a.nom
        ");
        
        $actuators = $stmt->fetchAll();
        
        // S'assurer que 'etat' n'est pas NULL s'il n'y a jamais eu d'état enregistré.
        foreach ($actuators as &$actuator) {
            $actuator['etat'] = $actuator['etat'] ?? 0; // Par défaut à 0 (OFF)
        }

        return $actuators;
    }

    /**
     * Récupère tous les actionneurs dont le dernier état connu est "ON" (actif).
     * @return array
     */
    public function findAllActive() {
        // La requête est modifiée pour joindre la table des états
        // et filtrer sur l'état le plus récent.
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
        
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère l'activité récente en interrogeant les deux bases de données.
     * @param int $limit
     * @return array
     */

    /**
     * Change l'état d'un actionneur en insérant une nouvelle ligne dans etats_actionneurs
     * et enregistre l'action dans les logs locaux.
     * @return bool
     */
    public function toggleState($actuatorId, $action, $userId) {
        $newState = ($action === 'ON') ? 1 : 0;

        try {
            // 1. Insérer le nouvel état dans la BD distante
            $this->db_remote->beginTransaction();
            $stmt_remote = $this->db_remote->prepare("
                INSERT INTO etats_actionneurs (actionneur_id, etat) VALUES (?, ?)
            ");
            $stmt_remote->execute([$actuatorId, $newState]);
            $this->db_remote->commit();
            
            // 2. Journaliser (log) l'action dans la BD locale
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
     * Met à jour le nom ou le type d'un actionneur.
     * @return bool
     */
    public function update($id, $name) {
        $stmt = $this->db_remote->prepare("UPDATE actionneurs SET nom = ? WHERE id = ?");
        return $stmt->execute([$name,$id]);
    }

    /**
     * Supprime un actionneur de la BD distante.
     * Note : Les logs dans la BD locale ne seront pas supprimés, ce qui est souvent le comportement souhaité.
     * @return bool
     */
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