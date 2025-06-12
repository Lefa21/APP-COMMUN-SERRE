<?php
// index.php - Version avec ordre correct
session_start();

// Configuration de base
define('BASE_PATH', __DIR__);
define('BASE_URL', 'http://localhost/APP-COMMUN-SERRE/');

// 🔑 HEADERS DE SÉCURITÉ EN PREMIER (avant toute sortie HTML)
function setSecurityHeaders() {
    if (!headers_sent()) {
        header('X-XSS-Protection: 1; mode=block');
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    }
}

// ⚡ APPLIQUER LES HEADERS IMMÉDIATEMENT
setSecurityHeaders();

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

// Router simple
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Sécurisation des paramètres
$controller = preg_replace('/[^a-zA-Z]/', '', $controller);
$action = preg_replace('/[^a-zA-Z]/', '', $action);

// Mapping des contrôleurs
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

// 🚨 VÉRIFICATIONS DE SÉCURITÉ AVEC REDIRECTIONS HEADERS (avant tout output)
try {
    // Vérifier si le contrôleur existe
    if (!isset($controllerMappings[$controller])) {
        throw new Exception("Contrôleur '$controller' non trouvé");
    }
    
    $controllerClass = $controllerMappings[$controller];
    
    // Vérifications d'authentification AVANT tout output
    if (in_array($controller, $requireAuth)) {
        if (!isset($_SESSION['user_id'])) {
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
    
    // Vérifier que le fichier du contrôleur existe
    $controllerFile = BASE_PATH . '/controllers/' . $controllerClass . '.php';
    if (!file_exists($controllerFile)) {
        throw new Exception("Fichier contrôleur '$controllerFile' non trouvé");
    }
    
    // 🎯 MAINTENANT on peut exécuter le contrôleur (qui va afficher du HTML)
    if (class_exists($controllerClass)) {
        $controllerInstance = new $controllerClass();
        
        if (method_exists($controllerInstance, $action)) {
            // Log de l'activité utilisateur
            if (isset($_SESSION['user_id']) && in_array($controller, ['actuator', 'admin', 'profile'])) {
                logUserActivity($_SESSION['user_id'], $controller, $action);
            }
            
            // ✅ ICI le contrôleur va appeler render() qui va inclure layout.php
            $controllerInstance->$action();
        } else {
            throw new Exception("Action '$action' non trouvée dans le contrôleur '$controllerClass'");
        }
    } else {
        throw new Exception("Classe contrôleur '$controllerClass' non trouvée");
    }
} catch (Exception $e) {
    // Log de l'erreur
    error_log("Erreur routing: " . $e->getMessage() . " - Controller: $controller, Action: $action");
    
    // Pages d'erreur (ici on peut envoyer des headers car pas encore d'output)
    if (strpos($e->getMessage(), 'non autorisé') !== false) {
        http_response_code(403);
        require_once BASE_PATH . '/views/errors/403.php';
    } else {
        http_response_code(404);
        require_once BASE_PATH . '/views/errors/404.php';
    }
}

/**
 * Logger l'activité utilisateur
 */
function logUserActivity($userId, $controller, $action) {
    try {
        $db = Database::getInstance()->getConnection();
        
        $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        if (strpos($ipAddress, ',') !== false) {
            $ipAddress = trim(explode(',', $ipAddress)[0]);
        }
        
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        $logType = 'user_action';
        if ($controller === 'auth') {
            $logType = $action === 'login' ? 'login' : ($action === 'logout' ? 'logout' : 'user_action');
        } elseif ($controller === 'admin') {
            $logType = 'admin_action';
        }
        
        // Vérifier si la table system_logs existe
        $stmt = $db->query("SHOW TABLES LIKE 'system_logs'");
        if ($stmt->rowCount() > 0) {
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
        }
        
    } catch (Exception $e) {
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
    
    return true;
}

// Debug (optionnel)
if (isset($_GET['test_controllers'])) {
    echo "<h2>Test des contrôleurs disponibles :</h2>";
    foreach ($controllerMappings as $name => $class) {
        $file = BASE_PATH . '/controllers/' . $class . '.php';
        $exists = file_exists($file);
        $color = $exists ? 'green' : 'red';
        echo "<p style='color: $color'>$name -> $class (" . ($exists ? 'OK' : 'MANQUANT') . ")</p>";
    }
    exit;
}
?>