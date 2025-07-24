<?php
/**
 * Logout - Cerrar sesión
 * Sistema de Análisis de Precios y Programa de Obra
 */

// Cargar configuración
require_once __DIR__ . '/../config/config.php';
require_once SRC_PATH . '/includes/Database.php';
require_once SRC_PATH . '/includes/Auth.php';
require_once SRC_PATH . '/includes/functions.php';

// Inicializar autenticación
$auth = new Auth();

// Cerrar sesión
$auth->logout();

// Redirigir al login con mensaje
redirectWithMessage('/', 'Sesión cerrada exitosamente.', 'success');
?>