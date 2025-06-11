<?php
// controllers/ActuatorController.php
require_once BASE_PATH . '/controllers/BaseController.php';

class ActuatorController extends BaseController {
    
    public function index() {
        // RESTRICTION : Seuls les admins peuvent accéder à cette page
        $this->requireAdmin();
        
        $actuators = $this->getActuators();
        
        $this->render('actuators/index', [
            'actuators' => $actuators,
            'isAdmin' => $this->isAdmin()
        ]);
    }
    
    public function toggle() {
        // RESTRICTION : Seuls les admins peuvent actionner
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Méthode non autorisée'], 405);
        }
        
        $actuatorId = (int)($_POST['actuator_id'] ?? 0);
        $action = $_POST['action'] ?? '';
        
        if (!$actuatorId || !in_array($action, ['ON', 'OFF'])) {
            $this->jsonResponse(['error' => 'Paramètres invalides'], 400);
        }
        
        // Vérifier que l'actionneur existe
        $actuator = $this->getActuatorById($actuatorId);
        if (!$actuator) {
            $this->jsonResponse(['error' => 'Actionneur non trouvé'], 404);
        }
        
        // Exécuter l'action
        $success = $this->executeAction($actuatorId, $action);
        
        if ($success) {
            // Simuler l'envoi de commande au microcontrôleur
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
        // RESTRICTION : Seuls les admins peuvent gérer
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleManagement();
        }
        
        $actuators = $this->getAllActuators();
        $teams = $this->getTeams();
        
        $this->render('actuators/manage', [
            'actuators' => $actuators,
            'teams' => $teams
        ]);
    }
    
    private function getActuators() {
        // Les admins voient tous les actionneurs
        $sql = "
            SELECT a.id, a.nom as name, a.type, 
                   COALESCE(a.team_id, 1) as team_id,
                   COALESCE(a.is_active, 1) as is_active,
                   COALESCE(a.current_state, 0) as current_state,
                   t.name as team_name 
            FROM actionneurs a
            LEFT JOIN teams t ON a.team_id = t.id
            WHERE COALESCE(a.is_active, 1) = 1
            ORDER BY t.name, a.nom
        ";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    private function getAllActuators() {
        $stmt = $this->db->query("
            SELECT a.id, a.nom as name, a.type, 
                   COALESCE(a.team_id, 1) as team_id,
                   COALESCE(a.is_active, 1) as is_active,
                   COALESCE(a.current_state, 0) as current_state,
                   t.name as team_name 
            FROM actionneurs a
            LEFT JOIN teams t ON a.team_id = t.id
            ORDER BY t.name, a.nom
        ");
        return $stmt->fetchAll();
    }
    
    private function getActuatorById($id) {
        $stmt = $this->db->prepare("
            SELECT id, nom as name, type, 
                   COALESCE(team_id, 1) as team_id,
                   COALESCE(is_active, 1) as is_active,
                   COALESCE(current_state, 0) as current_state
            FROM actionneurs WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    private function executeAction($actuatorId, $action) {
        try {
            $this->db->beginTransaction();
            
            // Mettre à jour l'état de l'actionneur
            $newState = $action === 'ON' ? 1 : 0;
            $stmt = $this->db->prepare("
                UPDATE actionneurs 
                SET current_state = ? 
                WHERE id = ?
            ");
            $stmt->execute([$newState, $actuatorId]);
            
            // Enregistrer dans etats_actionneurs (table existante)
            $stmt = $this->db->prepare("
                INSERT INTO etats_actionneurs (actionneur_id, etat) 
                VALUES (?, ?)
            ");
            $stmt->execute([$actuatorId, $newState]);
            
            // Enregistrer l'action dans les logs
            $stmt = $this->db->prepare("
                INSERT INTO actuator_logs (actionneur_id, action, user_id) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$actuatorId, $action, $_SESSION['user_id']]);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Erreur executeAction: " . $e->getMessage());
            return false;
        }
    }
    
    private function sendToMicrocontroller($actuator, $action) {
        // Simulation de l'envoi de commande au microcontrôleur
        $command = [
            'actuator_id' => $actuator['id'],
            'type' => $actuator['type'],
            'action' => $action,
            'timestamp' => time(),
            'user' => $_SESSION['username']
        ];
        
        // Pour le développement, on peut logger la commande
        error_log("Commande envoyée au microcontrôleur: " . json_encode($command));
        
        // Ici, vous pourrez ajouter la vraie communication série avec TIVA
        /*
        $serialPort = '/dev/ttyUSB0'; // Port série
        $fp = fopen($serialPort, 'w');
        if ($fp) {
            fwrite($fp, json_encode($command) . "\n");
            fclose($fp);
        }
        */
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
        $teamId = (int)($_POST['team_id'] ?? 0);
        
        if ($name && $type && $teamId) {
            $stmt = $this->db->prepare("
                INSERT INTO actionneurs (nom, type, team_id) 
                VALUES (?, ?, ?)
            ");
            $success = $stmt->execute([$name, $type, $teamId]);
            
            if ($success) {
                $_SESSION['success_message'] = 'Actionneur ajouté avec succès';
            } else {
                $_SESSION['error_message'] = 'Erreur lors de l\'ajout de l\'actionneur';
            }
        }
    }
    
    private function editActuator() {
        $id = (int)($_POST['actuator_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        
        if ($id && $name) {
            $stmt = $this->db->prepare("
                UPDATE actionneurs 
                SET nom = ?, is_active = ? 
                WHERE id = ?
            ");
            $success = $stmt->execute([$name, $isActive, $id]);
            
            if ($success) {
                $_SESSION['success_message'] = 'Actionneur modifié avec succès';
            } else {
                $_SESSION['error_message'] = 'Erreur lors de la modification de l\'actionneur';
            }
        }
    }
    
    private function deleteActuator() {
        $id = (int)($_POST['actuator_id'] ?? 0);
        if ($id) {
            try {
                $this->db->beginTransaction();
                
                // Supprimer les états et logs associés
                $stmt = $this->db->prepare("DELETE FROM etats_actionneurs WHERE actionneur_id = ?");
                $stmt->execute([$id]);
                
                $stmt = $this->db->prepare("DELETE FROM actuator_logs WHERE actionneur_id = ?");
                $stmt->execute([$id]);
                
                // Supprimer l'actionneur
                $stmt = $this->db->prepare("DELETE FROM actionneurs WHERE id = ?");
                $success = $stmt->execute([$id]);
                
                $this->db->commit();
                
                if ($success) {
                    $_SESSION['success_message'] = 'Actionneur supprimé avec succès';
                } else {
                    $_SESSION['error_message'] = 'Erreur lors de la suppression de l\'actionneur';
                }
            } catch (Exception $e) {
                $this->db->rollBack();
                $_SESSION['error_message'] = 'Erreur lors de la suppression de l\'actionneur';
            }
        }
    }
    
    private function getTeams() {
        $stmt = $this->db->query("SELECT id, name FROM teams ORDER BY name");
        return $stmt->fetchAll();
    }
}
?>