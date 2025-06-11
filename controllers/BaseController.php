<?php
// controllers/BaseController.php
class BaseController {
    protected $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    protected function render($view, $data = []) {
        // Extraire les données pour les rendre disponibles dans la vue
        extract($data);
        
        // Démarrer la mise en mémoire tampon
        ob_start();
        
        // Inclure la vue
        require_once BASE_PATH . '/views/' . $view . '.php';
        
        // Récupérer le contenu
        $content = ob_get_clean();
        
        // Inclure le layout principal
        require_once BASE_PATH . '/views/layout.php';
    }
    
    protected function renderPartial($view, $data = []) {
        extract($data);
        require_once BASE_PATH . '/views/' . $view . '.php';
    }
    
    protected function redirect($url) {
        header('Location: ' . BASE_URL . $url);
        exit;
    }
    
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    protected function isAdmin() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
    
    protected function requireLogin() {
        if (!$this->isLoggedIn()) {
            $this->redirect('?controller=auth&action=login');
        }
    }
    
    protected function requireAdmin() {
        if (!$this->isAdmin()) {
            $this->redirect('?controller=home&action=index');
        }
    }
    
    protected function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    protected function getUserTeamId() {
        return $_SESSION['team_id'] ?? null;
    }
}
?>