<div class="hero-section bg-primary text-white py-5 mb-5 rounded">
    <div class="row align-items-center">
        <div class="col-lg-6">
            <h1 class="display-4 fw-bold mb-4">
                <i class="bi bi-tools"></i>
                Mechanical FIX
            </h1>
            <p class="lead mb-4">
                Servicio profesional de mecánicos a domicilio. 
                Reparamos tu vehículo donde te encuentres, 
                cuando lo necesites.
            </p>
            <div class="d-flex gap-3 flex-wrap">
                <a href="<?= $this->url('services/request') ?>" class="btn btn-light btn-lg">
                    <i class="bi bi-plus-circle"></i>
                    Solicitar Servicio
                </a>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="<?= $this->url('register') ?>" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-person-plus"></i>
                        Registrarse
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-6 text-center">
            <img src="<?= $this->url('logo-mechanical-fix.png') ?>" alt="Mechanical FIX" class="img-fluid" style="max-height: 300px;">
        </div>
    </div>
</div>

<!-- Services Section -->
<section class="mb-5">
    <h2 class="text-center mb-5">Nuestros Servicios</h2>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-droplet-fill text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title">Cambio de Aceite</h5>
                    <p class="card-text">Cambio profesional de aceite y filtro para mantener tu motor en óptimas condiciones.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-stop-circle text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title">Frenos</h5>
                    <p class="card-text">Revisión, mantenimiento y reparación del sistema de frenos para tu seguridad.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-circle text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title">Llantas</h5>
                    <p class="card-text">Montaje, balanceo y reparación de llantas en el lugar donde te encuentres.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-lightning-charge text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title">Sistema Eléctrico</h5>
                    <p class="card-text">Diagnóstico y reparación del sistema eléctrico de tu vehículo.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-battery-charging text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title">Batería</h5>
                    <p class="card-text">Cambio e instalación de baterías para que nunca te quedes varado.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-search text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title">Diagnóstico</h5>
                    <p class="card-text">Revisión completa de tu vehículo para detectar posibles problemas.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="bg-light py-5 rounded mb-5">
    <div class="container">
        <h2 class="text-center mb-5">¿Por qué elegirnos?</h2>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="bi bi-geo-alt-fill text-primary" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5>Servicio a Domicilio</h5>
                    <p>Vamos hasta donde te encuentres, sin que tengas que moverte.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="bi bi-clock-fill text-primary" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5>Disponibilidad 24/7</h5>
                    <p>Servicio disponible las 24 horas del día, los 7 días de la semana.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="bi bi-award-fill text-primary" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5>Mecánicos Certificados</h5>
                    <p>Nuestros mecánicos están certificados y tienen amplia experiencia.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="bi bi-shield-check-fill text-primary" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5>Garantía</h5>
                    <p>Todos nuestros servicios incluyen garantía para tu tranquilidad.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How it works -->
<section class="mb-5">
    <h2 class="text-center mb-5">¿Cómo funciona?</h2>
    <div class="row g-4">
        <div class="col-md-3">
            <div class="text-center">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <span class="h3 mb-0">1</span>
                </div>
                <h5>Solicita</h5>
                <p>Llena el formulario con los datos de tu vehículo y describe el problema.</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-center">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <span class="h3 mb-0">2</span>
                </div>
                <h5>Asignamos</h5>
                <p>Asignamos un mecánico certificado disponible en tu zona.</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-center">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <span class="h3 mb-0">3</span>
                </div>
                <h5>Reparamos</h5>
                <p>El mecánico llega a tu ubicación y realiza el servicio requerido.</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-center">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <span class="h3 mb-0">4</span>
                </div>
                <h5>Listo</h5>
                <p>Tu vehículo queda reparado y funcionando perfectamente.</p>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="bg-primary text-white py-5 rounded text-center">
    <h2 class="mb-4">¿Necesitas ayuda con tu vehículo?</h2>
    <p class="lead mb-4">No esperes más, solicita nuestro servicio profesional ahora mismo.</p>
    <a href="<?= $this->url('services/request') ?>" class="btn btn-light btn-lg">
        <i class="bi bi-telephone"></i>
        Solicitar Servicio Ahora
    </a>
</section>