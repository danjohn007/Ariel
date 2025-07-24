<?php
/**
 * Listado de obras
 * Sistema de Análisis de Precios y Programa de Obra
 */

// Cargar configuración
require_once __DIR__ . '/../../config/config.php';
require_once SRC_PATH . '/includes/Database.php';
require_once SRC_PATH . '/includes/Auth.php';
require_once SRC_PATH . '/includes/functions.php';

// Inicializar autenticación
$auth = new Auth();
$auth->requireAuth();

$db = Database::getInstance();
$currentUser = $auth->getCurrentUser();

// Parámetros de paginación y filtros
$page = (int)($_GET['page'] ?? 1);
$limit = 10;
$offset = ($page - 1) * $limit;
$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';

// Construir consulta con filtros
$whereConditions = [];
$params = [];

if (!empty($search)) {
    $whereConditions[] = "(o.nombre LIKE ? OR o.cliente LIKE ? OR o.ubicacion LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($status)) {
    $whereConditions[] = "o.estado = ?";
    $params[] = $status;
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

try {
    // Obtener total de registros para paginación
    $totalSql = "SELECT COUNT(*) FROM obras o $whereClause";
    $totalRecords = $db->count($totalSql, $params);
    
    // Obtener obras con paginación
    $sql = "
        SELECT o.*, u.nombre as responsable_nombre, u.apellidos as responsable_apellidos
        FROM obras o 
        LEFT JOIN usuarios u ON o.usuario_responsable_id = u.id 
        $whereClause
        ORDER BY o.fecha_creacion DESC 
        LIMIT $limit OFFSET $offset
    ";
    
    $obras = $db->fetchAll($sql, $params);
    
    // Obtener estadísticas rápidas
    $estadisticas = [
        'total' => $db->count("SELECT COUNT(*) FROM obras WHERE estado != 'cancelado'"),
        'activas' => $db->count("SELECT COUNT(*) FROM obras WHERE estado = 'activo'"),
        'pausadas' => $db->count("SELECT COUNT(*) FROM obras WHERE estado = 'pausado'"),
        'completadas' => $db->count("SELECT COUNT(*) FROM obras WHERE estado = 'completado'")
    ];
    
} catch (Exception $e) {
    $error = "Error al cargar las obras: " . $e->getMessage();
}

$pageTitle = 'Gestión de Obras';
$breadcrumbs = [
    ['text' => 'Dashboard', 'url' => '/dashboard.php'],
    ['text' => 'Obras']
];

// Capturar contenido
ob_start();
?>

<div class="container-fluid">
    <!-- Header con estadísticas -->
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="bi bi-building text-primary"></i>
                        Gestión de Obras
                    </h1>
                    <p class="text-muted">Administración de proyectos de construcción</p>
                </div>
                <?php if ($auth->canWrite()): ?>
                    <div>
                        <a href="/obras/create.php" class="btn btn-primary">
                            <i class="bi bi-plus"></i>
                            Nueva Obra
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Estadísticas rápidas -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="bi bi-building fs-1"></i>
                    </div>
                    <h4 class="mb-1"><?= number_format($estadisticas['total']) ?></h4>
                    <small class="text-muted">Total de Obras</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="bi bi-play-circle fs-1"></i>
                    </div>
                    <h4 class="mb-1 text-success"><?= number_format($estadisticas['activas']) ?></h4>
                    <small class="text-muted">Obras Activas</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="bi bi-pause-circle fs-1"></i>
                    </div>
                    <h4 class="mb-1 text-warning"><?= number_format($estadisticas['pausadas']) ?></h4>
                    <small class="text-muted">Obras Pausadas</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="bi bi-check-circle fs-1"></i>
                    </div>
                    <h4 class="mb-1 text-info"><?= number_format($estadisticas['completadas']) ?></h4>
                    <small class="text-muted">Completadas</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filtros y búsqueda -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" 
                           class="form-control" 
                           id="search" 
                           name="search" 
                           value="<?= escape($search) ?>"
                           placeholder="Buscar por nombre, cliente o ubicación...">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Estado</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Todos los estados</option>
                        <option value="activo" <?= $status === 'activo' ? 'selected' : '' ?>>Activo</option>
                        <option value="pausado" <?= $status === 'pausado' ? 'selected' : '' ?>>Pausado</option>
                        <option value="completado" <?= $status === 'completado' ? 'selected' : '' ?>>Completado</option>
                        <option value="cancelado" <?= $status === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary me-2">
                        <i class="bi bi-search"></i>
                        Buscar
                    </button>
                    <a href="/obras/index.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i>
                        Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Lista de obras -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-list-ul"></i>
                    Lista de Obras
                    <?php if ($totalRecords > 0): ?>
                        <span class="badge bg-primary ms-2"><?= number_format($totalRecords) ?></span>
                    <?php endif; ?>
                </h5>
                
                <?php if (!empty($search) || !empty($status)): ?>
                    <small class="text-muted">
                        <?= $totalRecords ?> resultado(s) encontrado(s)
                    </small>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <?= escape($error) ?>
                </div>
            <?php elseif (empty($obras)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-building fs-1 text-muted"></i>
                    <h5 class="text-muted mt-3">No se encontraron obras</h5>
                    <p class="text-muted">
                        <?php if (!empty($search) || !empty($status)): ?>
                            Intenta ajustar los filtros de búsqueda
                        <?php else: ?>
                            <?php if ($auth->canWrite()): ?>
                                Comienza creando tu primera obra
                            <?php else: ?>
                                No hay obras disponibles para mostrar
                            <?php endif; ?>
                        <?php endif; ?>
                    </p>
                    <?php if ($auth->canWrite() && empty($search) && empty($status)): ?>
                        <a href="/obras/create.php" class="btn btn-primary">
                            <i class="bi bi-plus"></i>
                            Crear Primera Obra
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Obra</th>
                                <th>Cliente</th>
                                <th>Ubicación</th>
                                <th>Responsable</th>
                                <th>Presupuesto</th>
                                <th>Avance</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($obras as $obra): ?>
                                <tr>
                                    <td>
                                        <div>
                                            <strong><?= escape($obra['nombre']) ?></strong>
                                            <?php if ($obra['descripcion']): ?>
                                                <br><small class="text-muted"><?= escape(truncate($obra['descripcion'], 50)) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td><?= escape($obra['cliente']) ?></td>
                                    <td>
                                        <small><?= escape($obra['ubicacion']) ?></small>
                                    </td>
                                    <td>
                                        <?php if ($obra['responsable_nombre']): ?>
                                            <?= escape($obra['responsable_nombre'] . ' ' . $obra['responsable_apellidos']) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Sin asignar</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= formatMoney($obra['presupuesto_actual'] ?: $obra['presupuesto_inicial']) ?></strong>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress me-2" style="width: 60px; height: 8px;">
                                                <div class="progress-bar bg-success" 
                                                     style="width: <?= $obra['avance_fisico'] ?>%"></div>
                                            </div>
                                            <small><?= formatPercent($obra['avance_fisico']) ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= getProjectStatusColor($obra['estado']) ?>">
                                            <?= getProjectStatusName($obra['estado']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="/obras/view.php?id=<?= $obra['id'] ?>" 
                                               class="btn btn-outline-primary"
                                               title="Ver detalles">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if ($auth->canWrite()): ?>
                                                <a href="/obras/edit.php?id=<?= $obra['id'] ?>" 
                                                   class="btn btn-outline-warning"
                                                   title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($auth->isAdmin()): ?>
                                                <a href="/obras/delete.php?id=<?= $obra['id'] ?>" 
                                                   class="btn btn-outline-danger"
                                                   title="Eliminar"
                                                   data-confirm-delete="¿Está seguro de que desea eliminar esta obra?">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <?php if ($totalRecords > $limit): ?>
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div>
                            <small class="text-muted">
                                Mostrando <?= number_format(min($offset + 1, $totalRecords)) ?> 
                                a <?= number_format(min($offset + $limit, $totalRecords)) ?> 
                                de <?= number_format($totalRecords) ?> obras
                            </small>
                        </div>
                        <div>
                            <?php
                            $baseUrl = '/obras/index.php';
                            $queryParams = [];
                            if ($search) $queryParams['search'] = $search;
                            if ($status) $queryParams['status'] = $status;
                            if (!empty($queryParams)) {
                                $baseUrl .= '?' . http_build_query($queryParams);
                            }
                            echo paginate($totalRecords, $limit, $page, $baseUrl);
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

// Incluir layout
include SRC_PATH . '/views/layout.php';
?>