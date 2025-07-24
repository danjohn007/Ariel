<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? escape($pageTitle . ' - ' . APP_NAME) : escape(APP_NAME) ?></title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/assets/css/style.css" rel="stylesheet">
    
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link href="<?= escape($css) ?>" rel="stylesheet">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Navigation -->
    <?php if ($auth->isLoggedIn()): ?>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand" href="/dashboard.php">
                    <i class="bi bi-building"></i>
                    Sistema de Construcción
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/dashboard.php">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        
                        <?php if ($auth->canWrite()): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-building"></i> Obras
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/obras/index.php">Ver Obras</a></li>
                                    <li><a class="dropdown-item" href="/obras/create.php">Nueva Obra</a></li>
                                </ul>
                            </li>
                            
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-list-check"></i> Conceptos
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/conceptos/index.php">Ver Conceptos</a></li>
                                    <li><a class="dropdown-item" href="/categorias/index.php">Categorías</a></li>
                                </ul>
                            </li>
                            
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-box"></i> Recursos
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/materiales/index.php">Materiales</a></li>
                                    <li><a class="dropdown-item" href="/mano-obra/index.php">Mano de Obra</a></li>
                                    <li><a class="dropdown-item" href="/maquinaria/index.php">Maquinaria</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="/proveedores/index.php">Proveedores</a></li>
                                </ul>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link" href="/programa/index.php">
                                    <i class="bi bi-calendar3"></i> Programa
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/obras/index.php">
                                    <i class="bi bi-building"></i> Obras
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/reportes/index.php">
                                    <i class="bi bi-graph-up"></i> Reportes
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php if ($auth->isAdmin()): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-gear"></i> Administración
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/usuarios/index.php">Usuarios</a></li>
                                    <li><a class="dropdown-item" href="/logs/index.php">Logs de Auditoría</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="/backup/index.php">Respaldos</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                    </ul>
                    
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i>
                                <?= escape($auth->getCurrentUser()['name']) ?>
                                <span class="badge bg-<?= getRoleColor($auth->getCurrentUser()['role']) ?> ms-1">
                                    <?= getRoleName($auth->getCurrentUser()['role']) ?>
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/perfil/index.php">
                                    <i class="bi bi-person"></i> Mi Perfil
                                </a></li>
                                <li><a class="dropdown-item" href="/configuracion/index.php">
                                    <i class="bi bi-gear"></i> Configuración
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/logout.php">
                                    <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                                </a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    <?php endif; ?>
    
    <!-- Flash Messages -->
    <?php $flash = getFlashMessage(); ?>
    <?php if ($flash): ?>
        <div class="container-fluid mt-3">
            <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : escape($flash['type']) ?> alert-dismissible fade show" role="alert">
                <?= escape($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Breadcrumb -->
    <?php if (isset($breadcrumbs) && !empty($breadcrumbs)): ?>
        <div class="container-fluid mt-3">
            <?= breadcrumb($breadcrumbs) ?>
        </div>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main class="<?= $auth->isLoggedIn() ? 'container-fluid mt-4' : '' ?>">
        <?= $content ?>
    </main>
    
    <!-- Footer -->
    <?php if ($auth->isLoggedIn()): ?>
        <footer class="bg-light text-center py-3 mt-5">
            <div class="container">
                <small class="text-muted">
                    <?= escape(APP_NAME) ?> &copy; <?= date('Y') ?> - 
                    Desarrollado para la gestión de obras de construcción
                </small>
            </div>
        </footer>
    <?php endif; ?>
    
    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js for dashboards -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom JS -->
    <script src="/assets/js/app.js"></script>
    
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?= escape($js) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <script>
        // CSRF Token for AJAX requests
        window.csrfToken = '<?= generateCSRFToken() ?>';
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>