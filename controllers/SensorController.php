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
        
        // --- MODIFICATION APPLIQUÉE ICI ---
        $sensors = $this->getSensorsWithData();
        
        // La fonction getAlerts existe dans votre modèle, donc c'est correct.
        $alerts = $this->sensorModel->getAlerts(null);
        
        $this->render('sensors/index', [
            'sensors' => $sensors,
            'alerts' => $alerts,
            'isAdmin' => $this->isAdmin()
        ]);
    }

    /**
     * Nouvelle fonction privée (similaire à HomeController)
     */
    private function getSensorsWithData() {
        $sensors = $this->sensorModel->getAllSensors();
        
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
        unset($sensor);

        return $sensors;
    }
    
    public function details() {
        $this->requireLogin();
        
        $sensorId = (int)($_GET['id'] ?? 0);
        $period = $_GET['period'] ?? '24h';
        
        if (!$sensorId) $this->redirect('?controller=sensor');
        
        // Ces fonctions existent toutes dans votre modèle, donc c'est correct.
        $sensor = $this->sensorModel->getSensorWithLatestData($sensorId);
        if (!$sensor) $this->redirect('?controller=sensor');
        
        $data = $this->sensorModel->getSensorData($sensorId, 100, $this->getPeriodStartDate($period));
        $stats = $this->sensorModel->getStatistics($sensorId, $period);
        $chartData = $this->sensorModel->getAggregatedData($sensorId, 'hour', $period);
        
        $this->render('sensors/details', [
            'sensor' => $sensor, 'data' => $data, 'stats' => $stats,
            'chartData' => $chartData, 'period' => $period, 'isAdmin' => $this->isAdmin()
        ]);
    }
    
    public function manage() {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleManagement();
        }
        
        $sensors = $this->sensorModel->getAllSensors(); // Correct, la fonction existe
        
        $this->render('sensors/manage', ['sensors' => $sensors,]);
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
        }
    }

    
    private function addSensor() {
        // ... (logique de récupération des POST)
        $name = trim($_POST['name'] ?? '');
        $type = $_POST['type'] ?? '';
        $unit = trim($_POST['unit'] ?? '');

        // La fonction addSensor existe, c'est correct
        if ($this->sensorModel->addSensor($name, $type, $unit)) {
            $this->setMessage('Capteur ajouté avec succès', 'success');
        } else {
            $this->setMessage('Erreur', 'error');
        }
    }
    
    private function editSensor() {
        // ... (logique de récupération des POST)
        $id = (int)($_POST['sensor_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $type = $_POST['type'] ?? '';
        $unit = trim($_POST['unit'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        // La fonction updateSensor existe, c'est correct
        if ($this->sensorModel->updateSensor($id, $name, $type, $unit, $isActive)) {
            $this->setMessage('Capteur modifié avec succès', 'success');
        } else {
            $this->setMessage('Erreur', 'error');
        }
    }
    
    private function deleteSensor() {
        $id = (int)($_POST['sensor_id'] ?? 0);
        // La fonction deleteSensor existe, c'est correct
        if ($this->sensorModel->deleteSensor($id)) {
            $this->setMessage('Capteur supprimé avec succès', 'success');
        } else {
            $this->setMessage('Erreur', 'error');
        }
    }
    
    // Le reste des fonctions (simulate, export, etc.) est correct car elles
    // utilisaient déjà les bonnes méthodes du modèle.
    // ...
    private function getPeriodStartDate($period) {
        $intervals = [
            '1h' => '-1 hour', '24h' => '-24 hours',
            '7d' => '-7 days', '30d' => '-30 days'
        ];
        return date('Y-m-d H:i:s', strtotime($intervals[$period] ?? '-24 hours'));
    }
}
