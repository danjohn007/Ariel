<?php
/**
 * Dashboard principal
 * Sistema de Análisis de Precios y Programa de Obra
 */

// Cargar configuración
require_once __DIR__ . '/../config/config.php';
require_once SRC_PATH . '/includes/Database.php';
require_once SRC_PATH . '/includes/Auth.php';
require_once SRC_PATH . '/includes/functions.php';

// Inicializar autenticación
$auth = new Auth();
$auth->requireAuth();

$db = Database::getInstance();
$currentUser = $auth->getCurrentUser();

// Obtener estadísticas generales
try {
    // Obras
    $totalObras = $db->count("SELECT COUNT(*) FROM obras WHERE estado != 'cancelado'");
    $obrasActivas = $db->count("SELECT COUNT(*) FROM obras WHERE estado = 'activo'");
    $obrasCompletadas = $db->count("SELECT COUNT(*) FROM obras WHERE estado = 'completado'");
    
    // Conceptos
    $totalConceptos = $db->count("SELECT COUNT(*) FROM conceptos WHERE activo = 1");
    
    // Presupuesto total
    $presupuestoTotal = $db->fetchOne("SELECT SUM(presupuesto_actual) as total FROM obras WHERE estado != 'cancelado'")['total'] ?? 0;
    
    // Avance promedio
    $avancePromedio = $db->fetchOne("SELECT AVG(avance_fisico) as promedio FROM obras WHERE estado = 'activo'")['promedio'] ?? 0;
    
    // Obras recientes (últimas 5)
    $obrasRecientes = $db->fetchAll("
        SELECT o.*, u.nombre, u.apellidos 
        FROM obras o 
        LEFT JOIN usuarios u ON o.usuario_responsable_id = u.id 
        ORDER BY o.fecha_creacion DESC 
        LIMIT 5
    ");
    
    // Conceptos con mayor avance (top 5)
    $conceptosAvance = $db->fetchAll("
        SELECT c.*, o.nombre as obra_nombre,
               CASE 
                   WHEN c.cantidad > 0 THEN (c.avance_cantidad / c.cantidad) * 100 
                   ELSE 0 
               END as porcentaje_avance
        FROM conceptos c 
        INNER JOIN obras o ON c.obra_id = o.id 
        WHERE c.activo = 1 AND o.estado = 'activo'
        ORDER BY porcentaje_avance DESC 
        LIMIT 5
    ");
    
    // Actividad reciente (logs)
    if ($auth->isAdmin()) {
        $actividadReciente = $db->fetchAll("
            SELECT l.*, u.nombre, u.apellidos 
            FROM logs_auditoria l 
            LEFT JOIN usuarios u ON l.usuario_id = u.id 
            ORDER BY l.fecha_creacion DESC 
            LIMIT 10
        ");
    }
    
} catch (Exception $e) {
    $error = "Error al cargar el dashboard: " . $e->getMessage();
}

$pageTitle = 'Dashboard';
$breadcrumbs = [
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
                        <i class="bi bi-speedometer2 text-primary"></i>
                        Bienvenido, <?= escape($currentUser['name']) ?>
                    </h1>
                    <p class="text-muted">
                        Dashboard del Sistema de Análisis de Precios y Programa de Obra
                    </p>
                </div>
                <div class="text-end">
                    <span class="badge bg-<?= getRoleColor($currentUser['role']) ?> fs-6">
                        <?= getRoleName($currentUser['role']) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tarjetas de estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted mb-2">Total de Obras</h6>
                            <h3 class="mb-0"><?= number_format($totalObras) ?></h3>
                        </div>
                        <div class="ms-3">
                            <div class="bg-primary rounded-circle p-3">
                                <i class="bi bi-building text-white fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted mb-2">Obras Activas</h6>
                            <h3 class="mb-0 text-success"><?= number_format($obrasActivas) ?></h3>
                        </div>
                        <div class="ms-3">
                            <div class="bg-success rounded-circle p-3">
                                <i class="bi bi-play-circle text-white fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
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
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted mb-2">Avance Promedio</h6>
                            <h3 class="mb-0 text-warning"><?= formatPercent($avancePromedio) ?></h3>
                        </div>
                        <div class="ms-3">
                            <div class="bg-warning rounded-circle p-3">
                                <i class="bi bi-graph-up text-white fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Obras Recientes -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-building text-primary"></i>
                            Obras Recientes
                        </h5>
                        <?php if ($auth->canWrite()): ?>
                            <a href="/obras/create.php" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-plus"></i> Nueva Obra
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($obrasRecientes)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-building fs-1"></i>
                            <p class="mt-2">No hay obras registradas</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Obra</th>
                                        <th>Cliente</th>
                                        <th>Responsable</th>
                                        <th>Estado</th>
                                        <th>Avance</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($obrasRecientes as $obra): ?>
                                        <tr>
                                            <td>
                                                <strong><?= escape($obra['nombre']) ?></strong><br>
                                                <small class="text-muted"><?= escape($obra['ubicacion']) ?></small>
                                            </td>
                                            <td><?= escape($obra['cliente']) ?></td>
                                            <td>
                                                <?php if ($obra['nombre'] && $obra['apellidos']): ?>
                                                    <?= escape($obra['nombre'] . ' ' . $obra['apellidos']) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Sin asignar</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= getProjectStatusColor($obra['estado']) ?>">
                                                    <?= getProjectStatusName($obra['estado']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar" 
                                                         role="progressbar" 
                                                         style="width: <?= $obra['avance_fisico'] ?>%"
                                                         aria-valuenow="<?= $obra['avance_fisico'] ?>" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        <?= formatPercent($obra['avance_fisico']) ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="/obras/view.php?id=<?= $obra['id'] ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Conceptos con Mayor Avance -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-list-check text-success"></i>
                        Conceptos - Top Avance
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($conceptosAvance)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-list-check fs-1"></i>
                            <p class="mt-2">No hay conceptos</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($conceptosAvance as $concepto): ?>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0"><?= escape(truncate($concepto['nombre'], 30)) ?></h6>
                                    <small class="text-muted"><?= escape($concepto['obra_nombre']) ?></small>
                                    <div class="progress mt-1" style="height: 8px;">
                                        <div class="progress-bar bg-success" 
                                             style="width: <?= $concepto['porcentaje_avance'] ?>%"></div>
                                    </div>
                                </div>
                                <div class="ms-2">
                                    <span class="badge bg-success">
                                        <?= formatPercent($concepto['porcentaje_avance']) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Actividad Reciente (solo para administradores) -->
    <?php if ($auth->isAdmin() && isset($actividadReciente)): ?>
        <div class="row">
            <div class="col-12">
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
                                                        Usuario del sistema
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
        </div>
    <?php endif; ?>
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
include SRC_PATH . '/views/layout.php';
?>