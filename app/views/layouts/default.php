<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $this->escape($title) . ' - ' : '' ?><?= APP_NAME ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link href="<?= $this->asset('css/style.css') ?>" rel="stylesheet">
    
    <?php if (isset($additionalCSS)): ?>
        <?= $additionalCSS ?>
    <?php endif; ?>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?= $this->url() ?>">
                <i class="bi bi-tools"></i>
                <?= APP_NAME ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $this->url('dashboard') ?>">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        
                        <?php if (isset($_SESSION['user_role'])): ?>
                            <?php if ($_SESSION['user_role'] === 'client'): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?= $this->url('services/request') ?>">
                                        <i class="bi bi-plus-circle"></i> Solicitar Servicio
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php if (in_array($_SESSION['user_role'], ['admin', 'coordinator'])): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?= $this->url('services/manage') ?>">
                                        <i class="bi bi-list-ul"></i> Gestionar Servicios
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?= $this->url('reports') ?>">
                                        <i class="bi bi-graph-up"></i> Reportes
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php if ($_SESSION['user_role'] === 'mechanic'): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?= $this->url('mechanic/dashboard') ?>">
                                        <i class="bi bi-wrench"></i> Mis Servicios
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $this->url('services/request') ?>">
                                <i class="bi bi-plus-circle"></i> Solicitar Servicio
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i>
                                <?= $this->escape($_SESSION['user_name'] ?? 'Usuario') ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= $this->url('profile') ?>">
                                    <i class="bi bi-person"></i> Mi Perfil
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= $this->url('logout') ?>">
                                    <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $this->url('login') ?>">
                                <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $this->url('register') ?>">
                                <i class="bi bi-person-plus"></i> Registrarse
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-4">
        <div class="container">
            <!-- Flash Messages -->
            <?= $this->flashMessages() ?>
            
            <!-- Page Content -->
            <?= $content ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="bi bi-tools"></i> <?= APP_NAME ?></h5>
                    <p>Servicio profesional de mecánicos a domicilio</p>
                </div>
                <div class="col-md-6">
                    <h6>Contacto</h6>
                    <p>
                        <i class="bi bi-telephone"></i> +52 (555) 123-4567<br>
                        <i class="bi bi-envelope"></i> info@mechanicalfix.com
                    </p>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <small>&copy; <?= date('Y') ?> <?= APP_NAME ?>. Todos los derechos reservados.</small>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?= $this->asset('js/app.js') ?>"></script>
    
    <?php if (isset($additionalJS)): ?>
        <?= $additionalJS ?>
    <?php endif; ?>
</body>
</html>