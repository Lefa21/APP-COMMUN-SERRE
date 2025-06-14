<?php
// controllers/BaseController.php - Version avec gestion propre des headers
class BaseController {

        /**
     * Le constructeur est maintenant vide.
     * Chaque contrôleur enfant (UserController, SensorController, etc.)
     * initialisera ses propres connexions à la base de données via le
     * Database::getConnection('local') ou Database::getConnection('remote').
     */
    public function __construct() {
        // La propriété $db a été supprimée, car elle n'est plus universelle.
    }
    
    /**
     * Affiche une vue en l'injectant dans le layout principal.
     * @param string $view Le chemin de la vue depuis le dossier /views
     * @param array $data Les données à rendre accessibles à la vue
     */
    protected function render($view, $data = []) {
        // Extraire les données pour les rendre disponibles dans la vue
        extract($data);
        
        // Démarrer la mise en mémoire tampon pour capturer le contenu de la vue
        ob_start();
        
        require_once BASE_PATH . '/views/' . $view . '.php';
        
        // Récupérer le contenu de la vue
        $content = ob_get_clean();
        
        // Inclure le layout principal qui affichera le $content
        require_once BASE_PATH . '/views/layout.php';
    }
    
    
    protected function renderPartial($view, $data = []) {
        extract($data);
        require_once BASE_PATH . '/views/' . $view . '.php';
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

    
    /**
     * Redirige l'utilisateur vers une autre URL de l'application.
     * Gère le cas où les en-têtes HTTP ont déjà été envoyés.
     * @param string $url L'URL de destination (ex: ?controller=home)
     */
    protected function redirect($url) {
        if (!headers_sent()) {
            header('Location: ' . BASE_URL . $url);
            exit;
        } else {
            // Fallback JavaScript si les en-têtes sont déjà envoyés
            echo "<script>window.location.href = '" . BASE_URL . $url . "';</script>";
            echo "<noscript><meta http-equiv='refresh' content='0;url=" . BASE_URL . $url . "'></noscript>";
            exit;
        }
    }
    
    /**
     * Répond avec des données au format JSON.
     * @param mixed $data Les données à encoder en JSON.
     * @param int $statusCode Le code de statut HTTP.
     */
    protected function jsonResponse($data, $statusCode = 200) {
        // Assure qu'aucun autre contenu n'est envoyé avant le JSON
        if (ob_get_level()) {
            ob_clean();
        }
        
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    // --- Fonctions utilitaires de session et de permissions ---

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
            $_SESSION['error_message'] = "Accès réservé aux administrateurs.";
            $this->redirect('?controller=home');
        }
    }
    
    /**
     * Définit un message de notification en session.
     * @param string $message
     * @param string $type ('success', 'error', 'info')
     */
    protected function setMessage($message, $type = 'info') {
        $_SESSION[$type . '_message'] = $message;
    }

    // --- Fonctions utilitaires générales ---

    /**
     * Valide une adresse email.
     * @param string $email
     * @return bool
     */
    protected function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
?>