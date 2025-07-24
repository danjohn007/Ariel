<?php
/**
 * Dashboard de Analista
 * Sistema de Análisis de Precios y Programa de Obra
 */

// Cargar configuración
require_once __DIR__ . '/../../config/config.php';
require_once SRC_PATH . '/includes/Database.php';
require_once SRC_PATH . '/includes/Auth.php';
require_once SRC_PATH . '/includes/functions.php';

// Inicializar autenticación y verificar rol de analista
$auth = new Auth();
$auth->requireRole(ROLE_ANALYST);

$db = Database::getInstance();
$currentUser = $auth->getCurrentUser();

// Obtener estadísticas específicas para analistas
try {
    // Obras donde el usuario es responsable o puede trabajar
    $obrasAsignadas = $db->fetchAll("
        SELECT o.*, 
               COUNT(c.id) as total_conceptos,
               SUM(c.importe) as presupuesto_conceptos
        FROM obras o 
        LEFT JOIN conceptos c ON o.id = c.obra_id AND c.activo = 1
        WHERE o.estado = 'activo' 
        GROUP BY o.id 
        ORDER BY o.fecha_creacion DESC 
        LIMIT 5
    ");
    
    // Conceptos recientes donde puede trabajar
    $conceptosRecientes = $db->fetchAll("
        SELECT c.*, o.nombre as obra_nombre,
               CASE 
                   WHEN c.cantidad > 0 THEN (c.avance_cantidad / c.cantidad) * 100 
                   ELSE 0 
               END as porcentaje_avance
        FROM conceptos c 
        INNER JOIN obras o ON c.obra_id = o.id 
        WHERE c.activo = 1 AND o.estado = 'activo'
        ORDER BY c.fecha_actualizacion DESC 
        LIMIT 8
    ");
    
    // Estadísticas de trabajo
    $totalObrasActivas = $db->count("SELECT COUNT(*) FROM obras WHERE estado = 'activo'");
    $totalConceptos = $db->count("SELECT COUNT(*) FROM conceptos WHERE activo = 1");
    $totalMateriales = $db->count("SELECT COUNT(*) FROM materiales WHERE activo = 1");
    $totalProveedores = $db->count("SELECT COUNT(*) FROM proveedores WHERE activo = 1");
    
    // Análisis de precios pendientes (conceptos sin análisis completo)
    $analisisPendientes = $db->fetchAll("
        SELECT c.*, o.nombre as obra_nombre,
               COUNT(ap.id) as total_analisis
        FROM conceptos c 
        INNER JOIN obras o ON c.obra_id = o.id 
        LEFT JOIN analisis_precios ap ON c.id = ap.concepto_id
        WHERE c.activo = 1 AND o.estado = 'activo'
        GROUP BY c.id 
        HAVING total_analisis < 3
        ORDER BY c.fecha_creacion DESC 
        LIMIT 5
    ");
    
    // Materiales con precios desactualizados (más de 30 días)
    $materialesDesactualizados = $db->fetchAll("
        SELECT m.*, 
               DATEDIFF(NOW(), m.fecha_actualizacion) as dias_sin_actualizar
        FROM materiales m 
        WHERE m.activo = 1 
        AND DATEDIFF(NOW(), m.fecha_actualizacion) > 30
        ORDER BY dias_sin_actualizar DESC 
        LIMIT 5
    ");
    
    // Presupuesto promedio de obras activas
    $presupuestoPromedio = $db->fetchOne("
        SELECT AVG(presupuesto_actual) as promedio 
        FROM obras 
        WHERE estado = 'activo' AND presupuesto_actual > 0
    ")['promedio'] ?? 0;
    
} catch (Exception $e) {
    $error = "Error al cargar el dashboard: " . $e->getMessage();
}

$pageTitle = 'Dashboard Analista';
$breadcrumbs = [
    ['text' => 'Análisis'],
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
                        <i class="bi bi-calculator text-warning"></i>
                        Panel de Analista
                    </h1>
                    <p class="text-muted">
                        Bienvenido, <?= escape($currentUser['name']) ?> - Análisis de precios y gestión de conceptos
                    </p>
                </div>
                <div class="text-end">
                    <span class="badge bg-warning fs-6">
                        <?= getRoleName($currentUser['role']) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tarjetas de estadísticas principales -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm border-start border-4 border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted mb-2">Obras Activas</h6>
                            <h3 class="mb-0 text-success"><?= number_format($totalObrasActivas) ?></h3>
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
            <div class="card border-0 shadow-sm border-start border-4 border-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted mb-2">Total Conceptos</h6>
                            <h3 class="mb-0 text-primary"><?= number_format($totalConceptos) ?></h3>
                        </div>
                        <div class="ms-3">
                            <div class="bg-primary rounded-circle p-3">
                                <i class="bi bi-list-check text-white fs-4"></i>
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
                            <h6 class="card-title text-muted mb-2">Materiales</h6>
                            <h3 class="mb-0 text-info"><?= number_format($totalMateriales) ?></h3>
                        </div>
                        <div class="ms-3">
                            <div class="bg-info rounded-circle p-3">
                                <i class="bi bi-box text-white fs-4"></i>
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
                            <h6 class="card-title text-muted mb-2">Presup. Promedio</h6>
                            <h3 class="mb-0 text-warning"><?= formatMoney($presupuestoPromedio) ?></h3>
                        </div>
                        <div class="ms-3">
                            <div class="bg-warning rounded-circle p-3">
                                <i class="bi bi-currency-dollar text-white fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Acciones rápidas para analistas -->
    <div class="row mb-4">
        <div class="col">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-tools text-primary"></i>
                        Herramientas de Análisis
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="/conceptos/create.php" class="btn btn-outline-primary w-100">
                                <i class="bi bi-plus-circle fs-4 d-block mb-2"></i>
                                Nuevo Concepto
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="/analisis/create.php" class="btn btn-outline-success w-100">
                                <i class="bi bi-calculator fs-4 d-block mb-2"></i>
                                Análisis de Precios
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="/materiales/index.php" class="btn btn-outline-info w-100">
                                <i class="bi bi-box fs-4 d-block mb-2"></i>
                                Catálogo Materiales
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="/cotizaciones/index.php" class="btn btn-outline-warning w-100">
                                <i class="bi bi-file-earmark-text fs-4 d-block mb-2"></i>
                                Cotizaciones
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Obras asignadas -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-building text-success"></i>
                            Obras Activas
                        </h5>
                        <a href="/obras/index.php" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-eye"></i> Ver todas
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($obrasAsignadas)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-building fs-1"></i>
                            <p class="mt-2">No hay obras activas</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($obrasAsignadas as $obra): ?>
                            <div class="d-flex justify-content-between align-items-center mb-3 p-3 border rounded">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?= escape($obra['nombre']) ?></h6>
                                    <small class="text-muted d-block"><?= escape($obra['cliente']) ?></small>
                                    <div class="mt-2">
                                        <span class="badge bg-primary me-1"><?= number_format($obra['total_conceptos']) ?> conceptos</span>
                                        <span class="badge bg-success"><?= formatMoney($obra['presupuesto_conceptos']) ?></span>
                                    </div>
                                </div>
                                <div class="ms-2">
                                    <a href="/obras/view.php?id=<?= $obra['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Conceptos recientes -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-list-check text-primary"></i>
                            Conceptos Recientes
                        </h5>
                        <a href="/conceptos/index.php" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-list"></i> Ver todos
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($conceptosRecientes)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-list-check fs-1"></i>
                            <p class="mt-2">No hay conceptos registrados</p>
                        </div>
                    <?php else: ?>
                        <?php foreach (array_slice($conceptosRecientes, 0, 5) as $concepto): ?>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0"><?= escape(truncate($concepto['nombre'], 35)) ?></h6>
                                    <small class="text-muted"><?= escape($concepto['obra_nombre']) ?></small>
                                    <div class="progress mt-1" style="height: 6px;">
                                        <div class="progress-bar bg-primary" 
                                             style="width: <?= $concepto['porcentaje_avance'] ?>%"></div>
                                    </div>
                                </div>
                                <div class="ms-2">
                                    <span class="badge bg-primary">
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
    
    <!-- Análisis pendientes y materiales desactualizados -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-triangle text-warning"></i>
                        Análisis Pendientes
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($analisisPendientes)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-check-circle fs-1 text-success"></i>
                            <p class="mt-2">Todos los conceptos tienen análisis completo</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($analisisPendientes as $concepto): ?>
                            <div class="d-flex justify-content-between align-items-center mb-3 p-3 border border-warning rounded">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?= escape(truncate($concepto['nombre'], 40)) ?></h6>
                                    <small class="text-muted"><?= escape($concepto['obra_nombre']) ?></small>
                                    <div class="mt-1">
                                        <span class="badge bg-warning">
                                            <?= number_format($concepto['total_analisis']) ?> análisis
                                        </span>
                                    </div>
                                </div>
                                <div class="ms-2">
                                    <a href="/analisis/create.php?concepto_id=<?= $concepto['id'] ?>" 
                                       class="btn btn-sm btn-warning">
                                        <i class="bi bi-plus"></i> Analizar
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-clock text-danger"></i>
                        Materiales Desactualizados
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($materialesDesactualizados)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-check-circle fs-1 text-success"></i>
                            <p class="mt-2">Todos los materiales están actualizados</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($materialesDesactualizados as $material): ?>
                            <div class="d-flex justify-content-between align-items-center mb-3 p-3 border border-danger rounded">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?= escape($material['nombre']) ?></h6>
                                    <small class="text-muted">
                                        Código: <?= escape($material['codigo']) ?> | 
                                        Precio: <?= formatMoney($material['precio_unitario']) ?>
                                    </small>
                                    <div class="mt-1">
                                        <span class="badge bg-danger">
                                            <?= number_format($material['dias_sin_actualizar']) ?> días
                                        </span>
                                    </div>
                                </div>
                                <div class="ms-2">
                                    <a href="/materiales/edit.php?id=<?= $material['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-pencil"></i> Actualizar
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

// Incluir layout
include SRC_PATH . '/views/layout.php';
?>