<?php
/**
 * Mechanical FIX - Sistema de Mecánicos a Domicilio
 * Main entry point for the application
 */

// Start session
session_start();

// Set error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define constants
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// Include autoloader
require_once ROOT_PATH . '/vendor/autoload.php';

// Include configuration
require_once CONFIG_PATH . '/config.php';

// Include core classes
require_once APP_PATH . '/core/Router.php';
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Model.php';
require_once APP_PATH . '/core/View.php';
require_once APP_PATH . '/core/Database.php';

// Initialize router
$router = new Router();

// Define routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('home', ['controller' => 'Home', 'action' => 'index']);
$router->add('login', ['controller' => 'Auth', 'action' => 'login']);
$router->add('logout', ['controller' => 'Auth', 'action' => 'logout']);
$router->add('register', ['controller' => 'Auth', 'action' => 'register']);
$router->add('dashboard', ['controller' => 'Dashboard', 'action' => 'index']);
$router->add('services/request', ['controller' => 'Services', 'action' => 'request']);
$router->add('services/manage', ['controller' => 'Services', 'action' => 'manage']);
$router->add('services/view/{id}', ['controller' => 'Services', 'action' => 'view']);
$router->add('mechanic/dashboard', ['controller' => 'Mechanic', 'action' => 'dashboard']);
$router->add('client/dashboard', ['controller' => 'Client', 'action' => 'dashboard']);
$router->add('admin/dashboard', ['controller' => 'Admin', 'action' => 'dashboard']);
$router->add('reports', ['controller' => 'Reports', 'action' => 'index']);

// Get URL
$url = $_SERVER['REQUEST_URI'];
$url = trim($url, '/');

// Remove query string
if (($pos = strpos($url, '?')) !== false) {
    $url = substr($url, 0, $pos);
}

// Remove base path if exists
$basePath = trim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($basePath && strpos($url, $basePath) === 0) {
    $url = substr($url, strlen($basePath));
    $url = trim($url, '/');
}

// Dispatch request
try {
    $router->dispatch($url);
} catch (Exception $e) {
    // Handle errors
    http_response_code(500);
    include APP_PATH . '/views/errors/500.php';
}