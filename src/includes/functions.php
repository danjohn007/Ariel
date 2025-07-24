<?php
/**
 * Funciones auxiliares del sistema
 * Sistema de Análisis de Precios y Programa de Obra
 */

/**
 * Escapar y limpiar datos de salida para prevenir XSS
 */
function escape($data) {
    if (is_array($data)) {
        return array_map('escape', $data);
    }
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Validar y limpiar datos de entrada
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return trim(strip_tags($data));
}

/**
 * Formatear número como moneda mexicana
 */
function formatMoney($amount, $currency = 'MXN') {
    if (!is_numeric($amount)) {
        return '$0.00';
    }
    
    return '$' . number_format((float)$amount, 2, '.', ',');
}

/**
 * Formatear número con decimales
 */
function formatNumber($number, $decimals = 2) {
    if (!is_numeric($number)) {
        return '0.00';
    }
    
    return number_format((float)$number, $decimals, '.', ',');
}

/**
 * Formatear porcentaje
 */
function formatPercent($number, $decimals = 2) {
    if (!is_numeric($number)) {
        return '0.00%';
    }
    
    return number_format((float)$number, $decimals, '.', ',') . '%';
}

/**
 * Formatear fecha
 */
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date) || $date === '0000-00-00') {
        return '-';
    }
    
    $dateTime = DateTime::createFromFormat('Y-m-d', $date);
    if (!$dateTime) {
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $date);
    }
    
    return $dateTime ? $dateTime->format($format) : '-';
}

/**
 * Formatear fecha y hora
 */
function formatDateTime($datetime, $format = 'd/m/Y H:i') {
    if (empty($datetime) || $datetime === '0000-00-00 00:00:00') {
        return '-';
    }
    
    $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
    return $dateTime ? $dateTime->format($format) : '-';
}

/**
 * Obtener nombre del rol en español
 */
function getRoleName($role) {
    $roles = [
        'admin' => 'Administrador',
        'analista' => 'Analista',
        'visitante' => 'Visitante'
    ];
    
    return $roles[$role] ?? 'Desconocido';
}

/**
 * Obtener color del rol para UI
 */
function getRoleColor($role) {
    $colors = [
        'admin' => 'danger',
        'analista' => 'warning',
        'visitante' => 'info'
    ];
    
    return $colors[$role] ?? 'secondary';
}

/**
 * Obtener nombre del estado del proyecto
 */
function getProjectStatusName($status) {
    $statuses = [
        'activo' => 'Activo',
        'pausado' => 'Pausado',
        'completado' => 'Completado',
        'cancelado' => 'Cancelado'
    ];
    
    return $statuses[$status] ?? 'Desconocido';
}

/**
 * Obtener color del estado del proyecto
 */
function getProjectStatusColor($status) {
    $colors = [
        'activo' => 'success',
        'pausado' => 'warning',
        'completado' => 'info',
        'cancelado' => 'danger'
    ];
    
    return $colors[$status] ?? 'secondary';
}

/**
 * Generar token CSRF
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verificar token CSRF
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generar campo oculto con token CSRF
 */
function csrfField() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . escape($token) . '">';
}

/**
 * Validar email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validar número
 */
function isValidNumber($number) {
    return is_numeric($number) && $number >= 0;
}

/**
 * Validar fecha
 */
function isValidDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * Obtener URL completa
 */
function fullUrl($path = '') {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . '://' . $host . $path;
}

/**
 * Redirigir con mensaje
 */
function redirectWithMessage($url, $message, $type = 'success') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
    header("Location: $url");
    exit;
}

/**
 * Obtener y limpiar mensaje flash
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'success';
        
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        
        return ['message' => $message, 'type' => $type];
    }
    
    return null;
}

/**
 * Generar breadcrumbs
 */
function breadcrumb($items) {
    if (empty($items)) {
        return '';
    }
    
    $html = '<nav aria-label="breadcrumb">';
    $html .= '<ol class="breadcrumb">';
    
    $count = count($items);
    foreach ($items as $index => $item) {
        if ($index === $count - 1) {
            $html .= '<li class="breadcrumb-item active" aria-current="page">' . escape($item['text']) . '</li>';
        } else {
            if (isset($item['url'])) {
                $html .= '<li class="breadcrumb-item"><a href="' . escape($item['url']) . '">' . escape($item['text']) . '</a></li>';
            } else {
                $html .= '<li class="breadcrumb-item">' . escape($item['text']) . '</li>';
            }
        }
    }
    
    $html .= '</ol>';
    $html .= '</nav>';
    
    return $html;
}

/**
 * Paginar resultados
 */
function paginate($totalItems, $itemsPerPage, $currentPage, $baseUrl) {
    $totalPages = ceil($totalItems / $itemsPerPage);
    
    if ($totalPages <= 1) {
        return '';
    }
    
    $html = '<nav aria-label="Paginación">';
    $html .= '<ul class="pagination justify-content-center">';
    
    // Botón anterior
    if ($currentPage > 1) {
        $prevPage = $currentPage - 1;
        $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="' . $baseUrl . '?page=' . $prevPage . '">Anterior</a>';
        $html .= '</li>';
    } else {
        $html .= '<li class="page-item disabled">';
        $html .= '<span class="page-link">Anterior</span>';
        $html .= '</li>';
    }
    
    // Números de página
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $currentPage + 2);
    
    if ($startPage > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=1">1</a></li>';
        if ($startPage > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    for ($i = $startPage; $i <= $endPage; $i++) {
        if ($i == $currentPage) {
            $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . $i . '">' . $i . '</a></li>';
        }
    }
    
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . $totalPages . '">' . $totalPages . '</a></li>';
    }
    
    // Botón siguiente
    if ($currentPage < $totalPages) {
        $nextPage = $currentPage + 1;
        $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="' . $baseUrl . '?page=' . $nextPage . '">Siguiente</a>';
        $html .= '</li>';
    } else {
        $html .= '<li class="page-item disabled">';
        $html .= '<span class="page-link">Siguiente</span>';
        $html .= '</li>';
    }
    
    $html .= '</ul>';
    $html .= '</nav>';
    
    return $html;
}

/**
 * Generar ID único
 */
function generateUniqueId($prefix = '') {
    return $prefix . uniqid() . mt_rand(1000, 9999);
}

/**
 * Convertir bytes a formato legible
 */
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * Verificar si una cadena contiene otra (insensible a mayúsculas)
 */
function contains($haystack, $needle) {
    return stripos($haystack, $needle) !== false;
}

/**
 * Truncar texto
 */
function truncate($text, $length = 100, $ending = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length - strlen($ending)) . $ending;
}