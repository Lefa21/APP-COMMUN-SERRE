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
            c.id, c.nom as name, c.unite as unit,
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
     
    public function addSensor($name, $unit) {
        $stmt = $this->db->prepare("
            INSERT INTO capteurs (nom, unite) 
            VALUES (?, ?)
        ");
        return $stmt->execute([$name, $unit]);
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
        
        switch ($sensor['nom']) {
            case 'temperature':
                $baseValue = 22;
                $variation = 8;
                break;
            case 'humidite':
                $baseValue = 60;
                $variation = 20;
                break;
                /*
            case 'soil_moisture':
                $baseValue = 45;
                $variation = 15;
                break;
                */
            case 'luminosite':
                $baseValue = 800;
                $variation = 200;
                break;
                /*
            case 'ph':
                $baseValue = 6.5;
                $variation = 1;
                break;
            case 'co2':
                $baseValue = 400;
                $variation = 100;
                break;
                */
        }
        
        // Générer une valeur réaliste
        $value = $baseValue + (rand(-100, 100) / 100) * $variation;
        $value = round($value, 2);
        
        return $this->addSensorData($sensorId, $value);
    }
    
    
   
    /**
     * Récupère les alertes pour les capteurs qui sont marqués comme actifs.
     * @return array
     */
    public function getAlerts() {
        // La requête est maintenant beaucoup plus simple.
        // La jointure et la condition sur la table 'teams' ont été supprimées.
        // La condition WHERE utilise maintenant directement c.is_actif.
        $sql = "
            SELECT 
                c.id, 
                c.nom as name, 
                c.unite as unit,
                m.valeur as value, 
                m.date_heure as timestamp,
                -- Définition des niveaux d'alerte basés sur les seuils
                CASE 
                    WHEN name = 'temperature' AND (m.valeur < 15 OR m.valeur > 35) THEN 'critical'
                    WHEN name = 'humidite' AND (m.valeur < 30 OR m.valeur > 90) THEN 'warning'
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
            -- Condition principale : on ne vérifie que les capteurs actifs
            WHERE c.is_actif = 1
            -- Et que la dernière mesure soit récente (moins d'une heure)
            AND m.date_heure >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
            -- Et que la valeur dépasse un des seuils d'alerte
            AND (
                (c.name = 'temperature' AND (m.valeur < 15 OR m.valeur > 35)) OR
                (c.name = 'humidite' AND (m.valeur < 30 OR m.valeur > 90)) OR
                (c.name = 'luminosite' AND m.valeur < 25)
            )
        ";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }


/**
     * Récupère tous les capteurs avec leur statut 'is_actif' direct.
     * La sous-requête complexe a été supprimée.
     * @return array
     */
    public function getAllSensors() {
        $stmt = $this->db->query("
            SELECT 
                id, 
                nom as name,
                unite as unit,
                is_actif as is_active -- On lit directement la colonne
            FROM capteurs
            ORDER BY nom
        ");
        return $stmt->fetchAll();
    }

   /**
     * Récupère TOUS les capteurs ACTIFS avec leur dernière lecture.
     * La requête a été simplifiée pour utiliser la colonne `is_actif`.
     * @return array
     */
    public function getAllSensorsWithLastReading() {
        $stmt = $this->db->query("
            SELECT 
                c.id, 
                c.nom as name, 
                c.unite as unit, 
                c.is_actif,
                (SELECT m.valeur FROM mesures m WHERE m.capteur_id = c.id ORDER BY m.date_heure DESC LIMIT 1) as value,
                (SELECT m.date_heure FROM mesures m WHERE m.capteur_id = c.id ORDER BY m.date_heure DESC LIMIT 1) as timestamp
            FROM capteurs c
            WHERE c.is_actif = 1
            ORDER BY c.nom
        ");
        return $stmt->fetchAll();
    }

    
    public function create($name,$unit) {
    // La colonne is_actif est définie à 1 (TRUE) par défaut lors de la création.
    $stmt = $this->db->prepare(
        "INSERT INTO capteurs (nom,unite, is_actif) VALUES (?, ?, 0)"
    );
    return $stmt->execute([$name,$unit]);
}

   /**
 * Met à jour un capteur.
 * @return bool
 */
public function update($id, $name,$unit, $isActive) {
    $stmt = $this->db->prepare("
        UPDATE capteurs 
        SET nom = ?, unite = ?, is_actif = ? 
        WHERE id = ?
    ");
    return $stmt->execute([$name, $unit, $isActive, $id]);
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
     * Compte le nombre de capteurs actifs.
     * La requête a été simplifiée pour utiliser directement la colonne is_actif.
     * @return int
     */
    public function countActive() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM capteurs WHERE is_actif = 1");
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