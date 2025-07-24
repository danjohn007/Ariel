<?php
/**
 * Dashboard de Visitante
 * Sistema de Análisis de Precios y Programa de Obra
 */

// Cargar configuración
require_once __DIR__ . '/../../config/config.php';
require_once SRC_PATH . '/includes/Database.php';
require_once SRC_PATH . '/includes/Auth.php';
require_once SRC_PATH . '/includes/functions.php';

// Inicializar autenticación y verificar rol de visitante
$auth = new Auth();
$auth->requireRole(ROLE_VISITOR);

$db = Database::getInstance();
$currentUser = $auth->getCurrentUser();

// Obtener datos de solo lectura para visitantes
try {
    // Estadísticas generales (solo lectura)
    $totalObras = $db->count("SELECT COUNT(*) FROM obras WHERE estado != 'cancelado'");
    $obrasActivas = $db->count("SELECT COUNT(*) FROM obras WHERE estado = 'activo'");
    $obrasCompletadas = $db->count("SELECT COUNT(*) FROM obras WHERE estado = 'completado'");
    
    // Presupuesto total
    $presupuestoTotal = $db->fetchOne("SELECT SUM(presupuesto_actual) as total FROM obras WHERE estado != 'cancelado'")['total'] ?? 0;
    
    // Avance promedio
    $avancePromedio = $db->fetchOne("SELECT AVG(avance_fisico) as promedio FROM obras WHERE estado = 'activo'")['promedio'] ?? 0;
    
    // Obras públicas (para vista de visitante)
    $obrasPublicas = $db->fetchAll("
        SELECT o.*, 
               COUNT(c.id) as total_conceptos,
               SUM(c.importe) as presupuesto_conceptos
        FROM obras o 
        LEFT JOIN conceptos c ON o.id = c.obra_id AND c.activo = 1
        WHERE o.estado IN ('activo', 'completado')
        GROUP BY o.id 
        ORDER BY o.fecha_creacion DESC 
        LIMIT 6
    ");
    
    // Estadísticas por estado de obras
    $estadisticasEstado = $db->fetchAll("
        SELECT estado, COUNT(*) as cantidad,
               SUM(presupuesto_actual) as presupuesto_total
        FROM obras 
        WHERE estado != 'cancelado'
        GROUP BY estado
    ");
    
    // Reportes de avance recientes
    $reportesRecientes = $db->fetchAll("
        SELECT r.*, o.nombre as obra_nombre
        FROM reportes_avance r 
        INNER JOIN obras o ON r.obra_id = o.id 
        ORDER BY r.fecha_reporte DESC 
        LIMIT 5
    ");
    
    // Top conceptos por importe
    $topConceptos = $db->fetchAll("
        SELECT c.nombre, c.importe, c.unidad, c.cantidad, 
               o.nombre as obra_nombre
        FROM conceptos c 
        INNER JOIN obras o ON c.obra_id = o.id 
        WHERE c.activo = 1 AND o.estado != 'cancelado'
        ORDER BY c.importe DESC 
        LIMIT 5
    ");
    
} catch (Exception $e) {
    $error = "Error al cargar el dashboard: " . $e->getMessage();
}

$pageTitle = 'Dashboard Visitante';
$breadcrumbs = [
    ['text' => 'Consulta'],
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
                        <i class="bi bi-eye text-info"></i>
                        Panel de Consulta
                    </h1>
                    <p class="text-muted">
                        Bienvenido, <?= escape($currentUser['name']) ?> - Vista de consulta del sistema
                    </p>
                </div>
                <div class="text-end">
                    <span class="badge bg-info fs-6">
                        <?= getRoleName($currentUser['role']) ?>
                    </span>
                    <small class="d-block text-muted">Solo lectura</small>
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
                            <h6 class="card-title text-muted mb-2">Total de Obras</h6>
                            <h3 class="mb-0 text-primary"><?= number_format($totalObras) ?></h3>
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
            <div class="card border-0 shadow-sm border-start border-4 border-success">
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
    
    <!-- Estadísticas por estado -->
    <div class="row mb-4">
        <div class="col">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart text-primary"></i>
                        Distribución de Obras por Estado
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($estadisticasEstado)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-bar-chart fs-1"></i>
                            <p class="mt-2">No hay datos estadísticos disponibles</p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($estadisticasEstado as $stat): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-<?= getProjectStatusColor($stat['estado']) ?> text-white">
                                        <div class="card-body text-center">
                                            <h3 class="mb-1"><?= number_format($stat['cantidad']) ?></h3>
                                            <p class="mb-1"><?= getProjectStatusName($stat['estado']) ?></p>
                                            <small><?= formatMoney($stat['presupuesto_total']) ?></small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Herramientas de consulta -->
    <div class="row mb-4">
        <div class="col">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-search text-primary"></i>
                        Herramientas de Consulta
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="/obras/index.php" class="btn btn-outline-primary w-100">
                                <i class="bi bi-building fs-4 d-block mb-2"></i>
                                Consultar Obras
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="/reportes/index.php" class="btn btn-outline-success w-100">
                                <i class="bi bi-graph-up fs-4 d-block mb-2"></i>
                                Ver Reportes
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="/conceptos/index.php" class="btn btn-outline-info w-100">
                                <i class="bi bi-list-check fs-4 d-block mb-2"></i>
                                Ver Conceptos
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="/programa/index.php" class="btn btn-outline-warning w-100">
                                <i class="bi bi-calendar3 fs-4 d-block mb-2"></i>
                                Programa de Obra
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Obras disponibles para consulta -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-building text-success"></i>
                            Obras Disponibles
                        </h5>
                        <a href="/obras/index.php" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-eye"></i> Ver todas
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($obrasPublicas)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-building fs-1"></i>
                            <p class="mt-2">No hay obras disponibles para consulta</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Obra</th>
                                        <th>Cliente</th>
                                        <th>Estado</th>
                                        <th>Conceptos</th>
                                        <th>Presupuesto</th>
                                        <th>Avance</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($obrasPublicas as $obra): ?>
                                        <tr>
                                            <td>
                                                <strong><?= escape(truncate($obra['nombre'], 30)) ?></strong><br>
                                                <small class="text-muted"><?= escape($obra['ubicacion']) ?></small>
                                            </td>
                                            <td><?= escape($obra['cliente']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= getProjectStatusColor($obra['estado']) ?>">
                                                    <?= getProjectStatusName($obra['estado']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    <?= number_format($obra['total_conceptos']) ?>
                                                </span>
                                            </td>
                                            <td><?= formatMoney($obra['presupuesto_conceptos']) ?></td>
                                            <td>
                                                <div class="progress" style="height: 15px;">
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
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Ver detalles">
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
        
        <!-- Top conceptos por importe -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-trophy text-warning"></i>
                        Top Conceptos por Importe
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($topConceptos)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-list-check fs-1"></i>
                            <p class="mt-2">No hay conceptos registrados</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($topConceptos as $index => $concepto): ?>
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <div class="bg-<?= $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'info') ?> rounded-circle d-flex align-items-center justify-content-center text-white" 
                                         style="width: 30px; height: 30px; font-size: 12px; font-weight: bold;">
                                        <?= $index + 1 ?>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0"><?= escape(truncate($concepto['nombre'], 25)) ?></h6>
                                    <small class="text-muted">
                                        <?= escape(truncate($concepto['obra_nombre'], 20)) ?>
                                    </small>
                                    <div class="mt-1">
                                        <strong class="text-success"><?= formatMoney($concepto['importe']) ?></strong>
                                        <small class="text-muted ms-2">
                                            <?= formatNumber($concepto['cantidad']) ?> <?= escape($concepto['unidad']) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Reportes de avance recientes -->
    <?php if (!empty($reportesRecientes)): ?>
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0">
                            <i class="bi bi-file-earmark-bar-graph text-info"></i>
                            Reportes de Avance Recientes
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Obra</th>
                                        <th>Fecha Reporte</th>
                                        <th>Período</th>
                                        <th>Avance Físico</th>
                                        <th>Avance Financiero</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reportesRecientes as $reporte): ?>
                                        <tr>
                                            <td><?= escape($reporte['obra_nombre']) ?></td>
                                            <td><?= formatDate($reporte['fecha_reporte']) ?></td>
                                            <td><?= escape($reporte['periodo_reportado']) ?></td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    <?= formatPercent($reporte['avance_fisico_total']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <?= formatPercent($reporte['avance_financiero_total']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="/reportes/view.php?id=<?= $reporte['id'] ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> Ver
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Nota informativa para visitantes -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="bi bi-info-circle"></i>
        <strong>Modo de solo lectura.</strong> Como visitante, puedes consultar información pero no realizar modificaciones.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>

<?php
$content = ob_get_clean();

// Incluir layout
include SRC_PATH . '/views/layout.php';
?>