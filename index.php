<?php
session_start();
if(isset($_SESSION['user_id'])) {
    header("Location: catalogo.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - Catálogo de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-7 text-center">
                <div class="card p-5 shadow-lg border-0 bg-white">
                    <div class="mb-3">
                        <span class="badge bg-primary-subtle text-primary border px-3 py-2 rounded-pill">
                            <i class="bi bi-git me-1"></i> Git + Jenkins CI/CD Integrado v2.0
                        </span>
                    </div>
                    <h1 class="display-5 fw-bold mb-3 text-dark">Catálogo de Productos en Promoción</h1>
                    <p class="lead text-muted mb-4">Descubre las mejores ofertas en tecnología y accesorios con entrega a Colombia, Italia y Francia.</p>
                    <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                        <a href="login.php" class="btn btn-primary btn-lg px-4 shadow-sm">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
                        </a>
                        <a href="registro.php" class="btn btn-outline-primary btn-lg px-4">
                            <i class="bi bi-person-plus me-1"></i> Registrarse
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>