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
    
    public function sensors() {
        $this->requireLogin();
        
        try {
            $sensors = $this->getAllSensorsWithData();
            $this->jsonResponse([
                'success' => true,
                'data' => $sensors,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Erreur lors de la récupération des données'
            ], 500);
        }
    }
    
    public function sensorData() {
        $this->requireLogin();
        
        $sensorId = (int)($_GET['sensor_id'] ?? 0);
        $period = $_GET['period'] ?? '24h';
        $format = $_GET['format'] ?? 'json';
        
        if (!$sensorId) {
            $this->jsonResponse(['error' => 'ID capteur requis'], 400);
        }
        
        try {
            // Vérifier que le capteur existe et que l'utilisateur a accès
            $sensor = $this->sensorModel->getSensorWithLatestData($sensorId);
            if (!$sensor) {
                $this->jsonResponse(['error' => 'Capteur non trouvé'], 404);
            }
            
            // Récupérer les données selon la période
            $data = $this->sensorModel->getSensorData($sensorId, 100, $this->getPeriodStartDate($period));
            $stats = $this->sensorModel->getStatistics($sensorId, $period);
            
            if ($format === 'csv') {
                $this->exportCSV($data, $sensor);
            } else {
                $this->jsonResponse([
                    'success' => true,
                    'sensor' => $sensor,
                    'data' => $data,
                    'statistics' => $stats,
                    'period' => $period
                ]);
            }
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Erreur lors de la récupération des données'
            ], 500);
        }
    }
    
    public function addSensorData() {
        // Endpoint pour recevoir des données depuis les microcontrôleurs
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Méthode POST requise'], 405);
        }
        
        // Authentification basique pour les microcontrôleurs
        $apiKey = $_SERVER['HTTP_API_KEY'] ?? $_POST['api_key'] ?? '';
        if (!$this->validateApiKey($apiKey)) {
            $this->jsonResponse(['error' => 'Clé API invalide'], 401);
        }
        
        $sensorId = (int)($_POST['sensor_id'] ?? 0);
        $value = (float)($_POST['value'] ?? 0);
        $timestamp = $_POST['timestamp'] ?? null;
        
        if (!$sensorId || $value === 0) {
            $this->jsonResponse(['error' => 'Données invalides'], 400);
        }
        
        try {
            // Si un timestamp est fourni, l'utiliser (pour les données en retard)
            if ($timestamp) {
                $stmt = $this->db->prepare("
                    INSERT INTO sensor_data (sensor_id, value, timestamp) 
                    VALUES (?, ?, ?)
                ");
                $success = $stmt->execute([$sensorId, $value, $timestamp]);
            } else {
                $success = $this->sensorModel->addSensorData($sensorId, $value);
            }
            
            if ($success) {
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Données ajoutées avec succès'
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
                'error' => 'Erreur base de données'
            ], 500);
        }
    }
    
    public function simulateData() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Méthode POST requise'], 405);
        }
        
        $sensorId = (int)($_POST['sensor_id'] ?? 0);
        
        if (!$sensorId) {
            $this->jsonResponse(['error' => 'ID capteur requis'], 400);
        }
        
        try {
            $success = $this->sensorModel->simulateData($sensorId);
            
            if ($success) {
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Données simulées ajoutées'
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'Erreur lors de la simulation'
                ], 500);
            }
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Erreur lors de la simulation'
            ], 500);
        }
    }
    
    public function alerts() {
        $this->requireLogin();
        
        try {
            $teamId = $this->isAdmin() ? null : $this->getUserTeamId();
            $alerts = $this->sensorModel->getAlerts($teamId);
            
            $this->jsonResponse([
                'success' => true,
                'alerts' => $alerts,
                'count' => count($alerts)
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Erreur lors de la récupération des alertes'
            ], 500);
        }
    }
    
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
            
            // Formater les données pour les graphiques
            $chartData = [
                'labels' => [],
                'datasets' => [
                    [
                        'label' => 'Moyenne',
                        'data' => [],
                        'borderColor' => 'rgb(45, 90, 39)',
                        'backgroundColor' => 'rgba(45, 90, 39, 0.1)',
                        'tension' => 0.4
                    ]
                ]
            ];
            
            foreach ($data as $point) {
                $chartData['labels'][] = $this->formatTimeLabel($point['time_group'], $interval);
                $chartData['datasets'][0]['data'][] = round($point['avg_value'], 2);
            }
            
            $this->jsonResponse([
                'success' => true,
                'chartData' => $chartData,
                'period' => $period,
                'interval' => $interval
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Erreur lors de la génération du graphique'
            ], 500);
        }
    }
    
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
        
        // Garder seulement la dernière valeur pour chaque capteur
        $sensors = [];
        foreach ($results as $row) {
            if ($row['rn'] == 1 || !isset($sensors[$row['id']])) {
                unset($row['rn']); // Supprimer la colonne technique
                $sensors[$row['id']] = $row;
            }
        }
        
        return array_values($sensors);
    }
    
    private function validateApiKey($apiKey) {
        // Clé API simple pour les microcontrôleurs
        // En production, utiliser un système plus sécurisé
        $validKeys = [
            'greenhouse_team_1',
            'greenhouse_team_2',
            'greenhouse_team_3',
            'greenhouse_team_4',
            'greenhouse_team_5'
        ];
        
        return in_array($apiKey, $validKeys);
    }
    
    private function getPeriodStartDate($period) {
        $intervals = [
            '1h' => '-1 hour',
            '24h' => '-24 hours',
            '7d' => '-7 days',
            '30d' => '-30 days'
        ];
        
        $interval = $intervals[$period] ?? '-24 hours';
        return date('Y-m-d H:i:s', strtotime($interval));
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
    
    private function exportCSV($data, $sensor) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="sensor_' . $sensor['id'] . '_data.csv"');
        
        $output = fopen('php://output', 'w');
        
        // En-têtes CSV
        fputcsv($output, [
            'Capteur',
            'Type',
            'Valeur',
            'Unité',
            'Date/Heure'
        ]);
        
        // Données
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