<?php
/**
 * Admin Dashboard - Users Management
 * Sistema Web de Análisis de Precios y Programa de Obra
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

// Require admin access
verificarAcceso(['admin']);

$pageTitle = 'Gestión de Usuarios - ' . APP_NAME;
$pageDescription = 'Panel de administración de usuarios';
$currentPage = 'usuarios';

$db = Database::getInstance();

// Get all users for display
$usuarios = $db->fetchAll("
    SELECT id, email, nombre, rol, activo, fecha_creacion, ultimo_acceso 
    FROM usuarios 
    ORDER BY fecha_creacion DESC
");

// Get user statistics
$stats = $db->fetch("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN activo = 1 THEN 1 ELSE 0 END) as activos,
        SUM(CASE WHEN rol = 'admin' THEN 1 ELSE 0 END) as admins,
        SUM(CASE WHEN rol = 'analista' THEN 1 ELSE 0 END) as analistas,
        SUM(CASE WHEN rol = 'visitante' THEN 1 ELSE 0 END) as visitantes
    FROM usuarios
");

include __DIR__ . '/../views/layout/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">
                <i class="bi bi-people"></i>
                Gestión de Usuarios
            </h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newUserModal">
                <i class="bi bi-person-plus"></i>
                Nuevo Usuario
            </button>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="dashboard-card text-center">
            <div class="card-icon">
                <i class="bi bi-people-fill"></i>
            </div>
            <h3><?php echo $stats['total']; ?></h3>
            <p>Total Usuarios</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="dashboard-card text-center">
            <div class="card-icon text-success">
                <i class="bi bi-person-check"></i>
            </div>
            <h3><?php echo $stats['activos']; ?></h3>
            <p>Usuarios Activos</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="dashboard-card text-center">
            <div class="card-icon text-warning">
                <i class="bi bi-shield-check"></i>
            </div>
            <h3><?php echo $stats['admins']; ?></h3>
            <p>Administradores</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="dashboard-card text-center">
            <div class="card-icon text-info">
                <i class="bi bi-graph-up"></i>
            </div>
            <h3><?php echo $stats['analistas']; ?></h3>
            <p>Analistas</p>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="dashboard-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Lista de Usuarios</h4>
        <div class="input-group" style="width: 300px;">
            <input type="text" class="form-control" placeholder="Buscar usuarios..." id="userSearch">
            <button class="btn btn-outline-secondary" type="button">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Registro</th>
                    <th>Último Acceso</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle me-2">
                                <?php echo strtoupper(substr($usuario['nombre'], 0, 1)); ?>
                            </div>
                            <strong><?php echo Security::escape($usuario['nombre']); ?></strong>
                        </div>
                    </td>
                    <td><?php echo Security::escape($usuario['email']); ?></td>
                    <td>
                        <span class="badge bg-<?php 
                            echo $usuario['rol'] === 'admin' ? 'danger' : 
                                ($usuario['rol'] === 'analista' ? 'warning' : 'info'); 
                        ?>">
                            <?php echo ucfirst($usuario['rol']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-<?php echo $usuario['activo'] ? 'success' : 'secondary'; ?>">
                            <?php echo $usuario['activo'] ? 'Activo' : 'Inactivo'; ?>
                        </span>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($usuario['fecha_creacion'])); ?></td>
                    <td>
                        <?php 
                        echo $usuario['ultimo_acceso'] 
                            ? date('d/m/Y H:i', strtotime($usuario['ultimo_acceso']))
                            : 'Nunca'; 
                        ?>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <?php if ($usuario['id'] != $_SESSION['user_id']): ?>
                            <button class="btn btn-outline-danger" title="Eliminar" 
                                    data-confirm="¿Está seguro de eliminar este usuario?">
                                <i class="bi bi-trash"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Recent Activity -->
<div class="row mt-4">
    <div class="col-12">
        <div class="dashboard-card">
            <h4>
                <i class="bi bi-clock-history"></i>
                Actividad Reciente
            </h4>
            <div id="recentActivity">
                <?php
                $recentActivity = $db->fetchAll("
                    SELECT la.*, u.nombre, u.email 
                    FROM log_actividad la
                    LEFT JOIN usuarios u ON la.usuario_id = u.id
                    ORDER BY la.fecha DESC
                    LIMIT 10
                ");
                ?>
                
                <?php if (empty($recentActivity)): ?>
                <p class="text-muted">No hay actividad reciente registrada.</p>
                <?php else: ?>
                <div class="timeline">
                    <?php foreach ($recentActivity as $activity): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <h6><?php echo Security::escape($activity['accion']); ?></h6>
                            <p class="mb-1">
                                <?php echo Security::escape($activity['descripcion'] ?? ''); ?>
                                <?php if ($activity['nombre']): ?>
                                    - <strong><?php echo Security::escape($activity['nombre']); ?></strong>
                                <?php endif; ?>
                            </p>
                            <small class="text-muted">
                                <?php echo date('d/m/Y H:i:s', strtotime($activity['fecha'])); ?>
                                <?php if ($activity['ip_address']): ?>
                                    - IP: <?php echo Security::escape($activity['ip_address']); ?>
                                <?php endif; ?>
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

<style>
.avatar-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 5px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--primary-color);
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -19px;
    top: 15px;
    width: 2px;
    height: calc(100% + 5px);
    background: #dee2e6;
}

.timeline-content h6 {
    margin-bottom: 5px;
    color: var(--primary-color);
}
</style>

<?php include __DIR__ . '/../views/layout/footer.php'; ?>