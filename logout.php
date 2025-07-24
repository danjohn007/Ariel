<?php
/**
 * Logout Page
 * Sistema Web de Análisis de Precios y Programa de Obra
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/models/User.php';

// Start session to access user data
$rol = getCurrentUserRole();
if ($rol) {
    startRoleSession($rol);
}

// Perform logout
if (isLoggedIn()) {
    $user = new User();
    $user->logout();
}

// Clear any remaining session data
if (session_status() === PHP_SESSION_ACTIVE) {
    session_unset();
    session_destroy();
}

// Set success message for login page
session_start();
$_SESSION['flash_message'] = 'Sesión cerrada exitosamente';
$_SESSION['flash_type'] = 'success';

// Redirect to login page
header('Location: /login.php');
exit;