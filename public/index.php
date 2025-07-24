<?php
/**
 * Punto de entrada principal - index.php
 * Sistema de Análisis de Precios y Programa de Obra
 */

// Cargar configuración
require_once __DIR__ . '/../config/config.php';
require_once SRC_PATH . '/includes/Database.php';
require_once SRC_PATH . '/includes/Auth.php';
require_once SRC_PATH . '/includes/functions.php';

// Inicializar autenticación
$auth = new Auth();

// Si el usuario ya está logueado, redirigir al dashboard correspondiente
if ($auth->isLoggedIn()) {
    $dashboardUrl = $auth->getDashboardUrl();
    header("Location: $dashboardUrl");
    exit;
}

// Si hay datos de login, procesarlos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Verificar token CSRF
    if (!verifyCSRFToken($csrf_token)) {
        $error = 'Token de seguridad inválido.';
    } elseif (empty($email) || empty($password)) {
        $error = 'Por favor complete todos los campos.';
    } elseif (!isValidEmail($email)) {
        $error = 'El formato del email no es válido.';
    } else {
        $result = $auth->login($email, $password);
        
        if ($result['success']) {
            $dashboardUrl = $auth->getDashboardUrl();
            header("Location: $dashboardUrl");
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

$pageTitle = 'Iniciar Sesión';

// Capturar contenido
ob_start();
?>

<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="/logo-mechanical-fix.png" alt="Logo" class="img-fluid mb-3" style="max-height: 80px;">
                        <h4 class="card-title"><?= escape(APP_NAME) ?></h4>
                        <p class="text-muted">Sistema de gestión de construcción</p>
                    </div>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-triangle"></i>
                            <?= escape($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="/">
                        <?= csrfField() ?>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope"></i> Correo Electrónico
                            </label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   value="<?= escape($_POST['email'] ?? '') ?>"
                                   required 
                                   autofocus>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="bi bi-lock"></i> Contraseña
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   required>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right"></i>
                                Iniciar Sesión
                            </button>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <p class="mb-3">
                            ¿No tienes una cuenta? 
                            <a href="/register.php" class="text-decoration-none">
                                <i class="bi bi-person-plus"></i>
                                Registrarse
                            </a>
                        </p>
                        
                        <h6 class="text-muted mb-3">Usuarios de Prueba:</h6>
                        <div class="row g-2">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body py-2">
                                        <strong>Administrador:</strong><br>
                                        <small>admin@construccion.com / password</small>
                                        <span class="badge bg-danger ms-1">Admin</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body py-2">
                                        <strong>Analista:</strong><br>
                                        <small>analista@construccion.com / password</small>
                                        <span class="badge bg-warning ms-1">Analista</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body py-2">
                                        <strong>Visitante:</strong><br>
                                        <small>visitante@construccion.com / password</small>
                                        <span class="badge bg-info ms-1">Visitante</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer text-center bg-light">
                    <small class="text-muted">
                        <i class="bi bi-shield-check"></i>
                        Acceso seguro con roles protegidos
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.min-vh-100 {
    min-height: 100vh;
}
body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.card {
    border: none;
    border-radius: 15px;
}
.btn-primary {
    background: linear-gradient(45deg, #667eea, #764ba2);
    border: none;
}
.btn-primary:hover {
    background: linear-gradient(45deg, #5a67d8, #6b46c1);
    transform: translateY(-1px);
}
</style>

<?php
$content = ob_get_clean();

// Incluir layout
include SRC_PATH . '/views/layout.php';
?>