<?php
/**
 * Analyst Dashboard - Progress Management
 * Sistema Web de Análisis de Precios y Programa de Obra
 */

require_once __DIR__ . '/../includes/auth.php';

// Require analyst or admin access
verificarAcceso(['analista', 'admin']);

$pageTitle = 'Avance de Obras - ' . APP_NAME;
$pageDescription = 'Panel de gestión de avance de obras';
$currentPage = 'avance';

include __DIR__ . '/../views/layout/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">
                <i class="bi bi-graph-up"></i>
                Avance de Obras
            </h1>
            <div class="btn-group">
                <button class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i>
                    Nueva Obra
                </button>
                <button class="btn btn-outline-primary">
                    <i class="bi bi-upload"></i>
                    Importar Datos
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="dashboard-card text-center">
            <div class="card-icon">
                <i class="bi bi-building"></i>
            </div>
            <h3>15</h3>
            <p>Obras Activas</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="dashboard-card text-center">
            <div class="card-icon text-success">
                <i class="bi bi-check-circle"></i>
            </div>
            <h3>8</h3>
            <p>Completadas</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="dashboard-card text-center">
            <div class="card-icon text-warning">
                <i class="bi bi-clock"></i>
            </div>
            <h3>4</h3>
            <p>En Progreso</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="dashboard-card text-center">
            <div class="card-icon text-danger">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <h3>3</h3>
            <p>Con Retraso</p>
        </div>
    </div>
</div>

<!-- Progress Overview -->
<div class="row">
    <div class="col-lg-8">
        <div class="dashboard-card">
            <h4>
                <i class="bi bi-bar-chart"></i>
                Progreso General
            </h4>
            <div class="progress-overview">
                <div class="overall-progress mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Progreso Total del Portfolio</span>
                        <span class="fw-bold">68%</span>
                    </div>
                    <div class="progress progress-lg">
                        <div class="progress-bar bg-success" style="width: 68%"></div>
                    </div>
                </div>
                
                <div class="chart-placeholder bg-light rounded d-flex align-items-center justify-content-center" style="height: 300px;">
                    <div class="text-center text-muted">
                        <i class="bi bi-graph-up display-1"></i>
                        <p class="mt-2">Gráfico de progreso por obra</p>
                        <small>Se implementará con Chart.js</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="dashboard-card">
            <h4>
                <i class="bi bi-calendar-event"></i>
                Hitos Próximos
            </h4>
            <div class="upcoming-milestones">
                <div class="milestone-item">
                    <div class="milestone-date">
                        <span class="day">25</span>
                        <span class="month">JUL</span>
                    </div>
                    <div class="milestone-info">
                        <h6>Entrega Fase 1</h6>
                        <p class="text-muted mb-0">Obra Residencial Norte</p>
                    </div>
                </div>
                
                <div class="milestone-item">
                    <div class="milestone-date bg-warning">
                        <span class="day">28</span>
                        <span class="month">JUL</span>
                    </div>
                    <div class="milestone-info">
                        <h6>Revisión Estructural</h6>
                        <p class="text-muted mb-0">Centro Comercial Sur</p>
                    </div>
                </div>
                
                <div class="milestone-item">
                    <div class="milestone-date bg-danger">
                        <span class="day">02</span>
                        <span class="month">AGO</span>
                    </div>
                    <div class="milestone-info">
                        <h6>Inspección Final</h6>
                        <p class="text-muted mb-0">Proyecto Industrial</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Works List -->
<div class="row mt-4">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>
                    <i class="bi bi-list-ul"></i>
                    Obras en Seguimiento
                </h4>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" style="width: auto;">
                        <option>Todas las obras</option>
                        <option>En progreso</option>
                        <option>Con retraso</option>
                        <option>Completadas</option>
                    </select>
                    <button class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-funnel"></i>
                        Filtros
                    </button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Obra</th>
                            <th>Cliente</th>
                            <th>Avance</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <strong>Residencial Norte</strong><br>
                                <small class="text-muted">RES-2024-001</small>
                            </td>
                            <td>Constructora ABC</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress me-2" style="width: 80px; height: 8px;">
                                        <div class="progress-bar bg-success" style="width: 75%"></div>
                                    </div>
                                    <span class="small">75%</span>
                                </div>
                            </td>
                            <td>15/01/2024</td>
                            <td>30/08/2024</td>
                            <td><span class="badge bg-success">En Tiempo</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Ver Detalles">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-info" title="Reportes">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>
                                <strong>Centro Comercial Sur</strong><br>
                                <small class="text-muted">COM-2024-002</small>
                            </td>
                            <td>Desarrollos XYZ</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress me-2" style="width: 80px; height: 8px;">
                                        <div class="progress-bar bg-warning" style="width: 45%"></div>
                                    </div>
                                    <span class="small">45%</span>
                                </div>
                            </td>
                            <td>01/03/2024</td>
                            <td>15/12/2024</td>
                            <td><span class="badge bg-warning">Atención</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Ver Detalles">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-info" title="Reportes">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>
                                <strong>Proyecto Industrial</strong><br>
                                <small class="text-muted">IND-2024-003</small>
                            </td>
                            <td>Industrias 123</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress me-2" style="width: 80px; height: 8px;">
                                        <div class="progress-bar bg-danger" style="width: 25%"></div>
                                    </div>
                                    <span class="small">25%</span>
                                </div>
                            </td>
                            <td>10/02/2024</td>
                            <td>25/07/2024</td>
                            <td><span class="badge bg-danger">Retrasado</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Ver Detalles">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-info" title="Reportes">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.progress-lg {
    height: 20px;
}

.milestone-item {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.milestone-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.milestone-date {
    background: var(--primary-color);
    color: white;
    border-radius: 8px;
    padding: 10px;
    text-align: center;
    margin-right: 15px;
    min-width: 60px;
}

.milestone-date .day {
    display: block;
    font-size: 18px;
    font-weight: bold;
    line-height: 1;
}

.milestone-date .month {
    display: block;
    font-size: 12px;
    text-transform: uppercase;
}

.milestone-info h6 {
    margin-bottom: 2px;
    font-size: 14px;
}

.milestone-info p {
    font-size: 12px;
}
</style>

<?php include __DIR__ . '/../views/layout/footer.php'; ?>