<?php
/**
 * Index Page - Landing Page
 * Sistema Web de Análisis de Precios y Programa de Obra
 */

require_once __DIR__ . '/includes/auth.php';

// If user is logged in, redirect to their dashboard
if (isLoggedIn()) {
    $currentUser = getCurrentUser();
    redirectAfterLogin($currentUser['rol']);
    exit;
}

$pageTitle = APP_NAME;
$pageDescription = 'Sistema Web de Análisis de Precios y Programa de Obra - Gestión integral de proyectos de construcción';
$bodyClass = 'landing-page';

include __DIR__ . '/views/layout/header.php';
?>

<!-- Hero Section -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">
                    Sistema de Análisis de Precios y Programa de Obra
                </h1>
                <p class="lead mb-4">
                    Gestione sus proyectos de construcción de manera eficiente con nuestro sistema integral 
                    de análisis de precios, seguimiento de avances y programación de obras.
                </p>
                <div class="d-flex gap-3">
                    <a href="/login.php" class="btn btn-light btn-lg">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Iniciar Sesión
                    </a>
                    <a href="/register.php" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-person-plus"></i>
                        Registrarse
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img src="/logo-mechanical-fix.png" alt="Logo del Sistema" class="img-fluid" style="max-width: 300px;">
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="h3 mb-3">Características Principales</h2>
                <p class="text-muted">Herramientas completas para la gestión de proyectos de construcción</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="feature-card text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-calculator text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h4>Análisis de Precios</h4>
                    <p class="text-muted">
                        Realice análisis detallados de precios unitarios, costos de materiales 
                        y estimaciones precisas para sus proyectos.
                    </p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="feature-card text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-graph-up text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h4>Seguimiento de Avances</h4>
                    <p class="text-muted">
                        Monitoree el progreso de sus obras en tiempo real con indicadores 
                        visuales y reportes automatizados.
                    </p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="feature-card text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-calendar3 text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <h4>Programación de Obras</h4>
                    <p class="text-muted">
                        Planifique y coordine actividades con cronogramas inteligentes 
                        y gestión de recursos optimizada.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Roles Section -->
<section class="roles-section py-5 bg-light">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="h3 mb-3">Roles de Usuario</h2>
                <p class="text-muted">Diferentes niveles de acceso según su función en el proyecto</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="role-card">
                    <div class="role-header bg-danger text-white">
                        <i class="bi bi-shield-check"></i>
                        <h4>Administrador</h4>
                    </div>
                    <div class="role-body">
                        <ul class="list-unstyled">
                            <li><i class="bi bi-check text-success"></i> Gestión completa de usuarios</li>
                            <li><i class="bi bi-check text-success"></i> Acceso a todas las obras</li>
                            <li><i class="bi bi-check text-success"></i> Reportes y análisis avanzados</li>
                            <li><i class="bi bi-check text-success"></i> Configuración del sistema</li>
                            <li><i class="bi bi-check text-success"></i> Auditoría de actividades</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="role-card">
                    <div class="role-header bg-warning text-dark">
                        <i class="bi bi-graph-up"></i>
                        <h4>Analista</h4>
                    </div>
                    <div class="role-body">
                        <ul class="list-unstyled">
                            <li><i class="bi bi-check text-success"></i> Gestión de obras asignadas</li>
                            <li><i class="bi bi-check text-success"></i> Creación y edición de reportes</li>
                            <li><i class="bi bi-check text-success"></i> Seguimiento de avances</li>
                            <li><i class="bi bi-check text-success"></i> Análisis de precios</li>
                            <li><i class="bi bi-check text-success"></i> Programación de actividades</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="role-card">
                    <div class="role-header bg-info text-white">
                        <i class="bi bi-eye"></i>
                        <h4>Visitante</h4>
                    </div>
                    <div class="role-body">
                        <ul class="list-unstyled">
                            <li><i class="bi bi-check text-success"></i> Visualización de programas</li>
                            <li><i class="bi bi-check text-success"></i> Consulta de reportes</li>
                            <li><i class="bi bi-check text-success"></i> Descarga de documentos</li>
                            <li><i class="bi bi-check text-success"></i> Acceso solo lectura</li>
                            <li><i class="bi bi-check text-success"></i> Notificaciones de cambios</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Security Section -->
<section class="security-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="h3 mb-4">
                    <i class="bi bi-shield-lock text-success"></i>
                    Seguridad y Confiabilidad
                </h2>
                <div class="security-features">
                    <div class="security-item mb-3">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Cifrado de datos:</strong> Toda la información se transmite y almacena de forma segura
                    </div>
                    <div class="security-item mb-3">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Autenticación robusta:</strong> Sistema de login con protección contra ataques
                    </div>
                    <div class="security-item mb-3">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Control de acceso:</strong> Permisos granulares según el rol de usuario
                    </div>
                    <div class="security-item mb-3">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Auditoría completa:</strong> Registro de todas las actividades del sistema
                    </div>
                    <div class="security-item mb-3">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Sesiones independientes:</strong> Múltiples usuarios pueden trabajar simultáneamente
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="security-badges">
                    <div class="badge-item">
                        <i class="bi bi-shield-check display-1 text-success"></i>
                        <h5 class="mt-2">Protección SSL</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section bg-primary text-white py-5">
    <div class="container text-center">
        <h2 class="h3 mb-3">¿Listo para optimizar sus proyectos?</h2>
        <p class="lead mb-4">
            Únase a los profesionales que ya confían en nuestro sistema para 
            gestionar sus obras de manera eficiente y segura.
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="/register.php" class="btn btn-light btn-lg">
                <i class="bi bi-person-plus"></i>
                Crear Cuenta Gratis
            </a>
            <a href="/login.php" class="btn btn-outline-light btn-lg">
                <i class="bi bi-box-arrow-in-right"></i>
                Acceder al Sistema
            </a>
        </div>
    </div>
</section>

<style>
.hero-section {
    background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%);
}

.feature-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
    height: 100%;
}

.feature-card:hover {
    transform: translateY(-5px);
}

.role-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
    height: 100%;
}

.role-header {
    padding: 20px;
    text-align: center;
}

.role-header i {
    font-size: 2rem;
    margin-bottom: 10px;
    display: block;
}

.role-body {
    padding: 20px;
}

.role-body li {
    padding: 8px 0;
    border-bottom: 1px solid #f8f9fa;
}

.role-body li:last-child {
    border-bottom: none;
}

.security-item {
    display: flex;
    align-items: flex-start;
}

.security-badges {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.landing-page .navbar {
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.landing-page footer {
    margin-top: 0;
}
</style>

<?php include __DIR__ . '/views/layout/footer.php'; ?>