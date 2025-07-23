<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">
                <i class="bi bi-speedometer2"></i>
                Dashboard
            </h1>
            <div>
                <span class="badge bg-primary">
                    <?= ucfirst($user['role']) ?>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Welcome Card -->
    <div class="col-12">
        <div class="card border-primary">
            <div class="card-body">
                <h5 class="card-title text-primary">
                    <i class="bi bi-person-circle"></i>
                    ¡Bienvenido/a, <?= $this->escape($user['first_name']) ?>!
                </h5>
                <p class="card-text">
                    Este es su panel de control personalizado. Desde aquí puede acceder a todas las funcionalidades del sistema.
                </p>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Email:</strong> <?= $this->escape($user['email']) ?></p>
                        <p class="mb-1"><strong>Rol:</strong> <?= ucfirst($user['role']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <?php if ($user['phone']): ?>
                            <p class="mb-1"><strong>Teléfono:</strong> <?= $this->escape($user['phone']) ?></p>
                        <?php endif; ?>
                        <p class="mb-1"><strong>Miembro desde:</strong> <?= date('F Y', strtotime($user['created_at'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                    <i class="bi bi-lightning-fill"></i>
                    Acciones Rápidas
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <?php if ($user['role'] === 'client'): ?>
                        <a href="<?= $this->url('services/request') ?>" class="btn btn-outline-primary">
                            <i class="bi bi-plus-circle"></i>
                            Solicitar Nuevo Servicio
                        </a>
                        <a href="<?= $this->url('client/services') ?>" class="btn btn-outline-info">
                            <i class="bi bi-list-ul"></i>
                            Ver Mis Servicios
                        </a>
                    <?php elseif ($user['role'] === 'mechanic'): ?>
                        <a href="<?= $this->url('mechanic/services') ?>" class="btn btn-outline-primary">
                            <i class="bi bi-wrench"></i>
                            Mis Servicios Asignados
                        </a>
                        <a href="<?= $this->url('mechanic/schedule') ?>" class="btn btn-outline-info">
                            <i class="bi bi-calendar3"></i>
                            Ver Agenda
                        </a>
                    <?php elseif (in_array($user['role'], ['admin', 'coordinator'])): ?>
                        <a href="<?= $this->url('services/manage') ?>" class="btn btn-outline-primary">
                            <i class="bi bi-list-ul"></i>
                            Gestionar Servicios
                        </a>
                        <a href="<?= $this->url('reports') ?>" class="btn btn-outline-info">
                            <i class="bi bi-graph-up"></i>
                            Ver Reportes
                        </a>
                    <?php endif; ?>
                    
                    <a href="<?= $this->url('profile') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-person-gear"></i>
                        Editar Perfil
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- System Stats -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="bi bi-bar-chart-fill"></i>
                    Información del Sistema
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 border-end">
                        <div class="h4 mb-0 text-primary">24/7</div>
                        <small class="text-muted">Disponibilidad</small>
                    </div>
                    <div class="col-6">
                        <div class="h4 mb-0 text-success">100%</div>
                        <small class="text-muted">Confiabilidad</small>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <p class="mb-2">
                        <i class="bi bi-shield-check text-success"></i>
                        Sistema seguro y confiable
                    </p>
                    <p class="mb-0">
                        <i class="bi bi-telephone text-primary"></i>
                        Soporte: +52 (555) 123-4567
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-clock-history"></i>
                    Actividad Reciente
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center text-muted py-4">
                    <i class="bi bi-info-circle" style="font-size: 2rem;"></i>
                    <p class="mt-2">No hay actividad reciente para mostrar.</p>
                    <?php if ($user['role'] === 'client'): ?>
                        <a href="<?= $this->url('services/request') ?>" class="btn btn-primary">
                            Solicitar Primer Servicio
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>