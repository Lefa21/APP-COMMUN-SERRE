<?php
// index.php - Point d'entrée principal
session_start();

// Configuration de base
define('BASE_PATH', __DIR__);
define('BASE_URL', 'http://localhost/greenhouse-project/');

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
$controllerClass = ucfirst($controller) . 'Controller';

try {
    if (class_exists($controllerClass)) {
        $controllerInstance = new $controllerClass();
        
        if (method_exists($controllerInstance, $action)) {
            $controllerInstance->$action();
        } else {
            throw new Exception("Action non trouvée");
        }
    } else {
        throw new Exception("Contrôleur non trouvé");
    }
} catch (Exception $e) {
    // Page 404 basique
    http_response_code(404);
    require_once BASE_PATH . '/views/errors/404.php';
}
?>