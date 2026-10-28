<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h3 class="mb-0"><i class="bi bi-person-gear me-2"></i>Perfil de Usuario</h3>
                </div>
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="display-3 text-primary"><i class="bi bi-person-circle"></i></div>
                        <h4 class="fw-bold mb-0"><?= htmlspecialchars(($user['nombre'] ?? '') . ' ' . ($user['apellidos'] ?? '')) ?></h4>
                        <p class="text-muted"><?= htmlspecialchars($user['correo'] ?? '') ?></p>
                    </div>

                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="bi bi-card-heading me-1"></i>Documento:</span>
                            <span class="fw-semibold"><?= htmlspecialchars($user['documento'] ?? 'No especificado') ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="bi bi-geo-alt-fill me-1"></i>Ciudad:</span>
                            <span class="fw-semibold"><?= htmlspecialchars($user['ciudad'] ?? 'No especificado') ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="bi bi-flag-fill me-1"></i>País:</span>
                            <span class="badge bg-success"><?= htmlspecialchars($user['pais'] ?? 'No especificado') ?></span>
                        </li>
                    </ul>

                    <div class="d-grid gap-2">
                        <a href="catalogo.php" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left me-1"></i> Volver al Catálogo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
