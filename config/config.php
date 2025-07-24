<?php
/**
 * Configuración principal del sistema
 * Sistema de Análisis de Precios y Programa de Obra
 */

// Cargar variables de entorno
function loadEnv($path) {
    if (!file_exists($path)) {
        throw new Exception("Archivo .env no encontrado en: $path");
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Cargar archivo .env
loadEnv(__DIR__ . '/../.env');

// Configuración de la base de datos
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'construccion_db');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

// Configuración de la aplicación
define('APP_NAME', getenv('APP_NAME') ?: 'Sistema de Construcción');
define('APP_ENV', getenv('APP_ENV') ?: 'production');
define('APP_DEBUG', getenv('APP_DEBUG') === 'true');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost');

// Configuración de sesiones
define('SESSION_NAME', getenv('SESSION_NAME') ?: 'construccion_session');
define('SESSION_LIFETIME', getenv('SESSION_LIFETIME') ?: 7200);

// Configuración de seguridad
define('ENCRYPTION_KEY', getenv('ENCRYPTION_KEY') ?: 'default-key-change-this');
define('PASSWORD_SALT', getenv('PASSWORD_SALT') ?: 'default-salt-change-this');

// Configuración de zona horaria
date_default_timezone_set('America/Mexico_City');

// Configuración de errores
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Configuración de sesiones PHP
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 en HTTPS

// Rutas del sistema
define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('SRC_PATH', ROOT_PATH . '/src');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('MIGRATIONS_PATH', ROOT_PATH . '/migrations');
define('DOCS_PATH', ROOT_PATH . '/docs');

// Roles de usuario
define('ROLE_ADMIN', 'admin');
define('ROLE_ANALYST', 'analista');
define('ROLE_VISITOR', 'visitante');

// Estados de proyecto
define('PROJECT_STATUS_ACTIVE', 'activo');
define('PROJECT_STATUS_PAUSED', 'pausado');
define('PROJECT_STATUS_COMPLETED', 'completado');
define('PROJECT_STATUS_CANCELLED', 'cancelado');

// Funciones auxiliares
function env($key, $default = null) {
    return getenv($key) ?: $default;
}

function config($key, $default = null) {
    $config = [
        'app.name' => APP_NAME,
        'app.env' => APP_ENV,
        'app.debug' => APP_DEBUG,
        'app.url' => APP_URL,
        'db.host' => DB_HOST,
        'db.port' => DB_PORT,
        'db.name' => DB_NAME,
        'db.user' => DB_USER,
        'db.pass' => DB_PASS,
        'session.name' => SESSION_NAME,
        'session.lifetime' => SESSION_LIFETIME,
    ];
    
    return $config[$key] ?? $default;
}

// Autoloader simple
spl_autoload_register(function ($class) {
    $file = SRC_PATH . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});