<?php
// controllers/ApiController.php
require_once BASE_PATH . '/controllers/BaseController.php';
require_once BASE_PATH . '/models/Sensor.php';
require_once BASE_PATH . '/models/Actuator.php';
require_once BASE_PATH . '/models/User.php';

class ApiController extends BaseController {
    
    private $sensorModel;
    private $actuatorModel;
    private $userModel;
    
    public function __construct() {
        parent::__construct();
        $this->sensorModel = new Sensor();
        $this->actuatorModel = new Actuator();
        $this->userModel = new User();
    }
    
    /**
     * Endpoint pour récupérer tous les capteurs avec leurs dernières données.
     */
    public function sensors() {
        $this->requireLogin();
        
        try {
            // Logique déplacée dans le modèle Sensor
            $sensors = $this->sensorModel->getAllSensorsWithLastReading();
            $this->jsonResponse([
                'success' => true,
                'data' => $sensors,
                'timestamp' => date('Y-m-d H:i:s'),
                'count' => count($sensors)
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => 'Erreur de récupération des capteurs'], 500);
        }
    }
    
    /**
     * Endpoint pour récupérer tous les actionneurs avec leur état.
     */
    public function actuators() {
        $this->requireLogin();
        
        try {
            // Logique déplacée dans le modèle Actuator
            $actuators = $this->actuatorModel->findAllActive();
            $this->jsonResponse([
                'success' => true,
                'data' => $actuators,
                'timestamp' => date('Y-m-d H:i:s'),
                'active_count' => count(array_filter($actuators, fn($a) => $a['current_state'])),
                'total_count' => count($actuators)
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => 'Erreur de récupération des actionneurs'], 500);
        }
    }

    /**
     * Endpoint pour ajouter des données de capteur (depuis microcontrôleur).
     */
    public function addSensorData() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Méthode POST requise'], 405);
        }
        
        $apiKey = $_SERVER['HTTP_API_KEY'] ?? $_POST['api_key'] ?? '';
        if (!$this->validateApiKey($apiKey)) {
            $this->jsonResponse(['error' => 'Clé API invalide'], 401);
        }
        
        $sensorId = (int)($_POST['sensor_id'] ?? 0);
        $value = (float)($_POST['value'] ?? 0.0);
        
        if (!$sensorId) {
            $this->jsonResponse(['error' => 'sensor_id requis'], 400);
        }
        
        try {
            if ($this->sensorModel->addSensorData($sensorId, $value)) {
                $this->jsonResponse(['success' => true, 'message' => 'Données ajoutées']);
            } else {
                $this->jsonResponse(['success' => false, 'error' => 'Erreur lors de l\'ajout'], 500);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => 'Erreur base de données'], 500);
        }
    }

    /**
     * Endpoint pour récupérer les données formatées pour un graphique.
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
            
            // Formatage des données pour Chart.js (la logique de présentation reste ici)
            $chartData = $this->formatDataForChart($data, $interval);
            
            $this->jsonResponse([
                'success' => true,
                'chartData' => $chartData,
                'period' => $period,
                'interval' => $interval
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => 'Erreur lors de la génération du graphique'], 500);
        }
    }
    
    /**
     * Endpoint de santé du système.
     */
     public function health() {
        $health = [
            'status' => 'healthy',
            'timestamp' => date('Y-m-d H:i:s'),
            'databases' => [],
            'services' => []
        ];

        // 1. Vérifier la connexion à la base de données LOCALE
        try {
            $db_local = Database::getConnection('local');
            $db_local->query("SELECT 1");
            $health['databases']['local'] = 'connected';
        } catch (Exception $e) {
            $health['databases']['local'] = 'disconnected';
            $health['status'] = 'error'; // Le statut global passe à 'error'
        }

        // 2. Vérifier la connexion à la base de données DISTANTE
        try {
            $db_remote = Database::getConnection('remote');
            $db_remote->query("SELECT 1");
            $health['databases']['remote'] = 'connected';
        } catch (Exception $e) {
            $health['databases']['remote'] = 'disconnected';
            $health['status'] = 'error'; // Le statut global passe à 'error'
        }

        // 3. Si les BDs sont OK, vérifier la logique applicative
        if ($health['status'] === 'healthy') {
            try {
                $activeSensors = $this->sensorModel->countActive();
                $health['services']['sensors'] = ['status' => 'operational', 'active' => $activeSensors];
                
                if ($activeSensors === 0) {
                    $health['status'] = 'warning'; // Le statut global passe à 'warning'
                    $health['message'] = 'Aucun capteur actif détecté.';
                }
            } catch (Exception $e) {
                $health['services']['sensors'] = ['status' => 'degraded', 'error' => 'Impossible de compter les capteurs'];
                $health['status'] = 'warning';
            }
        }
        
        // Déterminer le code de statut HTTP final
        $statusCode = ($health['status'] === 'error') ? 503 : 200; // 503 Service Unavailable

        $this->jsonResponse($health, $statusCode);
    }

    // --- Méthodes privées du contrôleur ---

    private function validateApiKey($apiKey) {
        // La logique de validation reste ici car elle est spécifique à l'API.
        $validKeys = [
            'serre_team_1_key_2025', 'serre_team_2_key_2025',
            'serre_team_3_key_2025', 'serre_team_4_key_2025',
            'serre_team_5_key_2025', 'serre_admin_master_key'
        ];
        return in_array($apiKey, $validKeys);
    }

    private function formatDataForChart($data, $interval) {
        $chartData = [
            'labels' => [],
            'datasets' => [
                ['label' => 'Moyenne', 'data' => [], /* ... styles ... */],
                ['label' => 'Min/Max', 'data' => [], /* ... styles ... */]
            ]
        ];
        
        foreach ($data as $point) {
            $chartData['labels'][] = date('H:i', strtotime($point['time_group']));
            $chartData['datasets'][0]['data'][] = round($point['avg_value'], 2);
            $chartData['datasets'][1]['data'][] = [
                round($point['min_value'], 2),
                round($point['max_value'], 2)
            ];
        }
        return $chartData;
    }
}