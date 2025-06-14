<?php
// controllers/ActuatorController.php
require_once BASE_PATH . '/controllers/BaseController.php';
require_once BASE_PATH . '/models/Actuator.php';

class ActuatorController extends BaseController {
    
    private $actuatorModel;

    public function __construct() {
        parent::__construct();
        $this->actuatorModel = new Actuator();
    }

    public function index() {
        // La vue principale pour les utilisateurs
        $this->requireLogin();

        $actuators = $this->actuatorModel->findAllActive();
        
        $this->render('actuators/index', [
            'actuators' => $actuators,
            'isAdmin' => $this->isAdmin()
        ]);
    }
    
    public function toggle() {
        // API pour changer l'état d'un actionneur
        $this->requireLogin(); // Ou une clé API
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Méthode non autorisée'], 405);
        }
        
        $actuatorId = (int)($_POST['actuator_id'] ?? 0);
        $action = $_POST['action'] ?? '';
        
        if (!$actuatorId || !in_array($action, ['ON', 'OFF'])) {
            $this->jsonResponse(['error' => 'Paramètres invalides'], 400);
        }
        
        $actuator = $this->actuatorModel->findById($actuatorId);
        if (!$actuator) {
            $this->jsonResponse(['error' => 'Actionneur non trouvé'], 404);
        }

        // Vérifier les permissions (un admin ou un membre de l'équipe peut agir)
        if (!$this->isAdmin()) {
            $this->jsonResponse(['error' => 'Permission refusée'], 403);
        }
        
        $success = $this->actuatorModel->toggleState($actuatorId, $action, $_SESSION['user_id']);
        
        if ($success) {
            $this->sendToMicrocontroller($actuator, $action);
            $this->jsonResponse([
                'success' => true,
                'message' => "Actionneur {$action} avec succès",
                'newState' => $action === 'ON'
            ]);
        } else {
            $this->jsonResponse(['error' => 'Erreur lors de l\'exécution'], 500);
        }
    }
    
    public function manage() {
        // Page d'administration
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleManagement();
        }
        
        $actuators = $this->actuatorModel->findAll();
        
        $this->render('actuators/manage', [
            'actuators' => $actuators,
        ]);
    }

    private function handleManagement() {
        $action = $_POST['management_action'] ?? '';
        
        switch ($action) {
            case 'add':
                $this->addActuator();
                break;
            case 'edit':
                $this->editActuator();
                break;
            case 'delete':
                $this->deleteActuator();
                break;
        }
    }
    
    private function addActuator() {
        $name = trim($_POST['name'] ?? '');
        $type = $_POST['type'] ?? '';
        
        if ($name && $type) {
            if ($this->actuatorModel->create($name, $type)) {
                $this->setMessage('Actionneur ajouté avec succès', 'success');
            } else {
                $this->setMessage('Erreur lors de l\'ajout', 'error');
            }
        }
    }
    
    private function editActuator() {
        $id = (int)($_POST['actuator_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        
        if ($id && $name) {
            if ($this->actuatorModel->update($id, $name, $isActive)) {
                $this->setMessage('Actionneur modifié avec succès', 'success');
            } else {
                $this->setMessage('Erreur lors de la modification', 'error');
            }
        }
    }
    
    private function deleteActuator() {
        $id = (int)($_POST['actuator_id'] ?? 0);
        if ($id) {
            if ($this->actuatorModel->delete($id)) {
                $this->setMessage('Actionneur supprimé avec succès', 'success');
            } else {
                $this->setMessage('Erreur lors de la suppression', 'error');
            }
        }
    }

    private function sendToMicrocontroller($actuator, $action) {
        // Simulation de l'envoi de commande (inchangée)
        $command = [
            'actuator_id' => $actuator['id'],
            'type' => $actuator['type'],
            'action' => $action,
            'timestamp' => time()
        ];
        error_log("Commande envoyée au microcontrôleur: " . json_encode($command));
    }
}