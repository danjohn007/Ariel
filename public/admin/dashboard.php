<?php
/**
 * Dashboard de Administrador
 * Sistema de Análisis de Precios y Programa de Obra
 */

// Start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if we're in test mode
$isTestMode = isset($_SESSION['user_id']) && !file_exists(__DIR__ . '/../../.env_production');

if ($isTestMode) {
    // Use mock authentication for testing
    require_once __DIR__ . '/../mock_auth.php';
    $auth = new MockAuth();
    
    // Mock database class
    class MockDatabase {
        public function count($sql, $params = []) { return rand(5, 50); }
        public function fetchOne($sql, $params = []) { return ['total' => rand(1000000, 50000000)]; }
        public function fetchAll($sql, $params = []) { return []; }
    }
    $db = new MockDatabase();
    
} else {
    // Use real authentication and database
    require_once __DIR__ . '/../../config/config.php';
    require_once SRC_PATH . '/includes/Database.php';
    require_once SRC_PATH . '/includes/Auth.php';
    require_once SRC_PATH . '/includes/functions.php';
    
    // Inicializar autenticación y verificar rol de administrador
    $auth = new Auth();
    $db = Database::getInstance();
}

// Always require admin role
$auth->requireRole(ROLE_ADMIN);
$currentUser = $auth->getCurrentUser();

// Obtener estadísticas avanzadas para administradores
if ($isTestMode) {
    // Mock data for testing
    $totalUsuarios = 15;
    $usuariosAdmin = 2;
    $usuariosAnalista = 7;
    $usuariosVisitante = 6;
    $totalObras = 8;
    $obrasActivas = 5;
    $totalConceptos = 234;
    $totalMateriales = 156;
    $totalProveedores = 23;
    $presupuestoTotal = 15750000;
    $actividadReciente = [];
    $usuariosActivos = [];
    $obrasPorEstado = [];
} else {
try {
    // Estadísticas de usuarios
    $totalUsuarios = $db->count("SELECT COUNT(*) FROM usuarios WHERE activo = 1");
    $usuariosAdmin = $db->count("SELECT COUNT(*) FROM usuarios WHERE rol = 'admin' AND activo = 1");
    $usuariosAnalista = $db->count("SELECT COUNT(*) FROM usuarios WHERE rol = 'analista' AND activo = 1");
    $usuariosVisitante = $db->count("SELECT COUNT(*) FROM usuarios WHERE rol = 'visitante' AND activo = 1");
    
    // Estadísticas de obras
    $totalObras = $db->count("SELECT COUNT(*) FROM obras WHERE estado != 'cancelado'");
    $obrasActivas = $db->count("SELECT COUNT(*) FROM obras WHERE estado = 'activo'");
    
    // Estadísticas del sistema
    $totalConceptos = $db->count("SELECT COUNT(*) FROM conceptos WHERE activo = 1");
    $totalMateriales = $db->count("SELECT COUNT(*) FROM materiales WHERE activo = 1");
    $totalProveedores = $db->count("SELECT COUNT(*) FROM proveedores WHERE activo = 1");
    
    // Presupuesto total
    $presupuestoTotal = $db->fetchOne("SELECT SUM(presupuesto_actual) as total FROM obras WHERE estado != 'cancelado'")['total'] ?? 0;
    
    // Actividad reciente del sistema
    $actividadReciente = $db->fetchAll("
        SELECT l.*, u.nombre, u.apellidos 
        FROM logs_auditoria l 
        LEFT JOIN usuarios u ON l.usuario_id = u.id 
        ORDER BY l.fecha_creacion DESC 
        LIMIT 15
    ");
    
    // Usuarios más activos
    $usuariosActivos = $db->fetchAll("
        SELECT u.nombre, u.apellidos, u.rol, u.ultimo_acceso,
               COUNT(l.id) as total_acciones
        FROM usuarios u 
        LEFT JOIN logs_auditoria l ON u.id = l.usuario_id 
        WHERE u.activo = 1 
        GROUP BY u.id 
        ORDER BY total_acciones DESC, u.ultimo_acceso DESC 
        LIMIT 10
    ");
    
    // Obras por estado
    $obrasPorEstado = $db->fetchAll("
        SELECT estado, COUNT(*) as cantidad 
        FROM obras 
        GROUP BY estado
    ");
    
} catch (Exception $e) {
    $error = "Error al cargar el dashboard: " . $e->getMessage();
}
}

$pageTitle = 'Dashboard Administrador';
$breadcrumbs = [
    ['text' => 'Administración'],
    ['text' => 'Dashboard']
];

// Capturar contenido
ob_start();
?>

<div class="container-fluid">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger" role="alert">
            <i class="bi bi-exclamation-triangle"></i>
            <?= escape($error) ?>
        </div>
    <?php endif; ?>
    
    <!-- Bienvenida -->
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="bi bi-shield-check text-danger"></i>
                        Panel de Administrador
                    </h1>
                    <p class="text-muted">
                        Bienvenido, <?= escape($currentUser['name']) ?> - Gestión completa del sistema
                    </p>
                </div>
                <div class="text-end">
                    <span class="badge bg-danger fs-6">
                        <?= getRoleName($currentUser['role']) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tarjetas de estadísticas principales -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm border-start border-4 border-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted mb-2">Total Usuarios</h6>
                            <h3 class="mb-0 text-primary"><?= number_format($totalUsuarios) ?></h3>
                        </div>
                        <div class="ms-3">
                            <div class="bg-primary rounded-circle p-3">
                                <i class="bi bi-people text-white fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm border-start border-4 border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted mb-2">Obras Activas</h6>
                            <h3 class="mb-0 text-success"><?= number_format($obrasActivas) ?></h3>
                        </div>
                        <div class="ms-3">
                            <div class="bg-success rounded-circle p-3">
                                <i class="bi bi-building text-white fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm border-start border-4 border-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted mb-2">Presupuesto Total</h6>
                            <h3 class="mb-0 text-info"><?= formatMoney($presupuestoTotal) ?></h3>
                        </div>
                        <div class="ms-3">
                            <div class="bg-info rounded-circle p-3">
                                <i class="bi bi-currency-dollar text-white fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm border-start border-4 border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted mb-2">Total Conceptos</h6>
                            <h3 class="mb-0 text-warning"><?= number_format($totalConceptos) ?></h3>
                        </div>
                        <div class="ms-3">
                            <div class="bg-warning rounded-circle p-3">
                                <i class="bi bi-list-check text-white fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Estadísticas de usuarios por rol -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-danger rounded-circle p-3 mx-auto mb-3" style="width: fit-content;">
                        <i class="bi bi-shield-check text-white fs-4"></i>
                    </div>
                    <h4 class="text-danger"><?= number_format($usuariosAdmin) ?></h4>
                    <p class="text-muted mb-0">Administradores</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-warning rounded-circle p-3 mx-auto mb-3" style="width: fit-content;">
                        <i class="bi bi-person-gear text-white fs-4"></i>
                    </div>
                    <h4 class="text-warning"><?= number_format($usuariosAnalista) ?></h4>
                    <p class="text-muted mb-0">Analistas</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-info rounded-circle p-3 mx-auto mb-3" style="width: fit-content;">
                        <i class="bi bi-person text-white fs-4"></i>
                    </div>
                    <h4 class="text-info"><?= number_format($usuariosVisitante) ?></h4>
                    <p class="text-muted mb-0">Visitantes</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Acciones rápidas de administración -->
    <div class="row mb-4">
        <div class="col">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-gear text-primary"></i>
                        Acciones de Administración
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="/usuarios/index.php" class="btn btn-outline-primary w-100">
                                <i class="bi bi-people fs-4 d-block mb-2"></i>
                                Gestionar Usuarios
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="/logs/index.php" class="btn btn-outline-info w-100">
                                <i class="bi bi-activity fs-4 d-block mb-2"></i>
                                Logs de Auditoría
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="/backup/index.php" class="btn btn-outline-warning w-100">
                                <i class="bi bi-archive fs-4 d-block mb-2"></i>
                                Respaldos
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="/configuracion/index.php" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-gear-fill fs-4 d-block mb-2"></i>
                                Configuración
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Actividad reciente del sistema -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-activity text-info"></i>
                        Actividad Reciente del Sistema
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($actividadReciente)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-activity fs-1"></i>
                            <p class="mt-2">No hay actividad reciente</p>
                        </div>
                    <?php else: ?>
                        <div class="timeline">
                            <?php foreach ($actividadReciente as $log): ?>
                                <div class="d-flex mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="bg-primary rounded-circle p-2">
                                            <i class="bi bi-person text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="mb-1">
                                                <?php if ($log['nombre'] && $log['apellidos']): ?>
                                                    <?= escape($log['nombre'] . ' ' . $log['apellidos']) ?>
                                                <?php else: ?>
                                                    Sistema
                                                <?php endif; ?>
                                            </h6>
                                            <small class="text-muted">
                                                <?= formatDateTime($log['fecha_creacion']) ?>
                                            </small>
                                        </div>
                                        <p class="mb-1">
                                            <strong><?= escape($log['accion']) ?></strong>
                                            <?php if ($log['tabla_afectada']): ?>
                                                en <code><?= escape($log['tabla_afectada']) ?></code>
                                            <?php endif; ?>
                                        </p>
                                        <small class="text-muted">
                                            IP: <?= escape($log['ip_address']) ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Usuarios más activos -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-person-check text-success"></i>
                        Usuarios Más Activos
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($usuariosActivos)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-person fs-1"></i>
                            <p class="mt-2">No hay datos de usuarios</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($usuariosActivos as $usuario): ?>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0"><?= escape($usuario['nombre'] . ' ' . $usuario['apellidos']) ?></h6>
                                    <small class="text-muted">
                                        <span class="badge bg-<?= getRoleColor($usuario['rol']) ?> me-1">
                                            <?= getRoleName($usuario['rol']) ?>
                                        </span>
                                        <?= $usuario['ultimo_acceso'] ? formatDateTime($usuario['ultimo_acceso']) : 'Nunca' ?>
                                    </small>
                                </div>
                                <div class="ms-2">
                                    <span class="badge bg-primary">
                                        <?= number_format($usuario['total_acciones']) ?> acciones
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}
</style>

<?php
$content = ob_get_clean();

// Incluir layout
if ($isTestMode) {
    // Simple test layout
    echo $content;
} else {
    include SRC_PATH . '/views/layout.php';
}
?>