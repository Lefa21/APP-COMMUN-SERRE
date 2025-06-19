<?php
// controllers/HomeController.php - Version mise à jour
require_once BASE_PATH . '/controllers/BaseController.php';
require_once BASE_PATH . '/models/Sensor.php';
require_once BASE_PATH . '/models/Actuator.php';
require_once BASE_PATH . '/models/ContactMessage.php';
require_once BASE_PATH . '/models/Weather.php';

class HomeController extends BaseController {
    
    private $sensorModel;
    private $actuatorModel;
    private $contactMessageModel;
    private $weatherModel;

    public function __construct() {
        parent::__construct();
        $this->sensorModel = new Sensor();
        $this->actuatorModel = new Actuator();
        $this->contactMessageModel = new ContactMessage();
        $this->weatherModel = new Weather();
    }

    public function index() {
        // Si l'utilisateur n'est pas connecté, afficher la landing page
        if (!$this->isLoggedIn() && !$this->isAdmin()) {
            $this->renderLandingPage();
            return;
        }
        
        // Code existant pour les utilisateurs connectés
        $sensors = $this->getSensorsWithData(); 
        $actuators = $this->actuatorModel->findAllActive();
        $weather = $this->weatherModel->getCurrentWeather('Paris');
        
        $data = [
            'sensors' => $sensors,
            'actuators' => $actuators,
            'weather' => $weather,
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
                'name' => 'Projet Green Pulse',
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
     * Gère l'affichage et la soumission du formulaire de contact.
     */
    public function contact() {
        // Initialisation des variables pour la vue
        $data = [
            'success_message' => '',
            'error_message' => '',
            'submitted_data' => []
        ];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et nettoyer les données du formulaire
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $subject = trim($_POST['subject'] ?? 'Sans objet');
            $messageText = trim($_POST['message'] ?? '');
            
            // Conserver les données soumises pour ré-afficher en cas d'erreur
            $data['submitted_data'] = $_POST;

            // Validation des données
            if (empty($name) || empty($email) || empty($messageText)) {
                $data['error_message'] = 'Les champs Nom, Email et Message sont obligatoires.';
            } elseif (!$this->validateEmail($email)) {
                $data['error_message'] = 'Votre adresse email ne semble pas valide.';
            } else {
                // Utilisation du modèle pour sauvegarder le message
                // C'est ici que l'appel à l'ancienne fonction est remplacé
                if ($this->contactMessageModel->save($name, $email, $subject, $messageText)) {
                    // Succès : préparer un message de succès et vider les données soumises
                    $this->setMessage('Votre message a été envoyé avec succès !', 'success');
                    // Rediriger pour éviter une nouvelle soumission du formulaire si l'utilisateur rafraîchit la page (Post/Redirect/Get pattern)
                    $this->redirect('?controller=home&action=contact');
                } else {
                    // Échec de la sauvegarde
                    $data['error_message'] = 'Une erreur technique est survenue. Veuillez réessayer plus tard.';
                }
            }
        }
        
        // Afficher la vue avec les données (messages d'erreur/succès)
        $this->render('home/contact', $data);
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

    public function contactSubmit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? 'Anonyme';
            $email = $_POST['email'] ?? '';
            $subject = $_POST['subject'] ?? 'Sans objet';
            $message = $_POST['message'] ?? '';

            if (!empty($email) && !empty($message)) {
                // On utilise maintenant le modèle ! C'est plus propre.
                if ($this->contactMessageModel->save($name, $email, $subject, $message)) {
                    $this->setMessage('Votre message a bien été envoyé.', 'success');
                } else {
                    $this->setMessage('Une erreur est survenue lors de l\'envoi du message.', 'error');
                }
            } else {
                 $this->setMessage('Veuillez remplir tous les champs obligatoires.', 'error');
            }
            $this->redirect('?controller=home&action=contactPage'); // Redirige vers la page de contact
        }
    }
}
?>