<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h3 class="mb-0">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Iniciar Sesión
                </h3>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="<?= $this->url('login') ?>">
                    <?= $this->csrfField() ?>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope"></i>
                            Email
                        </label>
                        <input type="email" class="form-control" id="email" name="email" required 
                               placeholder="Ingrese su email" value="<?= isset($_POST['email']) ? $this->escape($_POST['email']) : '' ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock"></i>
                            Contraseña
                        </label>
                        <input type="password" class="form-control" id="password" name="password" required 
                               placeholder="Ingrese su contraseña">
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Recordarme
                        </label>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Iniciar Sesión
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-4">
                    <hr>
                    <p class="mb-2">
                        <a href="<?= $this->url('forgot-password') ?>" class="text-decoration-none">
                            ¿Olvidó su contraseña?
                        </a>
                    </p>
                    <p class="mb-0">
                        ¿No tiene una cuenta? 
                        <a href="<?= $this->url('register') ?>" class="text-decoration-none">
                            Regístrese aquí
                        </a>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Demo credentials -->
        <div class="card mt-3 border-info">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle"></i>
                    Credenciales de Demostración
                </h6>
            </div>
            <div class="card-body p-3">
                <small>
                    <strong>Administrador:</strong><br>
                    Email: admin@mechanicalfix.com<br>
                    Contraseña: admin123<br><br>
                    
                    <em>Nota: Esta es una demostración del sistema. En producción, estas credenciales no estarían visibles.</em>
                </small>
            </div>
        </div>
    </div>
</div>