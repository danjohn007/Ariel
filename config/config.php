<?php
/**
 * Configuration file for Mechanical FIX
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'mechanical_fix');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Application settings
define('APP_NAME', 'Mechanical FIX');
define('APP_URL', 'http://localhost:8080');
define('APP_ENV', 'development'); // development, production

// Security
define('SECRET_KEY', 'mechanical_fix_secret_key_2024');
define('SESSION_LIFETIME', 7200); // 2 hours

// Email configuration
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_USERNAME', '');
define('MAIL_PASSWORD', '');
define('MAIL_PORT', 587);
define('MAIL_ENCRYPTION', 'tls');
define('MAIL_FROM_EMAIL', 'noreply@mechanicalfix.com');
define('MAIL_FROM_NAME', 'Mechanical FIX');

// Google Maps API
define('GOOGLE_MAPS_API_KEY', '');

// Payment Gateway (example: Stripe)
define('STRIPE_PUBLISHABLE_KEY', '');
define('STRIPE_SECRET_KEY', '');

// File upload settings
define('MAX_FILE_SIZE', 10485760); // 10MB
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'mp4', 'avi']);

// Timezone
date_default_timezone_set('America/Mexico_City');