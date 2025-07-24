<?php
/**
 * Login Page
 * Sistema Web de Análisis de Precios y Programa de Obra
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/includes/security.php';

// Redirect if already logged in
if (isLoggedIn()) {
    $currentUser = getCurrentUser();
    redirectAfterLogin($currentUser['rol']);
    exit;
}

$pageTitle = 'Iniciar Sesión - ' . APP_NAME;
$pageDescription = 'Inicie sesión en el sistema de análisis de precios';
$bodyClass = 'auth-page';

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Token de seguridad inválido. Intente nuevamente.';
    } else {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $error = 'Todos los campos son obligatorios';
        } else {
            $user = new User();
            $result = $user->login($email, $password);
            
            if ($result['success']) {
                // Store flash message for redirect
                $_SESSION['flash_message'] = 'Bienvenido, ' . $result['user']['nombre'];
                $_SESSION['flash_type'] = 'success';
                
                // Redirect based on role
                redirectAfterLogin($result['user']['rol']);
                exit;
            } else {
                $error = $result['message'];
            }
        }
    }
}

include __DIR__ . '/views/layout/header.php';
?>

<div class="auth-container">
    <div class="text-center mb-4">
        <img src="/logo-mechanical-fix.png" alt="Logo" height="80" class="mb-3">
        <h2 class="h4">Iniciar Sesión</h2>
        <p class="text-muted">Accede a tu cuenta del sistema</p>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-danger" role="alert">
            <i class="bi bi-exclamation-triangle"></i>
            <?php echo Security::escape($error); ?>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success" role="alert">
            <i class="bi bi-check-circle"></i>
            <?php echo Security::escape($success); ?>
        </div>
    <?php endif; ?>
    
    <form method="post" class="auth-form" data-validate="true" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
        
        <div class="mb-3">
            <label for="email" class="form-label">
                <i class="bi bi-envelope"></i>
                Correo Electrónico
            </label>
            <input 
                type="email" 
                class="form-control" 
                id="email" 
                name="email" 
                value="<?php echo Security::escape($_POST['email'] ?? ''); ?>"
                required 
                autocomplete="email"
                placeholder="Ingrese su correo electrónico"
            >
        </div>
        
        <div class="mb-3">
            <label for="password" class="form-label">
                <i class="bi bi-lock"></i>
                Contraseña
            </label>
            <div class="input-group">
                <input 
                    type="password" 
                    class="form-control" 
                    id="password" 
                    name="password" 
                    required 
                    autocomplete="current-password"
                    placeholder="Ingrese su contraseña"
                >
                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                </button>
            </div>
        </div>
        
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="remember" name="remember">
            <label class="form-check-label" for="remember">
                Recordarme
            </label>
        </div>
        
        <div class="d-grid">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-box-arrow-in-right"></i>
                Iniciar Sesión
            </button>
        </div>
    </form>
    
    <div class="text-center mt-4">
        <p class="mb-2">
            <a href="/forgot-password.php" class="text-decoration-none">
                ¿Olvidaste tu contraseña?
            </a>
        </p>
        <p class="mb-0">
            ¿No tienes cuenta? 
            <a href="/register.php" class="text-decoration-none">
                Regístrate aquí
            </a>
        </p>
    </div>
    
    <!-- Security Information -->
    <div class="mt-4 p-3 bg-light rounded">
        <h6 class="text-muted">
            <i class="bi bi-shield-check"></i>
            Información de Seguridad
        </h6>
        <ul class="text-muted small mb-0">
            <li>Las sesiones expiran automáticamente por seguridad</li>
            <li>Se registran todos los intentos de acceso</li>
            <li>Cuentas bloqueadas temporalmente tras intentos fallidos</li>
        </ul>
    </div>
</div>

<script>
// Toggle password visibility
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordField = document.getElementById('password');
    const icon = document.getElementById('togglePasswordIcon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        passwordField.type = 'password';
        icon.className = 'bi bi-eye';
    }
});

// Demo credentials info (remove in production)
if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
    document.addEventListener('DOMContentLoaded', function() {
        const demoInfo = document.createElement('div');
        demoInfo.className = 'alert alert-info mt-3';
        demoInfo.innerHTML = `
            <strong>Credenciales de demostración:</strong><br>
            Email: admin@empresa.com<br>
            Contraseña: admin123
        `;
        document.querySelector('.auth-container').appendChild(demoInfo);
    });
}
</script>

<?php
$extraJS = [];
include __DIR__ . '/views/layout/footer.php';
?>