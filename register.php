<?php
/**
 * Registration Page
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

$pageTitle = 'Registrarse - ' . APP_NAME;
$pageDescription = 'Registre una nueva cuenta en el sistema';
$bodyClass = 'auth-page';

$error = '';
$success = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Token de seguridad inválido. Intente nuevamente.';
    } else {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        
        // Basic validation
        if (empty($email) || empty($password) || empty($confirmPassword) || empty($nombre)) {
            $error = 'Todos los campos son obligatorios';
        } elseif ($password !== $confirmPassword) {
            $error = 'Las contraseñas no coinciden';
        } else {
            // Determine role based on email
            $rol = 'visitante'; // Default role
            if (in_array($email, ADMIN_EMAILS)) {
                $rol = 'admin';
            }
            
            $user = new User();
            $result = $user->register($email, $password, $nombre, $rol);
            
            if ($result['success']) {
                $success = 'Cuenta creada exitosamente. Ahora puede iniciar sesión.';
                
                // Clear form data on success
                $_POST = [];
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
        <h2 class="h4">Crear Cuenta</h2>
        <p class="text-muted">Regístrese para acceder al sistema</p>
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
            <label for="nombre" class="form-label">
                <i class="bi bi-person"></i>
                Nombre Completo
            </label>
            <input 
                type="text" 
                class="form-control" 
                id="nombre" 
                name="nombre" 
                value="<?php echo Security::escape($_POST['nombre'] ?? ''); ?>"
                required 
                autocomplete="name"
                placeholder="Ingrese su nombre completo"
                data-validate="text"
            >
        </div>
        
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
            <div class="form-text">
                <i class="bi bi-info-circle"></i>
                Use admin@empresa.com para obtener privilegios de administrador
            </div>
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
                    autocomplete="new-password"
                    placeholder="Crear contraseña"
                    data-validate="password"
                >
                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                </button>
            </div>
            <div class="form-text">
                <i class="bi bi-shield-check"></i>
                Mínimo 8 caracteres, una mayúscula, una minúscula y un número
            </div>
        </div>
        
        <div class="mb-3">
            <label for="confirm_password" class="form-label">
                <i class="bi bi-lock-fill"></i>
                Confirmar Contraseña
            </label>
            <input 
                type="password" 
                class="form-control" 
                id="confirm_password" 
                name="confirm_password" 
                required 
                autocomplete="new-password"
                placeholder="Confirme su contraseña"
                data-validate="password-confirm"
            >
        </div>
        
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
            <label class="form-check-label" for="terms">
                Acepto los <a href="/terms.php" target="_blank">términos y condiciones</a>
            </label>
        </div>
        
        <div class="d-grid">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-person-plus"></i>
                Crear Cuenta
            </button>
        </div>
    </form>
    
    <div class="text-center mt-4">
        <p class="mb-0">
            ¿Ya tienes cuenta? 
            <a href="/login.php" class="text-decoration-none">
                Inicia sesión aquí
            </a>
        </p>
    </div>
    
    <!-- Role Information -->
    <div class="mt-4 p-3 bg-light rounded">
        <h6 class="text-muted">
            <i class="bi bi-info-circle"></i>
            Roles del Sistema
        </h6>
        <ul class="text-muted small mb-0">
            <li><strong>Admin:</strong> Acceso total al sistema (solo admin@empresa.com)</li>
            <li><strong>Analista:</strong> Gestión de obras y reportes</li>
            <li><strong>Visitante:</strong> Solo lectura de reportes y programas</li>
        </ul>
    </div>
    
    <!-- Security Information -->
    <div class="mt-3 p-3 bg-light rounded">
        <h6 class="text-muted">
            <i class="bi bi-shield-check"></i>
            Seguridad
        </h6>
        <ul class="text-muted small mb-0">
            <li>Sus datos están protegidos con cifrado</li>
            <li>Las contraseñas se almacenan de forma segura</li>
            <li>Se registra toda la actividad del sistema</li>
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

// Password strength indicator
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strengthBar = document.querySelector('.password-strength');
    
    if (!strengthBar) {
        const bar = document.createElement('div');
        bar.className = 'password-strength mt-2';
        bar.innerHTML = '<div class="progress"><div class="progress-bar" role="progressbar"></div></div>';
        this.parentNode.parentNode.appendChild(bar);
    }
    
    const progressBar = document.querySelector('.password-strength .progress-bar');
    let strength = 0;
    let strengthText = '';
    
    if (password.length >= 8) strength += 25;
    if (/[A-Z]/.test(password)) strength += 25;
    if (/[a-z]/.test(password)) strength += 25;
    if (/[0-9]/.test(password)) strength += 25;
    
    if (strength < 50) {
        strengthText = 'Débil';
        progressBar.className = 'progress-bar bg-danger';
    } else if (strength < 75) {
        strengthText = 'Regular';
        progressBar.className = 'progress-bar bg-warning';
    } else if (strength < 100) {
        strengthText = 'Buena';
        progressBar.className = 'progress-bar bg-info';
    } else {
        strengthText = 'Excelente';
        progressBar.className = 'progress-bar bg-success';
    }
    
    progressBar.style.width = strength + '%';
    progressBar.textContent = strengthText;
});
</script>

<?php
$extraJS = [];
include __DIR__ . '/views/layout/footer.php';
?>