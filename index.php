<?php
// index.php - Version avec ordre correct
session_start();

// Configuration de base
define('BASE_PATH', __DIR__);
define('BASE_URL', 'https://green-pulse.herogu.garageisep.com/');

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
// Inclure la configuration
require_once BASE_PATH . '/config/Database.php';
require_once BASE_PATH . '/helpers/conversion_helper.php'; 

spl_autoload_register(function ($class) {
    $paths = [
        BASE_PATH . '/controllers/' . $class . '.php',
        BASE_PATH . '/models/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});



// Router simple
$controllerName = $_GET['controller'] ?? 'home';
$actionName = $_GET['action'] ?? 'index';


// Sécurisation des paramètres
$controllerName = preg_replace('/[^a-zA-Z0-9]/', '', $controllerName);
$actionName = preg_replace('/[^a-zA-Z0-9]/', '', $actionName);



// Mapping des contrôleurs
$controllerMappings = [
    'home' => 'HomeController',
    'auth' => 'AuthController',
    'sensor' => 'SensorController',
    'actuator' => 'ActuatorController',
    'api' => 'ApiController',
    'profile' => 'ProfileController',
    'admin' => 'AdminController',
];

// Contrôleurs nécessitant une authentification
$requireAuth = [
    'sensor', 'actuator', 'api', 'profile', 'admin'
];

// Contrôleurs nécessitant des privilèges admin
$requireAdmin = [
    'admin'
];



// 🚨 VÉRIFICATIONS DE SÉCURITÉ AVEC REDIRECTIONS HEADERS (avant tout output)
try {
    if (!isset($controllerMappings[$controllerName])) {
        throw new Exception("Contrôleur '$controllerName' non valide.");
    }
    $controllerClass = $controllerMappings[$controllerName];
    $controllerFile = BASE_PATH . '/controllers/' . $controllerClass . '.php';

    if (!file_exists($controllerFile)) {
        throw new Exception("Fichier contrôleur introuvable : $controllerFile");
    }
    
    require_once $controllerFile;
    
    if (class_exists($controllerClass)) {
        $controllerInstance = new $controllerClass();
        if (method_exists($controllerInstance, $actionName)) {
            // Journalisation de l'activité (si nécessaire)
            if (isset($_SESSION['user_id'])) {
                logUserActivity($_SESSION['user_id'], $controllerName, $actionName);
            }
            // Appel de l'action du contrôleur
            $controllerInstance->$actionName();
        } else {
            throw new Exception("Action '$actionName' non trouvée dans le contrôleur '$controllerClass'.");
        }

    } else {
        throw new Exception("Classe contrôleur '$controllerClass' non trouvée.");
    }

} catch (Exception $e) {
    // Enregistrer l'erreur pour le débogage
    error_log("Erreur de routage: " . $e->getMessage());
    
    // Afficher la page d'erreur 404
    http_response_code(404);
    require_once BASE_PATH . '/views/errors/404.php';
    exit();
}

/**
 * Logger l'activité utilisateur
 */
function logUserActivity($userId, $controller, $action) {
    try {
        $db = Database::getConnection('local');
        
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