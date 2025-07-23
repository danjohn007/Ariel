<?php
/**
 * Simple autoloader for Mechanical FIX
 */

spl_autoload_register(function ($className) {
    // Define class paths
    $paths = [
        APP_PATH . '/core/',
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
    ];
    
    foreach ($paths as $path) {
        $file = $path . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});