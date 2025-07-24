<?php
/**
 * Authentication Functions
 * Access control and session management
 */

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../includes/security.php';

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    // Determine session name from current page/context
    $rol = getCurrentUserRole();
    if ($rol) {
        session_name('ariel_' . $rol . '_session');
    }
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $user = new User();
    return $user->verifySession();
}

/**
 * Get current user role from session or URL context
 */
function getCurrentUserRole() {
    // Try to get from session first
    if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['rol'])) {
        return $_SESSION['rol'];
    }
    
    // Try to determine from URL path
    $path = $_SERVER['REQUEST_URI'] ?? '';
    if (strpos($path, '/admin/') !== false) {
        return 'admin';
    } elseif (strpos($path, '/analista/') !== false) {
        return 'analista';
    } elseif (strpos($path, '/visitante/') !== false) {
        return 'visitante';
    }
    
    return null;
}

/**
 * Start session for specific role
 */
function startRoleSession($rol) {
    session_name('ariel_' . $rol . '_session');
    session_start();
}

/**
 * Verify access to page based on allowed roles
 */
function verificarAcceso($allowedRoles = []) {
    if (!isLoggedIn()) {
        redirectToLogin();
        exit;
    }
    
    $userRole = $_SESSION['rol'] ?? null;
    
    if (!empty($allowedRoles) && !in_array($userRole, $allowedRoles)) {
        redirectToUnauthorized();
        exit;
    }
    
    return true;
}

/**
 * Require authentication for page
 */
function requireAuth($allowedRoles = []) {
    return verificarAcceso($allowedRoles);
}

/**
 * Get current user data
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'email' => $_SESSION['email'] ?? null,
        'nombre' => $_SESSION['nombre'] ?? null,
        'rol' => $_SESSION['rol'] ?? null
    ];
}

/**
 * Check if current user has specific role
 */
function hasRole($role) {
    $user = getCurrentUser();
    return $user && $user['rol'] === $role;
}

/**
 * Check if current user is admin
 */
function isAdmin() {
    return hasRole('admin');
}

/**
 * Check if current user is analyst
 */
function isAnalista() {
    return hasRole('analista');
}

/**
 * Check if current user is visitor
 */
function isVisitante() {
    return hasRole('visitante');
}

/**
 * Redirect to login page
 */
function redirectToLogin() {
    $loginUrl = '/login.php';
    
    // Store current page for redirect after login
    $currentPage = $_SERVER['REQUEST_URI'] ?? '';
    if ($currentPage && $currentPage !== '/login.php' && $currentPage !== '/logout.php') {
        $_SESSION['redirect_after_login'] = $currentPage;
    }
    
    header('Location: ' . $loginUrl);
    exit;
}

/**
 * Redirect to unauthorized page
 */
function redirectToUnauthorized() {
    http_response_code(403);
    header('Location: /unauthorized.php');
    exit;
}

/**
 * Redirect after successful login based on role
 */
function redirectAfterLogin($rol) {
    // Check if there's a stored redirect URL
    if (isset($_SESSION['redirect_after_login'])) {
        $redirectUrl = $_SESSION['redirect_after_login'];
        unset($_SESSION['redirect_after_login']);
        header('Location: ' . $redirectUrl);
        return;
    }
    
    // Default role-based redirects
    switch ($rol) {
        case 'admin':
            header('Location: /admin/usuarios.php');
            break;
        case 'analista':
            header('Location: /analista/avance.php');
            break;
        case 'visitante':
            header('Location: /visitante/programa.php');
            break;
        default:
            header('Location: /index.php');
            break;
    }
}

/**
 * Generate navigation menu based on user role
 */
function generateRoleMenu($currentPage = '') {
    $user = getCurrentUser();
    if (!$user) {
        return '';
    }
    
    $menu = '<nav class="role-menu">';
    $menu .= '<ul>';
    
    switch ($user['rol']) {
        case 'admin':
            $menu .= '<li><a href="/admin/usuarios.php"' . ($currentPage === 'usuarios' ? ' class="active"' : '') . '>Usuarios</a></li>';
            $menu .= '<li><a href="/admin/obras.php"' . ($currentPage === 'obras' ? ' class="active"' : '') . '>Obras</a></li>';
            $menu .= '<li><a href="/admin/reportes.php"' . ($currentPage === 'reportes' ? ' class="active"' : '') . '>Reportes</a></li>';
            break;
            
        case 'analista':
            $menu .= '<li><a href="/analista/avance.php"' . ($currentPage === 'avance' ? ' class="active"' : '') . '>Avance</a></li>';
            $menu .= '<li><a href="/analista/obras.php"' . ($currentPage === 'obras' ? ' class="active"' : '') . '>Obras</a></li>';
            $menu .= '<li><a href="/analista/reportes.php"' . ($currentPage === 'reportes' ? ' class="active"' : '') . '>Reportes</a></li>';
            break;
            
        case 'visitante':
            $menu .= '<li><a href="/visitante/programa.php"' . ($currentPage === 'programa' ? ' class="active"' : '') . '>Programa</a></li>';
            $menu .= '<li><a href="/visitante/reportes.php"' . ($currentPage === 'reportes' ? ' class="active"' : '') . '>Reportes</a></li>';
            break;
    }
    
    $menu .= '<li><a href="/logout.php">Cerrar Sesión</a></li>';
    $menu .= '</ul>';
    $menu .= '</nav>';
    
    return $menu;
}

/**
 * Clean expired sessions (should be called periodically)
 */
function cleanExpiredSessions() {
    $user = new User();
    $user->cleanExpiredSessions();
}

/**
 * Initialize session security
 */
function initSessionSecurity() {
    // Session security settings
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Strict');
    
    // Clean expired sessions occasionally (10% chance)
    if (rand(1, 100) <= 10) {
        cleanExpiredSessions();
    }
}

// Initialize session security
initSessionSecurity();