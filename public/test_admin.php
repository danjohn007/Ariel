<?php
/**
 * Test Dashboard de Administrador
 */

require_once __DIR__ . '/mock_auth.php';

$auth = new MockAuth();
$auth->requireRole(ROLE_ADMIN);
$currentUser = $auth->getCurrentUser();

// Mock data
$totalUsuarios = 15;
$usuariosAdmin = 2;
$usuariosAnalista = 7;
$usuariosVisitante = 6;
$totalObras = 8;
$obrasActivas = 5;
$presupuestoTotal = 15750000;
$totalConceptos = 234;

$pageTitle = 'Dashboard Administrador - TEST';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/test_auth.php">
                <i class="bi bi-building"></i>
                Sistema de Construcción - TEST
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i>
                        <?= escape($currentUser['name']) ?>
                        <span class="badge bg-danger ms-1">
                            <?= getRoleName($currentUser['role']) ?>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="/test_auth.php?test_logout">
                            <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <!-- Bienvenida -->
        <div class="row mb-4">
            <div class="col">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-1">
                            <i class="bi bi-shield-check text-danger"></i>
                            Panel de Administrador
                        </h1>
                        <p class="text-muted">
                            Bienvenido, <?= escape($currentUser['name']) ?> - Gestión completa del sistema
                        </p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-danger fs-6">
                            <?= getRoleName($currentUser['role']) ?>
                        </span>
                        <div class="alert alert-warning mt-2">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>MODO TEST</strong> - Datos simulados
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tarjetas de estadísticas principales -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm border-start border-4 border-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title text-muted mb-2">Total Usuarios</h6>
                                <h3 class="mb-0 text-primary"><?= number_format($totalUsuarios) ?></h3>
                            </div>
                            <div class="ms-3">
                                <div class="bg-primary rounded-circle p-3">
                                    <i class="bi bi-people text-white fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm border-start border-4 border-success">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title text-muted mb-2">Obras Activas</h6>
                                <h3 class="mb-0 text-success"><?= number_format($obrasActivas) ?></h3>
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
                <div class="card border-0 shadow-sm border-start border-4 border-info">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title text-muted mb-2">Presupuesto Total</h6>
                                <h3 class="mb-0 text-info"><?= formatMoney($presupuestoTotal) ?></h3>
                            </div>
                            <div class="ms-3">
                                <div class="bg-info rounded-circle p-3">
                                    <i class="bi bi-currency-dollar text-white fs-4"></i>
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
                                <h6 class="card-title text-muted mb-2">Total Conceptos</h6>
                                <h3 class="mb-0 text-warning"><?= number_format($totalConceptos) ?></h3>
                            </div>
                            <div class="ms-3">
                                <div class="bg-warning rounded-circle p-3">
                                    <i class="bi bi-list-check text-white fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Estadísticas de usuarios por rol -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="bg-danger rounded-circle p-3 mx-auto mb-3" style="width: fit-content;">
                            <i class="bi bi-shield-check text-white fs-4"></i>
                        </div>
                        <h4 class="text-danger"><?= number_format($usuariosAdmin) ?></h4>
                        <p class="text-muted mb-0">Administradores</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="bg-warning rounded-circle p-3 mx-auto mb-3" style="width: fit-content;">
                            <i class="bi bi-person-gear text-white fs-4"></i>
                        </div>
                        <h4 class="text-warning"><?= number_format($usuariosAnalista) ?></h4>
                        <p class="text-muted mb-0">Analistas</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="bg-info rounded-circle p-3 mx-auto mb-3" style="width: fit-content;">
                            <i class="bi bi-person text-white fs-4"></i>
                        </div>
                        <h4 class="text-info"><?= number_format($usuariosVisitante) ?></h4>
                        <p class="text-muted mb-0">Visitantes</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Verification that admin dashboard is working -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-check-circle"></i>
                            ✅ Admin Dashboard Test Successful
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>User Information:</h6>
                                <ul class="list-unstyled">
                                    <li><strong>Name:</strong> <?= escape($currentUser['name']) ?></li>
                                    <li><strong>Email:</strong> <?= escape($currentUser['email']) ?></li>
                                    <li><strong>Role:</strong> <?= escape($currentUser['role']) ?></li>
                                    <li><strong>Access Level:</strong> Administrator</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Dashboard Features:</h6>
                                <ul class="list-unstyled">
                                    <li>✅ Role verification (requireRole)</li>
                                    <li>✅ Admin-specific interface</li>
                                    <li>✅ Statistical data display</li>
                                    <li>✅ Navigation protection</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <a href="/test_auth.php" class="btn btn-primary">
                                <i class="bi bi-arrow-left"></i> Back to Test Menu
                            </a>
                            <a href="/test_analista.php" class="btn btn-outline-warning">
                                Test Analista Dashboard
                            </a>
                            <a href="/test_visitante.php" class="btn btn-outline-info">
                                Test Visitante Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>