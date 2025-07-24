<?php
/**
 * Test authentication without database
 * For demonstration purposes only
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Mock user login for testing
if (isset($_GET['test_login'])) {
    $role = $_GET['test_login'];
    
    if (in_array($role, ['admin', 'analista', 'visitante'])) {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_name'] = 'Usuario ' . ucfirst($role);
        $_SESSION['user_email'] = $role . '@test.com';
        $_SESSION['user_role'] = $role;
        $_SESSION['login_time'] = time();
        
        // Redirect to appropriate dashboard
        $dashboardUrls = [
            'admin' => '/admin/dashboard.php',
            'analista' => '/analista/dashboard.php',
            'visitante' => '/visitante/dashboard.php'
        ];
        
        header('Location: ' . $dashboardUrls[$role]);
        exit;
    }
}

// Mock logout
if (isset($_GET['test_logout'])) {
    session_destroy();
    header('Location: /test_auth.php');
    exit;
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$currentRole = $_SESSION['user_role'] ?? null;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Authentication - Sistema de Construcción</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="bi bi-shield-check"></i>
                            Test Authentication System
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if (!$isLoggedIn): ?>
                            <h5>Test Role-Based Authentication</h5>
                            <p class="text-muted">Click on a role to test the dashboard redirection:</p>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <a href="?test_login=admin" class="btn btn-danger w-100 btn-lg">
                                        <i class="bi bi-shield-check fs-4 d-block mb-2"></i>
                                        Login as Admin
                                    </a>
                                    <small class="text-muted d-block text-center mt-1">
                                        → /admin/dashboard.php
                                    </small>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <a href="?test_login=analista" class="btn btn-warning w-100 btn-lg">
                                        <i class="bi bi-person-gear fs-4 d-block mb-2"></i>
                                        Login as Analista
                                    </a>
                                    <small class="text-muted d-block text-center mt-1">
                                        → /analista/dashboard.php
                                    </small>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <a href="?test_login=visitante" class="btn btn-info w-100 btn-lg">
                                        <i class="bi bi-person fs-4 d-block mb-2"></i>
                                        Login as Visitante
                                    </a>
                                    <small class="text-muted d-block text-center mt-1">
                                        → /visitante/dashboard.php
                                    </small>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <h6>Test Registration System</h6>
                            <p class="text-muted">Test the registration page (doesn't require database):</p>
                            <a href="/register.php" class="btn btn-outline-primary">
                                <i class="bi bi-person-plus"></i>
                                Test Registration Page
                            </a>
                            
                        <?php else: ?>
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle"></i>
                                <strong>Logged in successfully!</strong><br>
                                Role: <span class="badge bg-primary"><?= htmlspecialchars($currentRole) ?></span><br>
                                User: <?= htmlspecialchars($_SESSION['user_name']) ?>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="?test_logout" class="btn btn-outline-danger">
                                    <i class="bi bi-box-arrow-right"></i>
                                    Logout
                                </a>
                                
                                <?php
                                $dashboardUrls = [
                                    'admin' => '/admin/dashboard.php',
                                    'analista' => '/analista/dashboard.php',
                                    'visitante' => '/visitante/dashboard.php'
                                ];
                                $dashboardUrl = $dashboardUrls[$currentRole] ?? '/dashboard.php';
                                ?>
                                
                                <a href="<?= $dashboardUrl ?>" class="btn btn-primary">
                                    <i class="bi bi-speedometer2"></i>
                                    Go to Dashboard
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <hr>
                        
                        <h6>Implementation Status</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between">
                                        Role-based dashboards
                                        <span class="badge bg-success">✓</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        Login redirection
                                        <span class="badge bg-success">✓</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        Registration system
                                        <span class="badge bg-success">✓</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between">
                                        Admin role protection
                                        <span class="badge bg-success">✓</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        Route access control
                                        <span class="badge bg-success">✓</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        Session management
                                        <span class="badge bg-success">✓</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>