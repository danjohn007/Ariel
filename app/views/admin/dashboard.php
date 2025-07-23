<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">
                <i class="bi bi-gear"></i>
                Panel de Administración
            </h1>
            <div>
                <span class="badge bg-primary">Administrador</span>
            </div>
        </div>
    </div>
</div>

<!-- Welcome Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-success">
            <h5 class="alert-heading">
                <i class="bi bi-check-circle"></i>
                ¡Bienvenido al Sistema Mechanical FIX!
            </h5>
            <p class="mb-0">
                El sistema ha sido instalado correctamente y está funcionando. 
                Como administrador, tienes acceso completo a todas las funcionalidades.
            </p>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Usuarios</h6>
                        <h3 class="mb-0">1</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-people" style="font-size: 2rem;"></i>
                    </div>
                </div>
                <small>Sistema instalado</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Servicios</h6>
                        <h3 class="mb-0">8</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-tools" style="font-size: 2rem;"></i>
                    </div>
                </div>
                <small>Tipos configurados</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Estado</h6>
                        <h3 class="mb-0">
                            <i class="bi bi-check-circle"></i>
                        </h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-shield-check" style="font-size: 2rem;"></i>
                    </div>
                </div>
                <small>Sistema operativo</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Base de Datos</h6>
                        <h3 class="mb-0">Demo</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-database" style="font-size: 2rem;"></i>
                    </div>
                </div>
                <small>Modo demostración</small>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-lightning-fill"></i>
                    Configuración del Sistema
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="list-group">
                            <h6 class="list-group-item-heading mb-2">Gestión de Usuarios</h6>
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="bi bi-person-plus"></i>
                                Crear Usuario
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="bi bi-people"></i>
                                Gestionar Usuarios
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="bi bi-wrench"></i>
                                Gestionar Mecánicos
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="list-group">
                            <h6 class="list-group-item-heading mb-2">Configuración</h6>
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="bi bi-gear"></i>
                                Configuración General
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="bi bi-tools"></i>
                                Tipos de Servicio
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="bi bi-map"></i>
                                Configurar Google Maps
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle"></i>
                    Información del Sistema
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Versión:</strong> 1.0.0<br>
                    <strong>PHP:</strong> <?= PHP_VERSION ?><br>
                    <strong>Servidor:</strong> <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Desarrollo' ?>
                </div>
                
                <div class="alert alert-warning py-2">
                    <small>
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Modo Demo:</strong> Configurar base de datos para producción
                    </small>
                </div>
                
                <div class="d-grid gap-2">
                    <a href="<?= $this->url('services/manage') ?>" class="btn btn-primary btn-sm">
                        <i class="bi bi-list-ul"></i>
                        Ver Servicios
                    </a>
                    <a href="<?= $this->url('reports') ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-graph-up"></i>
                        Reportes
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Modules -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-grid-3x3-gap"></i>
                    Módulos del Sistema
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="text-center p-3 border rounded">
                            <i class="bi bi-file-earmark-text text-primary" style="font-size: 2rem;"></i>
                            <h6 class="mt-2">Solicitudes</h6>
                            <p class="text-muted small">Gestión de solicitudes de servicio</p>
                            <span class="badge bg-success">Implementado</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 border rounded">
                            <i class="bi bi-people text-primary" style="font-size: 2rem;"></i>
                            <h6 class="mt-2">Usuarios</h6>
                            <p class="text-muted small">Gestión de usuarios y roles</p>
                            <span class="badge bg-success">Implementado</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 border rounded">
                            <i class="bi bi-credit-card text-primary" style="font-size: 2rem;"></i>
                            <h6 class="mt-2">Pagos</h6>
                            <p class="text-muted small">Sistema de pagos y facturación</p>
                            <span class="badge bg-warning">En desarrollo</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>