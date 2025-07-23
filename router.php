<?php
/**
 * Router script for PHP built-in development server
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve static files directly
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Route everything else through index.php
include __DIR__ . '/index.php';