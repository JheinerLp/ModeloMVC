<div class="container py-4">
    <div class="row justify-content-center align-items-center">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center py-3 rounded-top">
                    <h3 class="mb-0"><i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión</h3>
                    <small class="text-white-50">Ingresa tus credenciales</small>
                </div>
                <div class="card-body p-4 p-md-5">
                    
                    <?php if(!empty($mensaje)): ?>
                        <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show d-flex align-items-center" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                            <div><?= $mensaje ?></div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="login.php">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                        <div class="mb-3">
                            <label for="correo" class="form-label fw-semibold">
                                <i class="bi bi-envelope me-1 text-primary"></i>Correo Electrónico
                            </label>
                            <input type="email" class="form-control" id="correo" name="correo" value="<?= htmlspecialchars($correo ?? '') ?>" placeholder="nombre@correo.com" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">
                                <i class="bi bi-lock me-1 text-primary"></i>Contraseña
                            </label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Tu contraseña" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mt-2 shadow-sm">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Entrar
                        </button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="mb-1 text-muted">¿No tienes cuenta? <a href="registro.php" class="fw-bold text-decoration-none">Regístrate aquí</a></p>
                        <p class="mb-0"><a href="index.php" class="text-muted small text-decoration-none"><i class="bi bi-arrow-left"></i> Volver al inicio</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
