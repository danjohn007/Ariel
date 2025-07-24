<?php
/**
 * Visitor Dashboard - Program View
 * Sistema Web de Análisis de Precios y Programa de Obra
 */

require_once __DIR__ . '/../includes/auth.php';

// Require visitor, analyst or admin access
verificarAcceso(['visitante', 'analista', 'admin']);

$pageTitle = 'Programa de Obras - ' . APP_NAME;
$pageDescription = 'Vista del programa general de obras';
$currentPage = 'programa';

include __DIR__ . '/../views/layout/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">
                <i class="bi bi-calendar3"></i>
                Programa de Obras
            </h1>
            <div class="btn-group">
                <button class="btn btn-outline-primary">
                    <i class="bi bi-download"></i>
                    Exportar PDF
                </button>
                <button class="btn btn-outline-secondary">
                    <i class="bi bi-printer"></i>
                    Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Program Overview -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="dashboard-card text-center">
            <div class="card-icon">
                <i class="bi bi-calendar-check"></i>
            </div>
            <h3>24</h3>
            <p>Obras Programadas</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="dashboard-card text-center">
            <div class="card-icon text-success">
                <i class="bi bi-clock"></i>
            </div>
            <h3>18</h3>
            <p>En Cronograma</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="dashboard-card text-center">
            <div class="card-icon text-warning">
                <i class="bi bi-exclamation-circle"></i>
            </div>
            <h3>6</h3>
            <p>Requieren Atención</p>
        </div>
    </div>
</div>

<!-- Program Calendar View -->
<div class="row">
    <div class="col-lg-8">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>
                    <i class="bi bi-calendar2-week"></i>
                    Cronograma General
                </h4>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary active">Mes</button>
                    <button class="btn btn-outline-primary">Trimestre</button>
                    <button class="btn btn-outline-primary">Año</button>
                </div>
            </div>
            
            <!-- Calendar placeholder -->
            <div class="calendar-view bg-light rounded p-4" style="min-height: 400px;">
                <div class="row text-center border-bottom pb-2 mb-3">
                    <div class="col"><strong>Lun</strong></div>
                    <div class="col"><strong>Mar</strong></div>
                    <div class="col"><strong>Mié</strong></div>
                    <div class="col"><strong>Jue</strong></div>
                    <div class="col"><strong>Vie</strong></div>
                    <div class="col"><strong>Sáb</strong></div>
                    <div class="col"><strong>Dom</strong></div>
                </div>
                
                <div class="text-center text-muted mt-5">
                    <i class="bi bi-calendar3 display-1"></i>
                    <p class="mt-3">Vista de calendario interactivo</p>
                    <small>Se implementará con FullCalendar.js</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="dashboard-card">
            <h4>
                <i class="bi bi-list-task"></i>
                Próximas Actividades
            </h4>
            <div class="upcoming-activities">
                <div class="activity-item">
                    <div class="activity-time">
                        <span class="time">09:00</span>
                        <span class="date">Hoy</span>
                    </div>
                    <div class="activity-info">
                        <h6>Inicio de excavación</h6>
                        <p class="text-muted mb-0">Residencial Norte - Etapa 2</p>
                        <span class="badge bg-success">Programado</span>
                    </div>
                </div>
                
                <div class="activity-item">
                    <div class="activity-time">
                        <span class="time">14:30</span>
                        <span class="date">Hoy</span>
                    </div>
                    <div class="activity-info">
                        <h6>Inspección estructural</h6>
                        <p class="text-muted mb-0">Centro Comercial Sur</p>
                        <span class="badge bg-warning">En progreso</span>
                    </div>
                </div>
                
                <div class="activity-item">
                    <div class="activity-time">
                        <span class="time">08:00</span>
                        <span class="date">Mañana</span>
                    </div>
                    <div class="activity-info">
                        <h6>Entrega de materiales</h6>
                        <p class="text-muted mb-0">Proyecto Industrial</p>
                        <span class="badge bg-info">Pendiente</span>
                    </div>
                </div>
                
                <div class="activity-item">
                    <div class="activity-time">
                        <span class="time">10:15</span>
                        <span class="date">26 Jul</span>
                    </div>
                    <div class="activity-info">
                        <h6>Reunión de seguimiento</h6>
                        <p class="text-muted mb-0">Todas las obras</p>
                        <span class="badge bg-primary">Reunión</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Program Summary -->
<div class="row mt-4">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>
                    <i class="bi bi-table"></i>
                    Resumen del Programa
                </h4>
                <div class="d-flex gap-2">
                    <input type="date" class="form-control form-control-sm" style="width: auto;" value="<?php echo date('Y-m-d'); ?>">
                    <select class="form-select form-select-sm" style="width: auto;">
                        <option>Todas las obras</option>
                        <option>Solo activas</option>
                        <option>Solo retrasadas</option>
                    </select>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Obra</th>
                            <th>Actividad</th>
                            <th>Fecha Programada</th>
                            <th>Duración</th>
                            <th>Recursos</th>
                            <th>Estado</th>
                            <th>Progreso</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <strong>Residencial Norte</strong><br>
                                <small class="text-muted">RES-2024-001</small>
                            </td>
                            <td>Cimentación - Excavación</td>
                            <td>24/07/2024 - 30/07/2024</td>
                            <td>7 días</td>
                            <td>2 Excavadoras, 4 Operarios</td>
                            <td><span class="badge bg-success">En tiempo</span></td>
                            <td>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: 60%"></div>
                                </div>
                                <small>60%</small>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>
                                <strong>Centro Comercial Sur</strong><br>
                                <small class="text-muted">COM-2024-002</small>
                            </td>
                            <td>Estructura - Columnas Nivel 2</td>
                            <td>25/07/2024 - 05/08/2024</td>
                            <td>12 días</td>
                            <td>1 Grúa, 6 Técnicos</td>
                            <td><span class="badge bg-warning">Atención</span></td>
                            <td>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: 30%"></div>
                                </div>
                                <small>30%</small>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>
                                <strong>Proyecto Industrial</strong><br>
                                <small class="text-muted">IND-2024-003</small>
                            </td>
                            <td>Instalaciones - Sistema Eléctrico</td>
                            <td>22/07/2024 - 28/07/2024</td>
                            <td>7 días</td>
                            <td>3 Electricistas</td>
                            <td><span class="badge bg-danger">Retrasado</span></td>
                            <td>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-danger" style="width: 15%"></div>
                                </div>
                                <small>15%</small>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>
                                <strong>Oficinas Corporate</strong><br>
                                <small class="text-muted">OFF-2024-004</small>
                            </td>
                            <td>Acabados - Pintura Interior</td>
                            <td>26/07/2024 - 02/08/2024</td>
                            <td>8 días</td>
                            <td>4 Pintores</td>
                            <td><span class="badge bg-info">Programado</span></td>
                            <td>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-info" style="width: 0%"></div>
                                </div>
                                <small>0%</small>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <nav class="mt-3">
                <ul class="pagination pagination-sm justify-content-center">
                    <li class="page-item disabled">
                        <span class="page-link">Anterior</span>
                    </li>
                    <li class="page-item active">
                        <span class="page-link">1</span>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">Siguiente</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<style>
.activity-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.activity-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.activity-time {
    background: var(--light-color);
    border-radius: 8px;
    padding: 8px 12px;
    text-align: center;
    margin-right: 15px;
    min-width: 80px;
    border: 1px solid #dee2e6;
}

.activity-time .time {
    display: block;
    font-weight: bold;
    font-size: 14px;
    color: var(--primary-color);
}

.activity-time .date {
    display: block;
    font-size: 11px;
    color: var(--secondary-color);
    text-transform: uppercase;
}

.activity-info h6 {
    margin-bottom: 5px;
    font-size: 14px;
}

.activity-info p {
    font-size: 12px;
    margin-bottom: 5px;
}

.calendar-view .row > div {
    padding: 8px;
    border-right: 1px solid #dee2e6;
}

.calendar-view .row > div:last-child {
    border-right: none;
}
</style>

<?php include __DIR__ . '/../views/layout/footer.php'; ?>