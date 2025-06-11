<?php
// models/Sensor.php
class Sensor {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAllSensors() {
        $stmt = $this->db->query("
            SELECT c.id, c.nom as name, c.type, c.unite as unit, 
                   COALESCE(c.team_id, 1) as team_id, 
                   COALESCE(c.is_active, 1) as is_active,
                   t.name as team_name 
            FROM capteurs c
            LEFT JOIN teams t ON c.team_id = t.id
            WHERE COALESCE(c.is_active, 1) = 1
            ORDER BY t.name, c.nom
        ");
        return $stmt->fetchAll();
    }
    
    public function getSensorsByTeam($teamId) {
        $stmt = $this->db->prepare("
            SELECT c.id, c.nom as name, c.type, c.unite as unit, 
                   COALESCE(c.team_id, 1) as team_id,
                   COALESCE(c.is_active, 1) as is_active,
                   t.name as team_name 
            FROM capteurs c
            LEFT JOIN teams t ON c.team_id = t.id
            WHERE COALESCE(c.team_id, 1) = ? AND COALESCE(c.is_active, 1) = 1
            ORDER BY c.nom
        ");
        $stmt->execute([$teamId]);
        return $stmt->fetchAll();
    }
    
    public function getSensorWithLatestData($sensorId) {
        $stmt = $this->db->prepare("
            SELECT 
                c.id, c.nom as name, c.type, c.unite as unit,
                COALESCE(c.team_id, 1) as team_id,
                t.name as team_name,
                m.valeur as value,
                m.date_heure as last_reading
            FROM capteurs c
            LEFT JOIN teams t ON c.team_id = t.id
            LEFT JOIN mesures m ON c.id = m.capteur_id
            WHERE c.id = ?
            ORDER BY m.date_heure DESC
            LIMIT 1
        ");
        $stmt->execute([$sensorId]);
        return $stmt->fetch();
    }
    
    public function getSensorData($sensorId, $limit = 100, $startDate = null, $endDate = null) {
        $sql = "
            SELECT valeur as value, date_heure as timestamp 
            FROM mesures 
            WHERE capteur_id = ?
        ";
        $params = [$sensorId];
        
        if ($startDate) {
            $sql .= " AND date_heure >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND date_heure <= ?";
            $params[] = $endDate;
        }
        
        $sql .= " ORDER BY date_heure DESC LIMIT ?";
        $params[] = $limit;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function addSensorData($sensorId, $value) {
        $stmt = $this->db->prepare("
            INSERT INTO mesures (capteur_id, valeur) 
            VALUES (?, ?)
        ");
        return $stmt->execute([$sensorId, $value]);
    }
    
    public function addSensor($name, $type, $unit, $teamId) {
        $stmt = $this->db->prepare("
            INSERT INTO capteurs (nom, type, unite, team_id) 
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$name, $type, $unit, $teamId]);
    }
    
    public function updateSensor($id, $name, $type, $unit, $isActive) {
        $stmt = $this->db->prepare("
            UPDATE capteurs 
            SET nom = ?, type = ?, unite = ?, is_active = ? 
            WHERE id = ?
        ");
        return $stmt->execute([$name, $type, $unit, $isActive, $id]);
    }
    
    public function deleteSensor($id) {
        // Supprimer d'abord les données associées
        $stmt = $this->db->prepare("DELETE FROM mesures WHERE capteur_id = ?");
        $stmt->execute([$id]);
        
        // Puis supprimer le capteur
        $stmt = $this->db->prepare("DELETE FROM capteurs WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getStatistics($sensorId, $period = '24h') {
        $dateCondition = '';
        switch ($period) {
            case '1h':
                $dateCondition = "date_heure >= DATE_SUB(NOW(), INTERVAL 1 HOUR)";
                break;
            case '24h':
                $dateCondition = "date_heure >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
                break;
            case '7d':
                $dateCondition = "date_heure >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                break;
            case '30d':
                $dateCondition = "date_heure >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                break;
            default:
                $dateCondition = "date_heure >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
        }
        
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_readings,
                AVG(valeur) as avg_value,
                MIN(valeur) as min_value,
                MAX(valeur) as max_value,
                STDDEV(valeur) as std_dev
            FROM mesures 
            WHERE capteur_id = ? AND {$dateCondition}
        ");
        $stmt->execute([$sensorId]);
        return $stmt->fetch();
    }
    
    public function getAggregatedData($sensorId, $interval = 'hour', $period = '24h') {
        $dateCondition = '';
        $groupBy = '';
        
        switch ($period) {
            case '24h':
                $dateCondition = "date_heure >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
                break;
            case '7d':
                $dateCondition = "date_heure >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                break;
            case '30d':
                $dateCondition = "date_heure >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                break;
        }
        
        switch ($interval) {
            case 'hour':
                $groupBy = "DATE_FORMAT(date_heure, '%Y-%m-%d %H:00:00')";
                break;
            case 'day':
                $groupBy = "DATE_FORMAT(date_heure, '%Y-%m-%d')";
                break;
            case 'minute':
                $groupBy = "DATE_FORMAT(date_heure, '%Y-%m-%d %H:%i:00')";
                break;
        }
        
        $stmt = $this->db->prepare("
            SELECT 
                {$groupBy} as time_group,
                AVG(valeur) as avg_value,
                MIN(valeur) as min_value,
                MAX(valeur) as max_value,
                COUNT(*) as reading_count
            FROM mesures 
            WHERE capteur_id = ? AND {$dateCondition}
            GROUP BY time_group
            ORDER BY time_group
        ");
        $stmt->execute([$sensorId]);
        return $stmt->fetchAll();
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
    
    public function getAlerts($teamId = null) {
        // Récupérer les alertes basées sur des seuils prédéfinis
        $sql = "
            SELECT 
                s.id, s.name, s.type, s.unit,
                t.name as team_name,
                sd.value, sd.timestamp,
                CASE 
                    WHEN s.type = 'temperature' AND (sd.value < 15 OR sd.value > 35) THEN 'critical'
                    WHEN s.type = 'humidity' AND (sd.value < 30 OR sd.value > 90) THEN 'warning'
                    WHEN s.type = 'soil_moisture' AND sd.value < 25 THEN 'critical'
                    ELSE 'normal'
                END as alert_level
            FROM sensors s
            JOIN teams t ON s.team_id = t.id
            JOIN sensor_data sd ON s.id = sd.sensor_id
            WHERE s.is_active = 1
            AND sd.timestamp >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
            AND (
                (s.type = 'temperature' AND (sd.value < 15 OR sd.value > 35)) OR
                (s.type = 'humidity' AND (sd.value < 30 OR sd.value > 90)) OR
                (s.type = 'soil_moisture' AND sd.value < 25)
            )
        ";
        
        if ($teamId) {
            $sql .= " AND s.team_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$teamId]);
        } else {
            $stmt = $this->db->query($sql);
        }
        
        return $stmt->fetchAll();
    }
}
?>