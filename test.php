<?php
/**
 * Simple test script to verify the system works
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define constants
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// Test basic includes
echo "<h1>Mechanical FIX - System Test</h1>";

// Test 1: Check if core files exist
echo "<h2>Test 1: Core Files</h2>";
$coreFiles = [
    '/app/core/Router.php',
    '/app/core/Controller.php',
    '/app/core/Model.php',
    '/app/core/View.php',
    '/app/core/Database.php'
];

foreach ($coreFiles as $file) {
    $path = ROOT_PATH . $file;
    if (file_exists($path)) {
        echo "✅ " . $file . " exists<br>";
    } else {
        echo "❌ " . $file . " missing<br>";
    }
}

// Test 2: Include autoloader
echo "<h2>Test 2: Autoloader</h2>";
try {
    require_once ROOT_PATH . '/vendor/autoload.php';
    echo "✅ Autoloader loaded successfully<br>";
} catch (Exception $e) {
    echo "❌ Autoloader failed: " . $e->getMessage() . "<br>";
}

// Test 3: Load core classes
echo "<h2>Test 3: Core Classes</h2>";
$classes = ['Router', 'Controller', 'Model', 'View'];

foreach ($classes as $class) {
    try {
        $file = APP_PATH . '/core/' . $class . '.php';
        require_once $file;
        echo "✅ " . $class . " class loaded<br>";
    } catch (Exception $e) {
        echo "❌ " . $class . " failed: " . $e->getMessage() . "<br>";
    }
}

// Test 4: Test Router
echo "<h2>Test 4: Router</h2>";
try {
    $router = new Router();
    $router->add('test', ['controller' => 'Home', 'action' => 'index']);
    $match = $router->match('test');
    if ($match && $match['controller'] === 'Home') {
        echo "✅ Router working correctly<br>";
    } else {
        echo "❌ Router not matching routes correctly<br>";
    }
} catch (Exception $e) {
    echo "❌ Router test failed: " . $e->getMessage() . "<br>";
}

// Test 5: Test View
echo "<h2>Test 5: View System</h2>";
try {
    $view = new View();
    $view->set('test_var', 'Hello World');
    echo "✅ View system working<br>";
} catch (Exception $e) {
    echo "❌ View test failed: " . $e->getMessage() . "<br>";
}

// Test 6: Check PHP version and extensions
echo "<h2>Test 6: PHP Environment</h2>";
echo "PHP Version: " . PHP_VERSION . "<br>";

$requiredExtensions = ['pdo', 'json', 'mbstring', 'openssl'];
foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✅ " . $ext . " extension loaded<br>";
    } else {
        echo "❌ " . $ext . " extension missing<br>";
    }
}

// Test 7: File permissions
echo "<h2>Test 7: File Permissions</h2>";
$writableDirs = ['/uploads'];

foreach ($writableDirs as $dir) {
    $path = ROOT_PATH . $dir;
    if (is_dir($path)) {
        if (is_writable($path)) {
            echo "✅ " . $dir . " is writable<br>";
        } else {
            echo "⚠️ " . $dir . " is not writable<br>";
        }
    } else {
        echo "ℹ️ " . $dir . " directory doesn't exist yet<br>";
    }
}

echo "<h2>System Test Complete</h2>";
echo "<p>If all tests passed with ✅, the system is ready to run!</p>";
echo "<p><a href='index.php'>Go to Main Application</a></p>";
?>