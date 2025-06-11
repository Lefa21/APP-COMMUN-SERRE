<?php
// controllers/ApiController.php
require_once BASE_PATH . '/controllers/BaseController.php';
require_once BASE_PATH . '/models/Sensor.php';

class ApiController extends BaseController {
    
    private $sensorModel;
    
    public function __construct() {
        parent::__construct();
        $this->sensorModel = new Sensor();
    }
    
    /**
     * Récupérer tous les capteurs avec leurs dernières données
     */
    public function sensors() {
        $this->requireLogin();
        
        try {
            $sensors = $this->getAllSensorsWithData();
            $this->jsonResponse([
                'success' => true,
                'data' => $sensors,
                'timestamp' => date('Y-m-d H:i:s'),
                'count' => count($sensors)
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Erreur lors de la récupération des capteurs',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Récupérer les données d'un capteur spécifique
     */
    public function sensorData() {
        $this->requireLogin();
        
        $sensorId = (int)($_GET['sensor_id'] ?? 0);
        $period = $_GET['period'] ?? '24h';
        $format = $_GET['format'] ?? 'json';
        $limit = (int)($_GET['limit'] ?? 100);
        
        if (!$sensorId) {
            $this->jsonResponse(['error' => 'ID capteur requis'], 400);
        }
        
        try {
            $sensor = $this->sensorModel->getSensorWithLatestData($sensorId);
            if (!$sensor) {
                $this->jsonResponse(['error' => 'Capteur non trouvé'], 404);
            }
            
            $data = $this->sensorModel->getSensorData($sensorId, $limit, $this->getPeriodStartDate($period));
            $stats = $this->sensorModel->getStatistics($sensorId, $period);
            
            if ($format === 'csv') {
                $this->exportCSV($data, $sensor);
            } else {
                $this->jsonResponse([
                    'success' => true,
                    'sensor' => $sensor,
                    'data' => $data,
                    'statistics' => $stats,
                    'period' => $period,
                    'count' => count($data)
                ]);
            }
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Erreur lors de la récupération des données'
            ], 500);
        }
    }
    
    /**
     * Récupérer tous les actionneurs avec leur état
     */
    public function actuators() {
        $this->requireLogin();
        
        try {
            $actuators = $this->getAllActuators();
            $this->jsonResponse([
                'success' => true,
                'data' => $actuators,
                'timestamp' => date('Y-m-d H:i:s'),
                'active_count' => count(array_filter($actuators, function($a) { return $a['current_state']; })),
                'total_count' => count($actuators)
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Erreur lors de la récupération des actionneurs'
            ], 500);
        }
    }
    
    /**
     * Ajouter des données de capteur (depuis microcontrôleur)
     */
    public function addSensorData() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Méthode POST requise'], 405);
        }
        
        // Authentification par clé API pour les microcontrôleurs
        $apiKey = $_SERVER['HTTP_API_KEY'] ?? $_POST['api_key'] ?? '';
        if (!$this->validateApiKey($apiKey)) {
            $this->jsonResponse(['error' => 'Clé API invalide'], 401);
        }
        
        $sensorId = (int)($_POST['sensor_id'] ?? 0);
        $value = (float)($_POST['value'] ?? 0);
        $timestamp = $_POST['timestamp'] ?? null;
        
        if (!$sensorId) {
            $this->jsonResponse(['error' => 'sensor_id requis'], 400);
        }
        
        if ($value === 0 && !isset($_POST['value'])) {
            $this->jsonResponse(['error' => 'value requise'], 400);
        }
        
        try {
            // Vérifier que le capteur existe
            $sensor = $this->sensorModel->getSensorWithLatestData($sensorId);
            if (!$sensor) {
                $this->jsonResponse(['error' => 'Capteur introuvable'], 404);
            }
            
            // Valider la valeur selon le type de capteur
            if (!$this->validateSensorValue($sensor['type'], $value)) {
                $this->jsonResponse(['error' => 'Valeur invalide pour ce type de capteur'], 400);
            }
            
            // Ajouter les données
            if ($timestamp) {
                $stmt = $this->db->prepare("
                    INSERT INTO mesures (capteur_id, valeur, date_heure) 
                    VALUES (?, ?, ?)
                ");
                $success = $stmt->execute([$sensorId, $value, $timestamp]);
            } else {
                $success = $this->sensorModel->addSensorData($sensorId, $value);
            }
            
            if ($success) {
                // Vérifier les alertes
                $alerts = $this->checkAlerts($sensorId, $value, $sensor);
                
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Données ajoutées avec succès',
                    'sensor_id' => $sensorId,
                    'value' => $value,
                    'alerts' => $alerts,
                    'timestamp' => date('Y-m-d H:i:s')
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'Erreur lors de l\'ajout des données'
                ], 500);
            }
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Erreur base de données',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Simuler des données pour un capteur
     */
    public function simulateData() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Méthode POST requise'], 405);
        }
        
        $sensorId = (int)($_POST['sensor_id'] ?? 0);
        $count = (int)($_POST['count'] ?? 1);
        
        if (!$sensorId || $count < 1 || $count > 100) {
            $this->jsonResponse(['error' => 'Paramètres invalides'], 400);
        }
        
        try {
            $successCount = 0;
            $values = [];
            
            for ($i = 0; $i < $count; $i++) {
                if ($this->sensorModel->simulateData($sensorId)) {
                    $successCount++;
                    // Récupérer la dernière valeur ajoutée
                    $lastData = $this->sensorModel->getSensorData($sensorId, 1);
                    if (!empty($lastData)) {
                        $values[] = $lastData[0]['value'];
                    }
                }
                usleep(100000); // 0.1 seconde entre chaque insertion
            }
            
            $this->jsonResponse([
                'success' => true,
                'message' => "{$successCount} mesures simulées ajoutées",
                'generated' => $successCount,
                'values' => $values,
                'sensor_id' => $sensorId
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Erreur lors de la simulation'
            ], 500);
        }
    }
    
    /**
     * Récupérer les alertes actives
     */
    public function alerts() {
        $this->requireLogin();
        
        try {
            $teamId = $this->isAdmin() ? null : $this->getUserTeamId();
            $alerts = $this->sensorModel->getAlerts($teamId);
            
            // Ajouter des informations contextuelles
            foreach ($alerts as &$alert) {
                $alert['severity'] = $this->getAlertSeverity($alert['alert_level']);
                $alert['recommendation'] = $this->getAlertRecommendation($alert);
                $alert['time_ago'] = $this->timeAgo($alert['timestamp']);
            }
            
            $this->jsonResponse([
                'success' => true,
                'alerts' => $alerts,
                'count' => count($alerts),
                'critical_count' => count(array_filter($alerts, function($a) { return $a['alert_level'] === 'critical'; })),
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Erreur lors de la récupération des alertes'
            ], 500);
        }
    }
    
    /**
     * Récupérer les données pour graphiques
     */
    public function chartData() {
        $this->requireLogin();
        
        $sensorId = (int)($_GET['sensor_id'] ?? 0);
        $interval = $_GET['interval'] ?? 'hour';
        $period = $_GET['period'] ?? '24h';
        
        if (!$sensorId) {
            $this->jsonResponse(['error' => 'ID capteur requis'], 400);
        }
        
        try {
            $data = $this->sensorModel->getAggregatedData($sensorId, $interval, $period);
            
            // Formater les données pour Chart.js
            $chartData = [
                'labels' => [],
                'datasets' => [
                    [
                        'label' => 'Moyenne',
                        'data' => [],
                        'borderColor' => '#2d5a27',
                        'backgroundColor' => 'rgba(45, 90, 39, 0.1)',
                        'tension' => 0.4,
                        'fill' => true
                    ],
                    [
                        'label' => 'Min/Max',
                        'data' => [],
                        'borderColor' => '#8bc34a',
                        'backgroundColor' => 'rgba(139, 195, 74, 0.05)',
                        'tension' => 0.2,
                        'fill' => false,
                        'pointRadius' => 2
                    ]
                ]
            ];
            
            foreach ($data as $point) {
                $chartData['labels'][] = $this->formatTimeLabel($point['time_group'], $interval);
                $chartData['datasets'][0]['data'][] = round($point['avg_value'], 2);
                $chartData['datasets'][1]['data'][] = [
                    round($point['min_value'], 2),
                    round($point['max_value'], 2)
                ];
            }
            
            $this->jsonResponse([
                'success' => true,
                'chartData' => $chartData,
                'period' => $period,
                'interval' => $interval,
                'dataPoints' => count($data)
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Erreur lors de la génération du graphique'
            ], 500);
        }
    }
    
    /**
     * Statistiques du système
     */
    public function systemStats() {
        $this->requireLogin();
        
        try {
            // Statistiques générales
            $stats = [
                'sensors' => [
                    'total' => $this->getCount('capteurs'),
                    'active' => $this->getCount('capteurs', 'is_active = 1'),
                    'with_data' => $this->getCountWithJoin()
                ],
                'actuators' => [
                    'total' => $this->getCount('actionneurs'),
                    'active' => $this->getCount('actionneurs', 'is_active = 1'),
                    'running' => $this->getCount('actionneurs', 'current_state = 1')
                ],
                'data' => [
                    'total_readings' => $this->getCount('mesures'),
                    'readings_today' => $this->getCount('mesures', 'DATE(date_heure) = CURDATE()'),
                    'readings_last_hour' => $this->getCount('mesures', 'date_heure >= DATE_SUB(NOW(), INTERVAL 1 HOUR)')
                ],
                'teams' => [
                    'total' => $this->getCount('teams'),
                    'active_users' => $this->getCount('user', 'role_id IS NOT NULL')
                ]
            ];
            
            // Ajout d'alertes actives
            $alerts = $this->sensorModel->getAlerts();
            $stats['alerts'] = [
                'total' => count($alerts),
                'critical' => count(array_filter($alerts, function($a) { return $a['alert_level'] === 'critical'; }))
            ];
            
            $this->jsonResponse([
                'success' => true,
                'stats' => $stats,
                'timestamp' => date('Y-m-d H:i:s'),
                'uptime' => $this->getSystemUptime()
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Erreur lors de la récupération des statistiques'
            ], 500);
        }
    }
    
    /**
     * Endpoint de santé du système
     */
    public function health() {
        try {
            // Vérifier la connexion BDD
            $this->db->query("SELECT 1");
            
            // Vérifier les capteurs actifs
            $activeSensors = $this->getCount('capteurs', 'is_active = 1');
            $recentData = $this->getCount('mesures', 'date_heure >= DATE_SUB(NOW(), INTERVAL 1 HOUR)');
            
            $health = [
                'status' => 'healthy',
                'database' => 'connected',
                'sensors' => [
                    'active' => $activeSensors,
                    'recent_data' => $recentData
                ],
                'timestamp' => date('Y-m-d H:i:s'),
                'version' => '1.0.0'
            ];
            
            // Déterminer le statut global
            if ($activeSensors === 0) {
                $health['status'] = 'warning';
                $health['message'] = 'Aucun capteur actif';
            } elseif ($recentData === 0) {
                $health['status'] = 'warning';
                $health['message'] = 'Aucune donnée récente';
            }
            
            $this->jsonResponse($health);
        } catch (Exception $e) {
            $this->jsonResponse([
                'status' => 'error',
                'database' => 'disconnected',
                'error' => 'Problème de connexion à la base de données',
                'timestamp' => date('Y-m-d H:i:s')
            ], 500);
        }
    }
    
    // ============ Méthodes privées ============
    
    private function getAllSensorsWithData() {
        $stmt = $this->db->prepare("
            SELECT 
                c.id, c.nom as name, c.type, c.unite as unit, 
                COALESCE(c.team_id, 1) as team_id,
                t.name as team_name,
                m.valeur as value, m.date_heure as timestamp,
                ROW_NUMBER() OVER (PARTITION BY c.id ORDER BY m.date_heure DESC) as rn
            FROM capteurs c
            LEFT JOIN teams t ON c.team_id = t.id
            LEFT JOIN mesures m ON c.id = m.capteur_id
            WHERE COALESCE(c.is_active, 1) = 1
        ");
        $stmt->execute();
        $results = $stmt->fetchAll();
        
        $sensors = [];
        foreach ($results as $row) {
            if ($row['rn'] == 1 || !isset($sensors[$row['id']])) {
                unset($row['rn']);
                $sensors[$row['id']] = $row;
            }
        }
        
        return array_values($sensors);
    }
    
    private function getAllActuators() {
        $stmt = $this->db->prepare("
            SELECT a.id, a.nom as name, a.type, 
                   COALESCE(a.team_id, 1) as team_id,
                   COALESCE(a.is_active, 1) as is_active,
                   COALESCE(a.current_state, 0) as current_state,
                   t.name as team_name 
            FROM actionneurs a
            LEFT JOIN teams t ON a.team_id = t.id
            ORDER BY t.name, a.nom
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    private function validateApiKey($apiKey) {
        // Clés API pour les équipes (à sécuriser en production)
        $validKeys = [
            'serre_team_1_key_2025',
            'serre_team_2_key_2025',
            'serre_team_3_key_2025',
            'serre_team_4_key_2025',
            'serre_team_5_key_2025',
            'serre_admin_master_key'
        ];
        
        return in_array($apiKey, $validKeys);
    }
    
    private function validateSensorValue($type, $value) {
        $ranges = [
            'temperature' => [-40, 80],
            'humidity' => [0, 100],
            'soil_moisture' => [0, 100],
            'light' => [0, 100000],
            'ph' => [0, 14],
            'co2' => [0, 10000]
        ];
        
        if (!isset($ranges[$type])) return true;
        
        return $value >= $ranges[$type][0] && $value <= $ranges[$type][1];
    }
    
    private function checkAlerts($sensorId, $value, $sensor) {
        $alerts = [];
        
        switch ($sensor['type']) {
            case 'temperature':
                if ($value < 15) {
                    $alerts[] = ['level' => 'critical', 'message' => 'Température trop basse'];
                } elseif ($value > 35) {
                    $alerts[] = ['level' => 'critical', 'message' => 'Température trop élevée'];
                } elseif ($value < 18 || $value > 30) {
                    $alerts[] = ['level' => 'warning', 'message' => 'Température à surveiller'];
                }
                break;
                
            case 'humidity':
            case 'soil_moisture':
                if ($value < 25) {
                    $alerts[] = ['level' => 'critical', 'message' => 'Humidité trop basse'];
                } elseif ($value > 90) {
                    $alerts[] = ['level' => 'warning', 'message' => 'Humidité élevée'];
                }
                break;
                
            case 'ph':
                if ($value < 5.5 || $value > 8.0) {
                    $alerts[] = ['level' => 'warning', 'message' => 'pH hors de la plage optimale'];
                }
                break;
        }
        
        return $alerts;
    }
    
    private function getAlertSeverity($level) {
        return [
            'critical' => 5,
            'warning' => 3,
            'info' => 1
        ][$level] ?? 1;
    }
    
    private function getAlertRecommendation($alert) {
        $recommendations = [
            'temperature' => [
                'low' => 'Vérifier le système de chauffage',
                'high' => 'Activer la ventilation ou l\'ombrage'
            ],
            'humidity' => [
                'low' => 'Augmenter l\'arrosage ou l\'humidification',
                'high' => 'Améliorer la ventilation'
            ],
            'soil_moisture' => [
                'low' => 'Arroser immédiatement',
                'high' => 'Vérifier le drainage'
            ]
        ];
        
        return $recommendations[$alert['type']]['low'] ?? 'Consulter un expert';
    }
    
    private function timeAgo($timestamp) {
        $time = time() - strtotime($timestamp);
        
        if ($time < 60) return 'À l\'instant';
        if ($time < 3600) return floor($time/60) . ' min';
        if ($time < 86400) return floor($time/3600) . ' h';
        return floor($time/86400) . ' j';
    }
    
    private function getPeriodStartDate($period) {
        $intervals = [
            '1h' => '-1 hour',
            '24h' => '-24 hours',
            '7d' => '-7 days',
            '30d' => '-30 days'
        ];
        
        return date('Y-m-d H:i:s', strtotime($intervals[$period] ?? '-24 hours'));
    }
    
    private function formatTimeLabel($timeGroup, $interval) {
        switch ($interval) {
            case 'hour':
                return date('H:i', strtotime($timeGroup));
            case 'day':
                return date('d/m', strtotime($timeGroup));
            case 'minute':
                return date('H:i', strtotime($timeGroup));
            default:
                return $timeGroup;
        }
    }
    
    private function getCount($table, $condition = '1=1') {
        $stmt = $this->db->query("SELECT COUNT(*) FROM {$table} WHERE {$condition}");
        return $stmt->fetchColumn();
    }
    
    private function getCountWithJoin() {
        $stmt = $this->db->query("
            SELECT COUNT(DISTINCT c.id) 
            FROM capteurs c 
            JOIN mesures m ON c.id = m.capteur_id 
            WHERE c.is_active = 1
        ");
        return $stmt->fetchColumn();
    }
    
    private function getSystemUptime() {
        // Simuler un uptime (en production, utiliser des métriques réelles)
        return '99.9%';
    }
    
    private function exportCSV($data, $sensor) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="sensor_' . $sensor['id'] . '_data.csv"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Capteur', 'Type', 'Valeur', 'Unité', 'Date/Heure']);
        
        foreach ($data as $row) {
            fputcsv($output, [
                $sensor['name'],
                $sensor['type'],
                $row['value'],
                $sensor['unit'],
                $row['timestamp']
            ]);
        }
        
        fclose($output);
        exit;
    }
}
?>