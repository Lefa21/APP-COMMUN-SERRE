<?php
// controllers/HomeController.php
require_once BASE_PATH . '/controllers/BaseController.php';

class HomeController extends BaseController {
    
    public function index() {
        if (!$this->isLoggedIn()) {
            $this->redirect('?controller=auth&action=login');
        }
        
        // Récupérer les données des capteurs de toutes les équipes
        $sensors = $this->getAllSensorsData();
        $actuators = $this->getAllActuators();
        $recentActivity = $this->getRecentActivity();
        
        $data = [
            'sensors' => $sensors,
            'actuators' => $actuators,
            'recentActivity' => $recentActivity,
            'isAdmin' => $this->isAdmin()
        ];
        
        $this->render('home/index', $data);
    }
    
    private function getAllSensorsData() {
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
            WHERE COALESCE(a.is_active, 1) = 1
            ORDER BY t.name, a.nom
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    private function getRecentActivity() {
        $stmt = $this->db->prepare("
            SELECT 
                al.action, al.timestamp,
                a.nom as actuator_name,
                u.username,
                t.name as team_name
            FROM actuator_logs al
            JOIN actionneurs a ON al.actionneur_id = a.id
            JOIN user u ON al.user_id = u.id_user
            LEFT JOIN teams t ON a.team_id = t.id
            ORDER BY al.timestamp DESC
            LIMIT 10
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }RecentActivity() {
        $stmt = $this->db->prepare("
            SELECT 
                al.action, al.timestamp,
                a.name as actuator_name,
                u.username,
                t.name as team_name
            FROM actuator_logs al
            JOIN actuators a ON al.actuator_id = a.id
            JOIN users u ON al.user_id = u.id
            JOIN teams t ON a.team_id = t.id
            ORDER BY al.timestamp DESC
            LIMIT 10
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>