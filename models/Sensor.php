<?php
// models/Sensor.php
class Sensor {
    private $db;
    
  public function __construct() {
        // Ce modèle gère les capteurs et mesures, qui sont sur la BD distante.
        $this->db = Database::getConnection('local');
    }
    

    
    public function getSensorWithLatestData($sensorId) {
    $stmt = $this->db->prepare("
        SELECT 
            c.id, c.nom as name, c.type, c.unite as unit,
            m.valeur as value,
            m.date_heure as last_reading
        FROM capteurs c
        LEFT JOIN mesures m ON c.id = m.capteur_id
        WHERE c.id = ?
        ORDER BY m.date_heure DESC
        LIMIT 1
    ");
    $stmt->execute([$sensorId]);
    return $stmt->fetch();
}
     
    public function addSensor($name, $type, $unit) {
        $stmt = $this->db->prepare("
            INSERT INTO capteurs (nom, type, unite) 
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$name, $type, $unit]);
    }
    
    public function deleteSensor($id) {
        // Supprimer d'abord les données associées
        $stmt = $this->db->prepare("DELETE FROM mesures WHERE capteur_id = ?");
        $stmt->execute([$id]);
        
        // Puis supprimer le capteur
        $stmt = $this->db->prepare("DELETE FROM capteurs WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    
    public function simulateData($sensorId) {
        // Fonction pour simuler des données de capteur (utile pour les tests)
        $sensor = $this->getSensorWithLatestData($sensorId);
        if (!$sensor) return false;
        
        $baseValue = 20; // Valeur de base
        $variation = 5;  // Variation possible
        
        switch ($sensor['type']) {
            case 'temperature':
                $baseValue = 22;
                $variation = 8;
                break;
            case 'humidity':
                $baseValue = 60;
                $variation = 20;
                break;
            case 'soil_moisture':
                $baseValue = 45;
                $variation = 15;
                break;
            case 'light':
                $baseValue = 800;
                $variation = 200;
                break;
            case 'ph':
                $baseValue = 6.5;
                $variation = 1;
                break;
            case 'co2':
                $baseValue = 400;
                $variation = 100;
                break;
        }
        
        // Générer une valeur réaliste
        $value = $baseValue + (rand(-100, 100) / 100) * $variation;
        $value = round($value, 2);
        
        return $this->addSensorData($sensorId, $value);
    }
    
    
   /**
     * Récupère les alertes actives pour les capteurs qui sont actuellement
     * contrôlés par un actionneur en marche (état = 1).
     * @return array
     */
    public function getAlerts() {
        $sql = "
            SELECT 
                c.id, 
                c.nom as name, 
                c.type, 
                c.unite as unit,
                m.valeur as value, 
                m.date_heure as timestamp,
                -- Définition des niveaux d'alerte basés sur les seuils
                CASE 
                    WHEN c.type = 'temperature' AND (m.valeur < 15 OR m.valeur > 35) THEN 'critical'
                    WHEN c.type = 'humidity' AND (m.valeur < 30 OR m.valeur > 90) THEN 'warning'
                    WHEN c.type = 'soil_moisture' AND m.valeur < 25 THEN 'critical'
                    ELSE 'normal'
                END as alert_level
            FROM capteurs c
            -- On joint la dernière mesure de chaque capteur pour avoir la valeur la plus récente
            JOIN mesures m ON m.id = (
                SELECT id FROM mesures sub_m
                WHERE sub_m.capteur_id = c.id
                ORDER BY date_heure DESC
                LIMIT 1
            )
            -- Condition principale : on ne vérifie que les capteurs dont l'actionneur lié est actif (état = 1)
            WHERE 
                -- Cette sous-requête vérifie l'état de l'actionneur lié
                (SELECT e.etat 
                 FROM etats_actionneurs e 
                 WHERE e.actionneur_id = c.linked_actuator_id
                 ORDER BY e.date_heure DESC 
                 LIMIT 1) = 1
            -- Et que la dernière mesure soit récente (moins d'une heure)
            AND m.date_heure >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
            -- Et que la valeur dépasse un des seuils d'alerte
            AND (
                (c.type = 'temperature' AND (m.valeur < 15 OR m.valeur > 35)) OR
                (c.type = 'humidity' AND (m.valeur < 30 OR m.valeur > 90)) OR
                (c.type = 'soil_moisture' AND m.valeur < 25)
            )
        ";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }


   /**
     * Récupère tous les capteurs avec leur état dynamique calculé.
     * L'état "is_active" est déterminé par l'état de l'actionneur lié.
     * @return array
     */
    public function getAllSensors() {
        // Cette requête utilise une sous-requête pour trouver l'état le plus récent
        // de l'actionneur lié à chaque capteur.
        $stmt = $this->db->query("
            SELECT 
                c.id, 
                c.nom as name, 
                c.type, 
                c.unite as unit,
                c.linked_actuator_id,
                -- Calcul dynamique de l'état du capteur.
                -- COALESCE renvoie 0 si la sous-requête ne trouve rien (pas d'actionneur lié ou pas d'état).
                COALESCE((SELECT e.etat 
                          FROM etats_actionneurs e 
                          WHERE e.actionneur_id = c.linked_actuator_id
                          ORDER BY e.date_heure DESC 
                          LIMIT 1), 0) as is_active
            FROM capteurs c
            ORDER BY c.nom
        ");
        
        return $stmt->fetchAll();
    }

    /**
     * Récupère tous les capteurs avec leur état dynamique et leur dernière lecture.
     * L'état "is_active" est maintenant calculé en fonction de l'état de l'actionneur lié.
     * @return array
     */
    public function getAllSensorsWithLastReading() {
        $stmt = $this->db->query("
            SELECT 
                c.id, 
                c.nom as name, 
                c.type, 
                c.unite as unit,
                -- Lecture de la dernière valeur du capteur
                (SELECT m.valeur FROM mesures m WHERE m.capteur_id = c.id ORDER BY m.date_heure DESC LIMIT 1) as value,
                -- Lecture de la date de la dernière valeur
                (SELECT m.date_heure FROM mesures m WHERE m.capteur_id = c.id ORDER BY m.date_heure DESC LIMIT 1) as timestamp,
                -- Calcul dynamique de l'état du capteur
                (SELECT e.etat 
                 FROM etats_actionneurs e 
                 WHERE e.actionneur_id = c.linked_actuator_id -- On utilise la nouvelle colonne de liaison
                 ORDER BY e.date_heure DESC 
                 LIMIT 1
                ) as is_active -- On garde le nom 'is_active' pour que les vues n'aient pas à changer
            FROM capteurs c
            ORDER BY c.nom
        ");
        
        $sensors = $stmt->fetchAll();

        // S'assurer que les valeurs par défaut sont correctes (0 ou false si aucune donnée)
        foreach ($sensors as &$sensor) {
            $sensor['is_active'] = $sensor['is_active'] ?? 0;
        }

        return $sensors;
    }

    
    /**
     * Crée un nouveau capteur.
     * @return bool
     */
    public function create($name, $type, $unit) {
        $stmt = $this->db->prepare("INSERT INTO capteurs (nom, type, unite) VALUES (?, ?, ?)");
        return $stmt->execute([$name, $type, $unit]);
    }

   /**
     * Met à jour un capteur.
     * La liaison à un actionneur (linked_actuator_id) n'est plus modifiable ici.
     * * @param int $id
     * @param string $name
     * @param string $type
     * @param string $unit
     * @return bool
     */
    public function update($id, $name, $type, $unit) {
        $stmt = $this->db->prepare("
            UPDATE capteurs 
            SET nom = ?, type = ?, unite = ?
            WHERE id = ?
        ");
        return $stmt->execute([$name, $type, $unit, $id]);
    }

    /**
     * Supprime un capteur et ses données associées.
     * @return bool
     */
    public function delete($id) {
        // La contrainte ON DELETE CASCADE dans la BDD devrait s'occuper des mesures.
        // Sinon, il faudrait décommenter la ligne suivante :
        // $this->db->prepare("DELETE FROM mesures WHERE capteur_id = ?")->execute([$id]);
        
        $stmt = $this->db->prepare("DELETE FROM capteurs WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Récupère l'historique des données pour un capteur.
     * @return array
     */
    public function getSensorData($sensorId, $limit = 100, $startDate = null, $endDate = null) {
        $sql = "SELECT valeur as value, date_heure as timestamp FROM mesures WHERE capteur_id = ?";
        $params = [$sensorId];
        if ($startDate) { $sql .= " AND date_heure >= ?"; $params[] = $startDate; }
        if ($endDate) { $sql .= " AND date_heure <= ?"; $params[] = $endDate; }
        $sql .= " ORDER BY date_heure DESC LIMIT ?";
        $params[] = $limit;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Ajoute une nouvelle mesure pour un capteur.
     * @return bool
     */
    public function addSensorData($sensorId, $value) {
        $stmt = $this->db->prepare("INSERT INTO mesures (capteur_id, valeur) VALUES (?, ?)");
        return $stmt->execute([$sensorId, $value]);
    }

    /**
     * Récupère les statistiques pour un capteur sur une période donnée.
     * @return array|false
     */
    public function getStatistics($sensorId, $period = '24h') {
        $dateCondition = "date_heure >= DATE_SUB(NOW(), INTERVAL " . $this->periodToInterval($period) . ")";
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total_readings, AVG(valeur) as avg_value, MIN(valeur) as min_value,
                   MAX(valeur) as max_value, STDDEV(valeur) as std_dev
            FROM mesures WHERE capteur_id = ? AND {$dateCondition}
        ");
        $stmt->execute([$sensorId]);
        return $stmt->fetch();
    }
    
    /**
     * Récupère des données agrégées pour les graphiques.
     * @return array
     */
    public function getAggregatedData($sensorId, $interval = 'hour', $period = '24h') {
        $dateCondition = "date_heure >= DATE_SUB(NOW(), INTERVAL " . $this->periodToInterval($period) . ")";
        
        switch ($interval) {
            case 'day': $groupBy = "DATE_FORMAT(date_heure, '%Y-%m-%d')"; break;
            case 'minute': $groupBy = "DATE_FORMAT(date_heure, '%Y-%m-%d %H:%i:00')"; break;
            case 'hour':
            default: $groupBy = "DATE_FORMAT(date_heure, '%Y-%m-%d %H:00:00')";
        }
        
        $stmt = $this->db->prepare("
            SELECT {$groupBy} as time_group, AVG(valeur) as avg_value
            FROM mesures WHERE capteur_id = ? AND {$dateCondition}
            GROUP BY time_group ORDER BY time_group
        ");
        $stmt->execute([$sensorId]);
        return $stmt->fetchAll();
    }

/**
     * Compte le nombre de capteurs actuellement actifs.
     * Un capteur est considéré comme actif si son actionneur lié a un état de 1 (ON).
     * @return int
     */
    public function countActive() {
        // Cette requête compte les capteurs pour lesquels la sous-requête (qui récupère l'état
        // de l'actionneur lié) retourne 1.
        $stmt = $this->db->query("
            SELECT COUNT(*) 
            FROM capteurs c 
            WHERE 
                (SELECT e.etat 
                 FROM etats_actionneurs e 
                 WHERE e.actionneur_id = c.linked_actuator_id 
                 ORDER BY e.date_heure DESC 
                 LIMIT 1) = 1
        ");
        
        return (int) $stmt->fetchColumn();
    }

    /**
     * Helper privé pour convertir la période en intervalle SQL.
     * @param string $period
     * @return string
     */
    private function periodToInterval($period) {
        switch ($period) {
            case '1h': return '1 HOUR';
            case '7d': return '7 DAY';
            case '30d': return '30 DAY';
            case '24h':
            default: return '24 HOUR';
        }
    }
}
?>