<?php
// index.php - Point d'entrée principal mis à jour
session_start();

// Configuration de base
define('BASE_PATH', __DIR__);
define('BASE_URL', 'http://localhost/APP-COMMUN-SERRE/');

// Autoloader simple
spl_autoload_register(function ($class) {
    $paths = [
        BASE_PATH . '/controllers/' . $class . '.php',
        BASE_PATH . '/models/' . $class . '.php',
        BASE_PATH . '/config/' . $class . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Inclure la configuration
require_once BASE_PATH . '/config/Database.php';

// Router simple avec support des nouveaux contrôleurs
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Sécurisation des paramètres
$controller = preg_replace('/[^a-zA-Z]/', '', $controller);
$action = preg_replace('/[^a-zA-Z]/', '', $action);

// Mapping des contrôleurs avec vérification des permissions
$controllerMappings = [
    'home' => 'HomeController',
    'auth' => 'AuthController',
    'sensor' => 'SensorController',
    'actuator' => 'ActuatorController',
    'api' => 'ApiController',
    'profile' => 'ProfileController',        
    'admin' => 'AdminController',           
    'weather' => 'WeatherController',       
    'dashboard' => 'DashboardController'
];

// Contrôleurs nécessitant une authentification
$requireAuth = [
    'sensor', 'actuator', 'api', 'profile', 'admin', 'weather', 'dashboard'
];

// Contrôleurs nécessitant des privilèges admin
$requireAdmin = [
    'admin'
];

try {
    // Vérifier si le contrôleur existe
    if (!isset($controllerMappings[$controller])) {
        throw new Exception("Contrôleur non trouvé");
    }
    
    $controllerClass = $controllerMappings[$controller];
    
    // Vérifications de sécurité
    if (in_array($controller, $requireAuth)) {
        if (!isset($_SESSION['user_id'])) {
            // Rediriger vers la page de connexion
            header('Location: ' . BASE_URL . '?controller=auth&action=login');
            exit;
        }
    }
    
    if (in_array($controller, $requireAdmin)) {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['error_message'] = 'Accès non autorisé. Privilèges administrateur requis.';
            header('Location: ' . BASE_URL . '?controller=home');
            exit;
        }
    }
    
    // Instancier et exécuter le contrôleur
    if (class_exists($controllerClass)) {
        $controllerInstance = new $controllerClass();
        
        if (method_exists($controllerInstance, $action)) {
            if (isset($_SESSION['user_id']) && in_array($controller, ['actuator', 'admin'])) {
                logUserActivity($_SESSION['user_id'], $controller, $action);
            }
            
            $controllerInstance->$action();
        } else {
            throw new Exception("Action non trouvée");
        }
    } else {
        throw new Exception("Classe contrôleur non trouvée");
    }
} catch (Exception $e) {
    // Log de l'erreur
    error_log("Erreur routing: " . $e->getMessage() . " - Controller: $controller, Action: $action");
    
    // Page 404 personnalisée
    http_response_code(404);
    
    // Si c'est une erreur de permission, afficher un message spécifique
    if (strpos($e->getMessage(), 'non autorisé') !== false) {
        require_once BASE_PATH . '/views/errors/403.php';
    } else {
        require_once BASE_PATH . '/views/errors/404.php';
    }
}

/**
 * Logger l'activité utilisateur pour le suivi admin
 */
function logUserActivity($userId, $controller, $action) {
    try {
        $db = Database::getInstance()->getConnection();
        
        // Détecter l'adresse IP
        $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        if (strpos($ipAddress, ',') !== false) {
            $ipAddress = trim(explode(',', $ipAddress)[0]);
        }
        
        // User agent
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        // Déterminer le type de log
        $logType = 'user_action';
        if ($controller === 'auth') {
            $logType = $action === 'login' ? 'login' : ($action === 'logout' ? 'logout' : 'user_action');
        } elseif ($controller === 'admin') {
            $logType = 'admin_action';
        }
        
        $stmt = $db->prepare("
            INSERT INTO system_logs (user_id, log_type, action, details, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $actionDescription = ucfirst($controller) . '::' . $action;
        $details = json_encode([
            'controller' => $controller,
            'action' => $action,
            'timestamp' => date('Y-m-d H:i:s'),
            'session_id' => session_id()
        ]);
        
        $stmt->execute([
            $userId,
            $logType,
            $actionDescription,
            $details,
            $ipAddress,
            $userAgent
        ]);
        
    } catch (Exception $e) {
        // Ne pas interrompre l'exécution si le logging échoue
        error_log("Erreur logging activité: " . $e->getMessage());
    }
}

/**
 * Fonction utilitaire pour vérifier les permissions
 */
function hasPermission($requiredRole = 'user') {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    if ($requiredRole === 'admin') {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
    
    return true; // Utilisateur connecté
}

/**
 * Fonction de maintenance (vérification programmée)
 */
function checkMaintenanceMode() {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("
            SELECT setting_value 
            FROM system_settings 
            WHERE setting_key = 'site_maintenance'
        ");
        $result = $stmt->fetch();
        
        if ($result && $result['setting_value'] === 'true') {
            // Autoriser seulement les admins en mode maintenance
            if (!hasPermission('admin')) {
                require_once BASE_PATH . '/views/errors/maintenance.php';
                exit;
            }
        }
    } catch (Exception $e) {
        // En cas d'erreur, continuer normalement
        error_log("Erreur vérification maintenance: " . $e->getMessage());
    }
}

// Vérifier le mode maintenance (optionnel)
if (function_exists('checkMaintenanceMode')) {
    checkMaintenanceMode();
}

/**
 * Nettoyage automatique des sessions expirées
 */
function cleanupExpiredSessions() {
    static $lastCleanup = 0;
    
    // Nettoyer seulement toutes les heures
    if (time() - $lastCleanup > 3600) {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->query("
                DELETE FROM active_sessions 
                WHERE last_activity < DATE_SUB(NOW(), INTERVAL 24 HOUR)
            ");
            $lastCleanup = time();
        } catch (Exception $e) {
            error_log("Erreur nettoyage sessions: " . $e->getMessage());
        }
    }
}

// Nettoyage périodique
cleanupExpiredSessions();

/**
 * Fonction de sécurité : limitation du taux de requêtes
 */
function rateLimitCheck() {
    $clientIP = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $cacheKey = 'rate_limit_' . md5($clientIP);
    
    // Simuler un cache simple avec les sessions
    if (!isset($_SESSION['rate_limits'])) {
        $_SESSION['rate_limits'] = [];
    }
    
    $now = time();
    $windowSize = 60; // 1 minute
    $maxRequests = 100; // 100 requêtes par minute
    
    // Nettoyer les anciens compteurs
    $_SESSION['rate_limits'] = array_filter($_SESSION['rate_limits'], function($timestamp) use ($now, $windowSize) {
        return ($now - $timestamp) < $windowSize;
    });
    
    // Compter les requêtes dans la fenêtre
    $requestCount = count($_SESSION['rate_limits']);
    
    if ($requestCount >= $maxRequests) {
        http_response_code(429);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'Trop de requêtes',
            'message' => 'Veuillez patienter avant de faire une nouvelle requête',
            'retry_after' => $windowSize
        ]);
        exit;
    }
    
    // Ajouter cette requête au compteur
    $_SESSION['rate_limits'][] = $now;
}

// Appliquer la limitation du taux pour les API
if ($controller === 'api') {
    rateLimitCheck();
}

/**
 * Headers de sécurité
 */
function setSecurityHeaders() {
    // Protection XSS
    header('X-XSS-Protection: 1; mode=block');
    
    // Empêcher le sniffing MIME
    header('X-Content-Type-Options: nosniff');
    
    // Protection contre le clickjacking
    header('X-Frame-Options: SAMEORIGIN');
    
    // Politique de référent
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    // Protection contre les attaques de timing
    header('X-Permitted-Cross-Domain-Policies: none');
    
    // En production, ajouter HTTPS
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

// Appliquer les headers de sécurité
setSecurityHeaders();

/**
 * Fonction pour gérer les erreurs globales
 */
function handleGlobalError($errno, $errstr, $errfile, $errline) {
    // Ne pas traiter les erreurs supprimées avec @
    if (!(error_reporting() & $errno)) {
        return false;
    }
    
    $errorTypes = [
        E_ERROR => 'ERREUR FATALE',
        E_WARNING => 'AVERTISSEMENT',
        E_NOTICE => 'NOTICE',
        E_USER_ERROR => 'ERREUR UTILISATEUR',
        E_USER_WARNING => 'AVERTISSEMENT UTILISATEUR',
        E_USER_NOTICE => 'NOTICE UTILISATEUR'
    ];
    
    $errorType = $errorTypes[$errno] ?? 'ERREUR INCONNUE';
    
    // Logger l'erreur
    error_log("[$errorType] $errstr dans $errfile ligne $errline");
    
    return true;
}

// Définir le gestionnaire d'erreurs personnalisé
set_error_handler('handleGlobalError');


?>