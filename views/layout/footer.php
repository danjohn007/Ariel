    </main>
    
    <!-- Footer -->
    <footer class="bg-light mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><?php echo htmlspecialchars(APP_NAME); ?></h5>
                    <p class="text-muted">Sistema de análisis de precios y programa de obra</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted">Versión <?php echo htmlspecialchars(APP_VERSION); ?></p>
                    <?php if (isLoggedIn()): ?>
                        <p class="text-muted small">
                            Sesión iniciada como: <?php echo htmlspecialchars($_SESSION['rol'] ?? ''); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="/public/js/main.js"></script>
    
    <?php if (isset($extraJS)): ?>
        <?php foreach ($extraJS as $js): ?>
            <script src="<?php echo htmlspecialchars($js); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- CSRF Token for AJAX requests -->
    <?php if (isLoggedIn()): ?>
        <script>
            window.csrfToken = '<?php echo Security::generateCSRFToken(); ?>';
        </script>
    <?php endif; ?>
</body>
</html>