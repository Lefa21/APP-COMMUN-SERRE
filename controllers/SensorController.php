<?php
// controllers/SensorController.php
require_once BASE_PATH . '/controllers/BaseController.php';
require_once BASE_PATH . '/models/Sensor.php';

class SensorController extends BaseController {
    
    private $sensorModel;
    
    public function __construct() {
        parent::__construct();
        $this->sensorModel = new Sensor();
    }
    
    public function index() {
        $this->requireLogin();
        
        $sensors = $this->getSensorsWithData();
        $alerts = $this->sensorModel->getAlerts($this->isAdmin() ? null : $this->getUserTeamId());
        
        $data = [
            'sensors' => $sensors,
            'alerts' => $alerts,
            'isAdmin' => $this->isAdmin()
        ];
        
        $this->render('sensors/index', $data);
    }
    
    public function details() {
        $this->requireLogin();
        
        $sensorId = (int)($_GET['id'] ?? 0);
        $period = $_GET['period'] ?? '24h';
        
        if (!$sensorId) {
            $this->redirect('?controller=sensor');
        }
        
        $sensor = $this->sensorModel->getSensorWithLatestData($sensorId);
        if (!$sensor) {
            $this->redirect('?controller=sensor');
        }
        
        $data = $this->sensorModel->getSensorData($sensorId, 100, $this->getPeriodStartDate($period));
        $stats = $this->sensorModel->getStatistics($sensorId, $period);
        $chartData = $this->sensorModel->getAggregatedData($sensorId, 'hour', $period);
        
        $this->render('sensors/details', [
            'sensor' => $sensor,
            'data' => $data,
            'stats' => $stats,
            'chartData' => $chartData,
            'period' => $period,
            'isAdmin' => $this->isAdmin()
        ]);
    }
    
    public function manage() {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleManagement();
        }
        
        $sensors = $this->sensorModel->getAllSensors();
        $teams = $this->getTeams();
        
        $this->render('sensors/manage', [
            'sensors' => $sensors,
            'teams' => $teams
        ]);
    }
    
    public function simulate() {
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
            for ($i = 0; $i < $count; $i++) {
                if ($this->sensorModel->simulateData($sensorId)) {
                    $successCount++;
                }
                // Petite pause pour éviter les doublons de timestamp
                usleep(100000); // 0.1 seconde
            }
            
            $this->jsonResponse([
                'success' => true,
                'message' => "{$successCount} mesures simulées ajoutées",
                'generated' => $successCount
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Erreur lors de la simulation'
            ], 500);
        }
    }
    
    public function export() {
        $this->requireLogin();
        
        $sensorId = (int)($_GET['sensor_id'] ?? 0);
        $period = $_GET['period'] ?? '24h';
        $format = $_GET['format'] ?? 'csv';
        
        if (!$sensorId) {
            $this->redirect('?controller=sensor');
        }
        
        $sensor = $this->sensorModel->getSensorWithLatestData($sensorId);
        if (!$sensor) {
            $this->redirect('?controller=sensor');
        }
        
        $data = $this->sensorModel->getSensorData($sensorId, 1000, $this->getPeriodStartDate($period));
        
        if ($format === 'json') {
            $this->exportJSON($data, $sensor, $period);
        } else {
            $this->exportCSV($data, $sensor, $period);
        }
    }
    
    private function getSensorsWithData() {
        $sensors = $this->sensorModel->getAllSensors();
        
        // Ajouter les dernières données pour chaque capteur
        foreach ($sensors as &$sensor) {
            $latestData = $this->sensorModel->getSensorData($sensor['id'], 1);
            if (!empty($latestData)) {
                $sensor['value'] = $latestData[0]['value'];
                $sensor['timestamp'] = $latestData[0]['timestamp'];
            } else {
                $sensor['value'] = null;
                $sensor['timestamp'] = null;
            }
        }
        
        return $sensors;
    }
    
    private function handleManagement() {
        $action = $_POST['management_action'] ?? '';
        
        switch ($action) {
            case 'add':
                $this->addSensor();
                break;
            case 'edit':
                $this->editSensor();
                break;
            case 'delete':
                $this->deleteSensor();
                break;
            case 'simulate_batch':
                $this->simulateBatchData();
                break;
        }
    }
    
    private function addSensor() {
        $name = trim($_POST['name'] ?? '');
        $type = $_POST['type'] ?? '';
        $unit = trim($_POST['unit'] ?? '');
        $teamId = (int)($_POST['team_id'] ?? 0);
        
        if ($name && $type && $unit && $teamId) {
            $success = $this->sensorModel->addSensor($name, $type, $unit, $teamId);
            if ($success) {
                $_SESSION['success_message'] = 'Capteur ajouté avec succès';
            } else {
                $_SESSION['error_message'] = 'Erreur lors de l\'ajout du capteur';
            }
        }
    }
    
    private function editSensor() {
        $id = (int)($_POST['sensor_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $type = $_POST['type'] ?? '';
        $unit = trim($_POST['unit'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        
        if ($id && $name && $type && $unit) {
            $success = $this->sensorModel->updateSensor($id, $name, $type, $unit, $isActive);
            if ($success) {
                $_SESSION['success_message'] = 'Capteur modifié avec succès';
            } else {
                $_SESSION['error_message'] = 'Erreur lors de la modification du capteur';
            }
        }
    }
    
    private function deleteSensor() {
        $id = (int)($_POST['sensor_id'] ?? 0);
        if ($id) {
            if ($success) {
                $_SESSION['success_message'] = 'Capteur supprimé avec succès';
            } else {
                $_SESSION['error_message'] = 'Erreur lors de la suppression du capteur';
            }
        }
    }
    
    private function simulateBatchData() {
        $sensorIds = $_POST['sensor_ids'] ?? [];
        $count = (int)($_POST['batch_count'] ?? 10);
        
        $totalGenerated = 0;
        foreach ($sensorIds as $sensorId) {
            for ($i = 0; $i < $count; $i++) {
                if ($this->sensorModel->simulateData((int)$sensorId)) {
                    $totalGenerated++;
                }
                usleep(50000); // 0.05 seconde
            }
        }
        
        $_SESSION['success_message'] = "{$totalGenerated} mesures simulées générées";
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
    
    private function exportCSV($data, $sensor, $period) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="capteur_' . $sensor['id'] . '_' . $period . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // En-têtes CSV
        fputcsv($output, [
            'Capteur',
            'Type',
            'Équipe',
            'Valeur',
            'Unité',
            'Date/Heure'
        ]);
        
        // Données
        foreach ($data as $row) {
            fputcsv($output, [
                $sensor['name'],
                $sensor['type'],
                $sensor['team_name'] ?? 'Équipe 1',
                $row['value'],
                $sensor['unit'],
                $row['timestamp']
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    private function exportJSON($data, $sensor, $period) {
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="capteur_' . $sensor['id'] . '_' . $period . '.json"');
        
        $export = [
            'sensor' => $sensor,
            'period' => $period,
            'export_date' => date('Y-m-d H:i:s'),
            'data_count' => count($data),
            'data' => $data
        ];
        
        echo json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    private function getTeams() {
        $stmt = $this->db->query("SELECT id, name FROM teams ORDER BY name");
        return $stmt->fetchAll();
    }
}
?>