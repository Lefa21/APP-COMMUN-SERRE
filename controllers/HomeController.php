<?php
// controllers/HomeController.php - Version mise à jour
require_once BASE_PATH . '/controllers/BaseController.php';
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
        // Si l'utilisateur n'est pas connecté, afficher la landing page
        if (!$this->isLoggedIn()) {
            $this->renderLandingPage();
            return;
        }
        
        // Code existant pour les utilisateurs connectés
        $sensors = $this->getSensorsWithData(); 
        $actuators = $this->actuatorModel->findAllActive();
        $recentActivity = $this->actuatorModel->getRecentActivity(10);
        
        $data = [
            'sensors' => $sensors,
            'actuators' => $actuators,
            'recentActivity' => $recentActivity,
            'isAdmin' => $this->isAdmin()
        ];
        
        $this->render('home/index', $data);
    }

    /**
     * Affiche la page d'accueil pour les visiteurs non connectés
     */
    private function renderLandingPage() {
        // Statistiques publiques (anonymisées)
        $stats = [
            'total_sensors' => $this->sensorModel->countActive(),
            'total_teams' => 5, // Nombre d'équipes configurées
            'water_savings' => 95, // Pourcentage d'économie d'eau
            'yield_increase' => 40, // Augmentation de rendement
            'monitoring_uptime' => 99.9 // Temps de fonctionnement
        ];
        
        $testimonials = [
            [
                'name' => 'Équipe Alpha',
                'message' => 'Nos tomates ont un rendement 35% supérieur depuis l\'utilisation du système.',
                'rating' => 5
            ],
            [
                'name' => 'Équipe Beta', 
                'message' => 'L\'automatisation nous fait économiser 3h de travail par jour.',
                'rating' => 5
            ]
        ];
        
        $this->render('home/landing', [
            'stats' => $stats,
            'testimonials' => $testimonials
        ]);
    }

    /**
     * Page À propos (accessible publiquement)
     */
    public function about() {
        $this->render('home/about', [
            'project_info' => [
                'name' => 'Projet Serres Connectées',
                'version' => '1.0',
                'team_size' => 30,
                'technologies' => ['PHP', 'MySQL', 'Bootstrap', 'IoT', 'Arduino'],
                'eco_features' => [
                    'Optimisation énergétique',
                    'Réduction consommation d\'eau',
                    'Agriculture sans pesticides',
                    'Interface éco-conçue'
                ]
            ]
        ]);
    }

    /**
     * Page de contact (accessible publiquement)
     */
    public function contact() {
        $message = '';
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $subject = trim($_POST['subject'] ?? '');
            $messageText = trim($_POST['message'] ?? '');
            
            if (empty($name) || empty($email) || empty($messageText)) {
                $error = 'Tous les champs sont obligatoires.';
            } elseif (!$this->validateEmail($email)) {
                $error = 'Email invalide.';
            } else {
                // Ici vous pourriez envoyer un email ou sauvegarder en base
                // Pour la démo, on simule l'envoi
                $this->logContactMessage($name, $email, $subject, $messageText);
                $message = 'Votre message a été envoyé avec succès. Nous vous répondrons sous 24h.';
            }
        }
        
        $this->render('home/contact', [
            'message' => $message,
            'error' => $error
        ]);
    }

    /**
     * Fonction existante pour les capteurs (privée)
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

    /**
     * Log des messages de contact (simple)
     */
    private function logContactMessage($name, $email, $subject, $message) {
        try {
            // Créer une table contact_messages si elle n'existe pas
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS contact_messages (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    subject VARCHAR(255),
                    message TEXT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
            
            $stmt = $this->db->prepare("
                INSERT INTO contact_messages (name, email, subject, message) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$name, $email, $subject, $message]);
            
            return true;
        } catch (Exception $e) {
            error_log("Erreur sauvegarde contact: " . $e->getMessage());
            return false;
        }
    }
}
?>