<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription ?? 'Sistema de Análisis de Precios y Programa de Obra'); ?>">
    <title><?php echo htmlspecialchars($pageTitle ?? APP_NAME); ?></title>
    
    <!-- Security headers -->
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="/public/css/styles.css" rel="stylesheet">
    
    <?php if (isset($extraCSS)): ?>
        <?php foreach ($extraCSS as $css): ?>
            <link href="<?php echo htmlspecialchars($css); ?>" rel="stylesheet">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="<?php echo htmlspecialchars($bodyClass ?? ''); ?>">
    
    <!-- Header -->
    <header class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <img src="/logo-mechanical-fix.png" alt="Logo" height="40" class="me-2">
                <?php echo htmlspecialchars(APP_NAME); ?>
            </a>
            
            <?php if (isLoggedIn()): ?>
                <div class="navbar-nav ms-auto">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                            <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?>
                            <span class="badge bg-secondary ms-1"><?php echo htmlspecialchars($_SESSION['rol'] ?? ''); ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><span class="dropdown-item-text"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/profile.php"><i class="bi bi-person"></i> Perfil</a></li>
                            <li><a class="dropdown-item" href="/logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a></li>
                        </ul>
                    </div>
                </div>
            <?php else: ?>
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="/login.php">Iniciar Sesión</a>
                    <a class="nav-link" href="/register.php">Registrarse</a>
                </div>
            <?php endif; ?>
        </div>
    </header>
    
    <!-- Navigation Menu -->
    <?php if (isLoggedIn()): ?>
        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
            <div class="container">
                <?php echo generateRoleMenu($currentPage ?? ''); ?>
            </div>
        </nav>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main class="container mt-4">
        <!-- Flash Messages -->
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?php echo htmlspecialchars($_SESSION['flash_type'] ?? 'info'); ?> alert-dismissible fade show">
                <?php echo htmlspecialchars($_SESSION['flash_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php 
            unset($_SESSION['flash_message'], $_SESSION['flash_type']); 
            endif; 
            ?>
        
        <!-- Page Content -->