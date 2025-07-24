<?php
/**
 * Unauthorized Access Page
 * Sistema Web de Análisis de Precios y Programa de Obra
 */

require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Acceso No Autorizado - ' . APP_NAME;
$pageDescription = 'No tiene permisos para acceder a esta página';
$bodyClass = 'error-page';

include __DIR__ . '/views/layout/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="error-container">
                <div class="error-icon mb-4">
                    <i class="bi bi-shield-exclamation text-danger" style="font-size: 6rem;"></i>
                </div>
                
                <h1 class="error-code text-danger">403</h1>
                <h2 class="error-title">Acceso No Autorizado</h2>
                
                <p class="error-message text-muted mb-4">
                    No tiene permisos suficientes para acceder a esta página. 
                    Su rol actual no permite realizar esta acción.
                </p>
                
                <?php if (isLoggedIn()): ?>
                    <?php $user = getCurrentUser(); ?>
                    <div class="user-info mb-4">
                        <div class="alert alert-info">
                            <strong>Usuario actual:</strong> <?php echo Security::escape($user['nombre']); ?><br>
                            <strong>Rol:</strong> 
                            <span class="badge bg-<?php 
                                echo $user['rol'] === 'admin' ? 'danger' : 
                                    ($user['rol'] === 'analista' ? 'warning' : 'info'); 
                            ?>">
                                <?php echo ucfirst($user['rol']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="actions">
                        <a href="javascript:history.back()" class="btn btn-secondary me-2">
                            <i class="bi bi-arrow-left"></i>
                            Volver
                        </a>
                        
                        <?php
                        // Redirect to appropriate dashboard based on role
                        $dashboardUrl = '/';
                        switch ($user['rol']) {
                            case 'admin':
                                $dashboardUrl = '/admin/usuarios.php';
                                break;
                            case 'analista':
                                $dashboardUrl = '/analista/avance.php';
                                break;
                            case 'visitante':
                                $dashboardUrl = '/visitante/programa.php';
                                break;
                        }
                        ?>
                        
                        <a href="<?php echo $dashboardUrl; ?>" class="btn btn-primary">
                            <i class="bi bi-house"></i>
                            Ir al Panel Principal
                        </a>
                    </div>
                <?php else: ?>
                    <div class="actions">
                        <a href="/login.php" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Iniciar Sesión
                        </a>
                    </div>
                <?php endif; ?>
                
                <!-- Help Information -->
                <div class="help-info mt-5">
                    <h5>¿Necesita acceso a esta funcionalidad?</h5>
                    <p class="text-muted small">
                        Contacte al administrador del sistema para solicitar los permisos necesarios.
                    </p>
                    
                    <div class="role-permissions mt-4">
                        <h6>Permisos por rol:</h6>
                        <div class="row text-start">
                            <div class="col-md-4">
                                <div class="permission-card">
                                    <h6 class="text-danger">
                                        <i class="bi bi-shield-check"></i>
                                        Administrador
                                    </h6>
                                    <ul class="small text-muted">
                                        <li>Gestión de usuarios</li>
                                        <li>Todas las obras</li>
                                        <li>Reportes completos</li>
                                        <li>Configuración</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="permission-card">
                                    <h6 class="text-warning">
                                        <i class="bi bi-graph-up"></i>
                                        Analista
                                    </h6>
                                    <ul class="small text-muted">
                                        <li>Gestión de obras</li>
                                        <li>Reportes asignados</li>
                                        <li>Seguimiento avances</li>
                                        <li>Análisis precios</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="permission-card">
                                    <h6 class="text-info">
                                        <i class="bi bi-eye"></i>
                                        Visitante
                                    </h6>
                                    <ul class="small text-muted">
                                        <li>Vista de programas</li>
                                        <li>Consulta reportes</li>
                                        <li>Solo lectura</li>
                                        <li>Descargas básicas</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.error-container {
    padding: 50px 0;
}

.error-code {
    font-size: 8rem;
    font-weight: bold;
    line-height: 1;
    margin-bottom: 20px;
}

.error-title {
    font-size: 2rem;
    margin-bottom: 20px;
    color: var(--dark-color);
}

.error-message {
    font-size: 1.1rem;
    line-height: 1.6;
}

.permission-card {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 15px;
    border-left: 4px solid var(--primary-color);
}

.permission-card h6 {
    margin-bottom: 10px;
}

.permission-card ul {
    margin-bottom: 0;
    padding-left: 15px;
}

.permission-card li {
    margin-bottom: 5px;
}
</style>

<?php include __DIR__ . '/views/layout/footer.php'; ?>