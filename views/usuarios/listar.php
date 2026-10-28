<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">
                <i class="bi bi-people-fill text-primary me-2"></i>Usuarios Registrados
            </h2>
            <p class="text-muted mb-0">Gestión y visualización de cuentas de usuario en el sistema.</p>
        </div>
        <div>
            <a href="registro.php" class="btn btn-primary shadow-sm">
                <i class="bi bi-person-plus-fill me-1"></i> Nuevo Usuario
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-primary text-white">
                        <tr>
                            <th scope="col" class="py-3 ps-4"># ID</th>
                            <th scope="col" class="py-3">Documento</th>
                            <th scope="col" class="py-3">Nombre Completo</th>
                            <th scope="col" class="py-3">Correo Electrónico</th>
                            <th scope="col" class="py-3">Ciudad</th>
                            <th scope="col" class="py-3">País</th>
                            <th scope="col" class="py-3 pe-4 text-center">Fecha Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($usuarios)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No hay usuarios registrados en el sistema.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($usuarios as $u): ?>
                                <tr>
                                    <td class="ps-4 fw-semibold text-secondary">#<?= htmlspecialchars($u['id']) ?></td>
                                    <td><span class="badge bg-secondary-subtle text-dark border"><?= htmlspecialchars($u['documento'] ?? 'N/A') ?></span></td>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars(($u['nombre'] ?? '') . ' ' . ($u['apellidos'] ?? '')) ?></td>
                                    <td><a href="mailto:<?= htmlspecialchars($u['correo']) ?>" class="text-decoration-none"><?= htmlspecialchars($u['correo']) ?></a></td>
                                    <td><?= htmlspecialchars($u['ciudad'] ?? 'N/A') ?></td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info-emphasis border">
                                            <?= htmlspecialchars($u['pais'] ?? 'N/A') ?>
                                        </span>
                                    </td>
                                    <td class="pe-4 text-center text-muted small">
                                        <?= !empty($u['fecha_registro']) ? date('d/m/Y H:i', strtotime($u['fecha_registro'])) : 'Reciente' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
