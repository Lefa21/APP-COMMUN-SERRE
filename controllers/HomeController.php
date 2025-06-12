<?php
// controllers/HomeController.php
require_once BASE_PATH . '/controllers/BaseController.php';
// On charge les modèles nécessaires
require_once BASE_PATH . '/models/Sensor.php';
require_once BASE_PATH . '/models/Actuator.php';

class HomeController extends BaseController {
    
    private $sensorModel;
    private $actuatorModel;

    public function __construct() {
        parent::__construct();
        $this->sensorModel = new Sensor();
        $this->actuatorModel = new Actuator();
    }

    public function index() {
        if (!$this->isLoggedIn()) {
            $this->redirect('?controller=auth&action=login');
        }
        
        // --- MODIFICATION APPLIQUÉE ICI ---
        // On utilise maintenant les fonctions exactes du modèle Sensor.php
        $sensors = $this->getSensorsWithData(); 
        
        $actuators = $this->actuatorModel->findAllActive(); // En supposant que ActuatorModel a cette méthode
        $recentActivity = $this->actuatorModel->getRecentActivity(10); // De même
        
        $data = [
            'sensors' => $sensors,
            'actuators' => $actuators,
            'recentActivity' => $recentActivity,
            'isAdmin' => $this->isAdmin()
        ];
        
        $this->render('home/index', $data);
    }

    /**
     * Nouvelle fonction privée pour construire les données des capteurs
     * en utilisant les méthodes fournies dans le modèle.
     */
    private function getSensorsWithData() {
        // 1. Récupérer tous les capteurs de base
        $sensors = $this->sensorModel->getAllSensors();
        
        // 2. Boucler pour ajouter la dernière donnée à chaque capteur
        foreach ($sensors as &$sensor) { // Le '&' permet de modifier le tableau directement
            $latestData = $this->sensorModel->getSensorData($sensor['id'], 1);
            if (!empty($latestData)) {
                $sensor['value'] = $latestData[0]['value'];
                $sensor['timestamp'] = $latestData[0]['timestamp'];
            } else {
                $sensor['value'] = null;
                $sensor['timestamp'] = null;
            }
        }
        unset($sensor); // Bonne pratique pour supprimer la référence après la boucle

        return $sensors;
    }
}
