<?php
// controllers/BaseController.php - Version avec gestion propre des headers
class BaseController {
    protected $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    protected function render($view, $data = []) {
        // 🎯 À ce stade, les headers de sécurité ont déjà été envoyés dans index.php
        // Donc on peut maintenant afficher du HTML sans problème
        
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
        // ⚠️ ATTENTION: Dans render(), les headers sont déjà envoyés
        // Donc on utilise JavaScript comme fallback
        if (!headers_sent()) {
            // Si on peut encore envoyer des headers (cas rare dans render())
            header('Location: ' . BASE_URL . $url);
            exit;
        } else {
            // Fallback JavaScript si headers déjà envoyés
            echo "<script>window.location.href = '" . BASE_URL . $url . "';</script>";
            echo "<noscript><meta http-equiv='refresh' content='0;url=" . BASE_URL . $url . "'></noscript>";
            echo "<p>Redirection en cours... <a href='" . BASE_URL . $url . "'>Cliquez ici si ça ne marche pas</a></p>";
            exit;
        }
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
        // Pour les réponses JSON, on nettoie tout output précédent
        ob_clean();
        
        // Vérifier si les headers peuvent encore être envoyés
        if (!headers_sent()) {
            http_response_code($statusCode);
            header('Content-Type: application/json');
        }
        echo json_encode($data);
        exit;
    }
    
    protected function getUserTeamId() {
        return $_SESSION['team_id'] ?? null;
    }
    
    /**
     * Afficher une notification (helper)
     */
    protected function setMessage($message, $type = 'info') {
        $_SESSION[$type . '_message'] = $message;
    }
    
    /**
     * Fonction utilitaire pour nettoyer les données
     */
    protected function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Validation d'email
     */
    protected function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Génération d'UUID
     */
    protected function generateUUID() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
    
    /**
     * Redirection sécurisée AVANT render (pour les contrôleurs)
     * À utiliser dans les actions du contrôleur AVANT d'appeler render()
     */
    protected function redirectBeforeRender($url) {
        if (!headers_sent()) {
            header('Location: ' . BASE_URL . $url);
            exit;
        }
        // Si les headers sont déjà envoyés, fallback JavaScript
        $this->redirect($url);
    }
}
?>