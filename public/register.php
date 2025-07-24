<?php
/**
 * Registro de usuarios - register.php
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

// Procesar registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = sanitize($_POST['nombre'] ?? '');
    $apellidos = sanitize($_POST['apellidos'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $rol = sanitize($_POST['rol'] ?? ROLE_VISITOR);
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Verificar token CSRF
    if (!verifyCSRFToken($csrf_token)) {
        $error = 'Token de seguridad inválido.';
    } elseif (empty($nombre) || empty($apellidos) || empty($email) || empty($password)) {
        $error = 'Por favor complete todos los campos obligatorios.';
    } elseif (!isValidEmail($email)) {
        $error = 'El formato del email no es válido.';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Las contraseñas no coinciden.';
    } elseif ($rol === ROLE_ADMIN) {
        $error = 'No se permite registrar usuarios administradores desde este formulario.';
    } elseif (!in_array($rol, [ROLE_ANALYST, ROLE_VISITOR])) {
        $error = 'Rol no válido seleccionado.';
    } else {
        $result = $auth->register($nombre, $apellidos, $email, $password, $rol);
        
        if ($result['success']) {
            redirectWithMessage('/', 'Usuario registrado exitosamente. Ya puede iniciar sesión.', 'success');
        } else {
            $error = $result['message'];
        }
    }
}

$pageTitle = 'Registro de Usuario';

// Capturar contenido
ob_start();
?>

<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="/logo-mechanical-fix.png" alt="Logo" class="img-fluid mb-3" style="max-height: 80px;">
                        <h4 class="card-title">Registro de Usuario</h4>
                        <p class="text-muted"><?= escape(APP_NAME) ?></p>
                    </div>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-triangle"></i>
                            <?= escape($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="/register.php">
                        <?= csrfField() ?>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">
                                    <i class="bi bi-person"></i> Nombre *
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nombre" 
                                       name="nombre" 
                                       value="<?= escape($_POST['nombre'] ?? '') ?>"
                                       required 
                                       maxlength="100"
                                       autofocus>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="apellidos" class="form-label">
                                    <i class="bi bi-person"></i> Apellidos *
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="apellidos" 
                                       name="apellidos" 
                                       value="<?= escape($_POST['apellidos'] ?? '') ?>"
                                       required 
                                       maxlength="100">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope"></i> Correo Electrónico *
                            </label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   value="<?= escape($_POST['email'] ?? '') ?>"
                                   required 
                                   maxlength="150">
                        </div>
                        
                        <div class="mb-3">
                            <label for="rol" class="form-label">
                                <i class="bi bi-shield"></i> Tipo de Usuario *
                            </label>
                            <select class="form-select" id="rol" name="rol" required>
                                <option value="">Seleccione un tipo de usuario</option>
                                <option value="<?= ROLE_ANALYST ?>" <?= ($_POST['rol'] ?? '') === ROLE_ANALYST ? 'selected' : '' ?>>
                                    <?= getRoleName(ROLE_ANALYST) ?> - Puede crear y analizar conceptos
                                </option>
                                <option value="<?= ROLE_VISITOR ?>" <?= ($_POST['rol'] ?? '') === ROLE_VISITOR ? 'selected' : '' ?>>
                                    <?= getRoleName(ROLE_VISITOR) ?> - Solo consulta de información
                                </option>
                            </select>
                            <div class="form-text">
                                <i class="bi bi-info-circle"></i>
                                Los usuarios Administradores son creados únicamente por el sistema.
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock"></i> Contraseña *
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       required 
                                       minlength="6">
                                <div class="form-text">Mínimo 6 caracteres</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">
                                    <i class="bi bi-lock-fill"></i> Confirmar Contraseña *
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="confirm_password" 
                                       name="confirm_password" 
                                       required 
                                       minlength="6">
                            </div>
                        </div>
                        
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-person-plus"></i>
                                Registrar Usuario
                            </button>
                        </div>
                        
                        <div class="text-center">
                            <p class="mb-0">
                                ¿Ya tienes una cuenta? 
                                <a href="/" class="text-decoration-none">
                                    <i class="bi bi-box-arrow-in-right"></i>
                                    Iniciar Sesión
                                </a>
                            </p>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="row text-center">
                        <div class="col-md-6">
                            <div class="card bg-light h-100">
                                <div class="card-body">
                                    <i class="bi bi-person-gear fs-1 text-warning"></i>
                                    <h6 class="mt-2">Analista</h6>
                                    <small class="text-muted">
                                        Crear obras, gestionar conceptos, realizar análisis de precios
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light h-100">
                                <div class="card-body">
                                    <i class="bi bi-eye fs-1 text-info"></i>
                                    <h6 class="mt-2">Visitante</h6>
                                    <small class="text-muted">
                                        Consultar información, ver reportes y avances
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer text-center bg-light">
                    <small class="text-muted">
                        <i class="bi bi-shield-check"></i>
                        Registro seguro con validación de roles
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

<script>
// Validación de contraseñas en tiempo real
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (password !== confirmPassword) {
        this.setCustomValidity('Las contraseñas no coinciden');
    } else {
        this.setCustomValidity('');
    }
});

// Mostrar/ocultar información del rol
document.getElementById('rol').addEventListener('change', function() {
    const selectedRole = this.value;
    // Aquí podrías agregar más información específica sobre cada rol si es necesario
});
</script>

<?php
$content = ob_get_clean();

// Incluir layout
include SRC_PATH . '/views/layout.php';
?>