<?php
/**
 * Mock Auth class for testing without database
 */

// Define roles if not already defined
if (!defined('ROLE_ADMIN')) define('ROLE_ADMIN', 'admin');
if (!defined('ROLE_ANALYST')) define('ROLE_ANALYST', 'analista');
if (!defined('ROLE_VISITOR')) define('ROLE_VISITOR', 'visitante');

class MockAuth {
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role']
        ];
    }
    
    public function hasRole($role) {
        return $this->isLoggedIn() && $_SESSION['user_role'] === $role;
    }
    
    public function requireRole($role) {
        if (!$this->isLoggedIn()) {
            header('Location: /test_auth.php');
            exit;
        }
        
        if (!$this->hasRole($role)) {
            header('HTTP/1.1 403 Forbidden');
            die('Access denied. Required role: ' . $role . '. Your role: ' . ($_SESSION['user_role'] ?? 'none'));
        }
    }
    
    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            header('Location: /test_auth.php');
            exit;
        }
    }
    
    public function isAdmin() {
        return $this->hasRole(ROLE_ADMIN);
    }
    
    public function canWrite() {
        return $this->hasRole(ROLE_ADMIN) || $this->hasRole(ROLE_ANALYST);
    }
    
    public function canOnlyRead() {
        return $this->hasRole(ROLE_VISITOR);
    }
    
    public function hasAnyRole($roles) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        return in_array($_SESSION['user_role'], $roles);
    }
}

// Mock helper functions
if (!function_exists('escape')) {
    function escape($data) {
        if (is_array($data)) {
            return array_map('escape', $data);
        }
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('formatMoney')) {
    function formatMoney($amount) {
        return '$' . number_format((float)$amount, 2, '.', ',');
    }
}

if (!function_exists('formatPercent')) {
    function formatPercent($number) {
        return number_format((float)$number, 2, '.', ',') . '%';
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date) {
        return date('d/m/Y', strtotime($date));
    }
}

if (!function_exists('formatDateTime')) {
    function formatDateTime($datetime) {
        return date('d/m/Y H:i', strtotime($datetime));
    }
}

if (!function_exists('getRoleName')) {
    function getRoleName($role) {
        $roles = [
            'admin' => 'Administrador',
            'analista' => 'Analista',
            'visitante' => 'Visitante'
        ];
        return $roles[$role] ?? 'Desconocido';
    }
}

if (!function_exists('getRoleColor')) {
    function getRoleColor($role) {
        $colors = [
            'admin' => 'danger',
            'analista' => 'warning',
            'visitante' => 'info'
        ];
        return $colors[$role] ?? 'secondary';
    }
}

if (!function_exists('getProjectStatusColor')) {
    function getProjectStatusColor($status) {
        $colors = [
            'activo' => 'success',
            'pausado' => 'warning',
            'completado' => 'info',
            'cancelado' => 'danger'
        ];
        return $colors[$status] ?? 'secondary';
    }
}

if (!function_exists('getProjectStatusName')) {
    function getProjectStatusName($status) {
        $statuses = [
            'activo' => 'Activo',
            'pausado' => 'Pausado',
            'completado' => 'Completado',
            'cancelado' => 'Cancelado'
        ];
        return $statuses[$status] ?? 'Desconocido';
    }
}

if (!function_exists('truncate')) {
    function truncate($text, $length = 100) {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length - 3) . '...';
    }
}