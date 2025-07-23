<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h3 class="mb-0">
                    <i class="bi bi-person-plus"></i>
                    Crear Cuenta
                </h3>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="<?= $this->url('register') ?>">
                    <?= $this->csrfField() ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">
                                <i class="bi bi-person"></i>
                                Nombre *
                            </label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required 
                                   placeholder="Ingrese su nombre" value="<?= isset($_POST['first_name']) ? $this->escape($_POST['first_name']) : '' ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">
                                <i class="bi bi-person"></i>
                                Apellidos *
                            </label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required 
                                   placeholder="Ingrese sus apellidos" value="<?= isset($_POST['last_name']) ? $this->escape($_POST['last_name']) : '' ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope"></i>
                            Email *
                        </label>
                        <input type="email" class="form-control" id="email" name="email" required 
                               placeholder="Ingrese su email" value="<?= isset($_POST['email']) ? $this->escape($_POST['email']) : '' ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">
                            <i class="bi bi-telephone"></i>
                            Teléfono
                        </label>
                        <input type="tel" class="form-control" id="phone" name="phone" 
                               placeholder="Ingrese su teléfono" value="<?= isset($_POST['phone']) ? $this->escape($_POST['phone']) : '' ?>">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">
                                <i class="bi bi-lock"></i>
                                Contraseña *
                            </label>
                            <input type="password" class="form-control" id="password" name="password" required 
                                   placeholder="Ingrese su contraseña" minlength="6">
                            <div class="form-text">Mínimo 6 caracteres</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="password_confirm" class="form-label">
                                <i class="bi bi-lock"></i>
                                Confirmar Contraseña *
                            </label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required 
                                   placeholder="Confirme su contraseña" minlength="6">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">
                            <i class="bi bi-geo-alt"></i>
                            Dirección
                        </label>
                        <textarea class="form-control" id="address" name="address" rows="2" 
                                  placeholder="Ingrese su dirección completa"><?= isset($_POST['address']) ? $this->escape($_POST['address']) : '' ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">
                                <i class="bi bi-building"></i>
                                Ciudad
                            </label>
                            <input type="text" class="form-control" id="city" name="city" 
                                   placeholder="Ciudad" value="<?= isset($_POST['city']) ? $this->escape($_POST['city']) : '' ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="zip_code" class="form-label">
                                <i class="bi bi-mailbox"></i>
                                Código Postal
                            </label>
                            <input type="text" class="form-control" id="zip_code" name="zip_code" 
                                   placeholder="CP" value="<?= isset($_POST['zip_code']) ? $this->escape($_POST['zip_code']) : '' ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">
                            Acepto los <a href="#" target="_blank">términos y condiciones</a> *
                        </label>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="newsletter" name="newsletter">
                        <label class="form-check-label" for="newsletter">
                            Deseo recibir novedades y promociones por email
                        </label>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-person-plus"></i>
                            Crear Cuenta
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-4">
                    <hr>
                    <p class="mb-0">
                        ¿Ya tiene una cuenta? 
                        <a href="<?= $this->url('login') ?>" class="text-decoration-none">
                            Inicie sesión aquí
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validate password confirmation
document.getElementById('password_confirm').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirm = this.value;
    
    if (password !== confirm) {
        this.setCustomValidity('Las contraseñas no coinciden');
    } else {
        this.setCustomValidity('');
    }
});
</script>