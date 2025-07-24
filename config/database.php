<?php
/**
 * Database Configuration
 * Sistema Web de Análisis de Precios y Programa de Obra
 */

// Database configuration
// For demo purposes, using SQLite
define('DB_HOST', 'localhost');
define('DB_NAME', __DIR__ . '/../demo.sqlite');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8');
define('USE_SQLITE', true);

// Security configuration
define('BCRYPT_COST', 12);
define('SESSION_LIFETIME', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_DURATION', 900); // 15 minutes

// Application configuration
define('APP_NAME', 'Sistema de Análisis de Precios');
define('APP_VERSION', '1.0.0');
define('TIMEZONE', 'America/Mexico_City');

// Admin email restriction
define('ADMIN_EMAILS', ['admin@empresa.com']);

// CSRF token configuration
define('CSRF_TOKEN_LIFETIME', 1800); // 30 minutes

// Set timezone
date_default_timezone_set(TIMEZONE);

// Security headers
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}