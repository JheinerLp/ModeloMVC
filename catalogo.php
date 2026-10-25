<?php
session_start();
require_once 'config/database.php';

// Si no ha iniciado sesión, redirigir al login
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$productos = [];
$error_db = null;

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        // Consulta para obtener productos activos
        $query = "SELECT * FROM productos WHERE activo = TRUE ORDER BY fecha_inicio DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch(Exception $e) {
    $error_db = $e->getMessage();
}

// Iconos representativos por categoría para enriquecer la interfaz
function obtenerIconoCategoria($categoria) {
    $cat = mb_strtolower($categoria ?? '', 'UTF-8');
    if (strpos($cat, 'electr') !== false) return 'bi-laptop';
    if (strpos($cat, 'acces') !== false) return 'bi-headphones';
    if (strpos($cat, 'wear') !== false || strpos($cat, 'reloj') !== false) return 'bi-smartwatch';
    if (strpos($cat, 'foto') !== false || strpos($cat, 'cámara') !== false) return 'bi-camera';
    return 'bi-box-seam';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos - Integración CI/CD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-light">
    <!-- Barra de navegación con indicador de versión Git/Jenkins -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="catalogo.php">
                <i class="bi bi-shop me-2"></i>Catálogo de Productos
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <span class="badge bg-warning text-dark py-2 px-3 rounded-pill d-inline-flex align-items-center shadow-sm">
                            <i class="bi bi-git me-1"></i> Git + Jenkins CI/CD v2.0
                        </span>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item me-3">
                        <span class="nav-link text-white d-flex align-items-center">
                            <i class="bi bi-person-circle me-1 fs-5"></i>
                            Hola, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm px-3 rounded-pill" href="logout.php">
                            <i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Banner de notificación de Integración Continua (Requerimiento 4) -->
    <div class="banner-ci border-bottom py-3 shadow-sm mb-4">
        <div class="container d-flex flex-wrap align-items-center justify-content-between">
            <div class="d-flex align-items-center text-muted small py-1">
                <span class="badge bg-success me-2"><i class="bi bi-check2-all me-1"></i>Despliegue Continuo</span>
                <span>Última versión sincronizada con repositorio <strong>Git</strong> y validada por <strong>Jenkins Pipeline</strong></span>
            </div>
            <div class="small text-muted py-1">
                <i class="bi bi-clock-history me-1"></i>Sistema actualizado: <?php echo date('d/m/Y'); ?>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <div class="row align-items-center mb-4">
            <div class="col-md-8">
                <h1 class="h2 fw-bold text-dark mb-1">
                    <i class="bi bi-tags-fill text-primary me-2"></i>Productos en Promoción
                </h1>
                <p class="text-muted mb-0">Explora nuestras ofertas exclusivas. Haz clic en <strong>Ver Detalles</strong> para ver la ficha completa.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <span class="badge bg-primary fs-6 px-3 py-2 rounded-pill shadow-sm">
                    Total disponibles: <?php echo count($productos); ?>
                </span>
            </div>
        </div>
        
        <?php if($error_db): ?>
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
                <div>
                    <strong>Aviso de Base de Datos:</strong> <?php echo htmlspecialchars($error_db); ?>. Asegúrate de tener XAMPP MySQL iniciado.
                </div>
            </div>
        <?php endif; ?>

        <?php if(empty($productos)): ?>
            <div class="card shadow-sm p-5 text-center my-4 border-0">
                <div class="display-1 text-muted mb-3"><i class="bi bi-inbox"></i></div>
                <h4 class="text-secondary">No hay productos en promoción actualmente</h4>
                <p class="text-muted">Por favor, asegúrate de importar el script <code>sql/database.sql</code> en tu base de datos MySQL.</p>
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach($productos as $producto): ?>
                    <?php 
                        $precio_orig = floatval($producto['precio_normal']);
                        $precio_prom = floatval($producto['precio_promo']);
                        $ahorro = max(0, $precio_orig - $precio_prom);
                        $descuento = ($precio_orig > 0) ? round(($ahorro / $precio_orig) * 100) : 0;
                        $iconoCat = obtenerIconoCategoria($producto['categoria'] ?? '');
                        $modalId = "modalDetalle_" . $producto['id'];
                    ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0 product-card position-relative overflow-hidden">
                            <!-- Badge de descuento superior -->
                            <div class="position-absolute top-0 end-0 m-3 z-2">
                                <span class="badge bg-danger fs-6 px-2 py-1 shadow-sm rounded-pill">
                                    <i class="bi bi-lightning-charge-fill me-1"></i><?php echo $descuento; ?>% OFF
                                </span>
                            </div>

                            <div class="card-body p-4 d-flex flex-column">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary-subtle text-primary p-3 rounded-circle me-3">
                                        <i class="bi <?php echo $iconoCat; ?> fs-3"></i>
                                    </div>
                                    <div>
                                        <span class="badge bg-secondary-subtle text-dark border">
                                            <?php echo htmlspecialchars($producto['categoria'] ?? 'General'); ?>
                                        </span>
                                        <h5 class="card-title fw-bold text-dark mb-0 mt-1">
                                            <?php echo htmlspecialchars($producto['nombre']); ?>
                                        </h5>
                                    </div>
                                </div>

                                <p class="card-text text-muted small flex-grow-1">
                                    <?php 
                                        $desc = htmlspecialchars($producto['descripcion'] ?? '');
                                        echo (mb_strlen($desc) > 85) ? mb_substr($desc, 0, 85) . '...' : $desc;
                                    ?>
                                </p>

                                <div class="my-3 p-3 bg-light rounded-3 border">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="text-decoration-line-through text-muted small d-block">
                                                Normal: $<?php echo number_format($precio_orig, 2); ?>
                                            </span>
                                            <span class="h4 text-success fw-bold mb-0">
                                                $<?php echo number_format($precio_prom, 2); ?>
                                            </span>
                                        </div>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            Ahorras $<?php echo number_format($ahorro, 2); ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="text-muted small mb-3">
                                    <i class="bi bi-calendar-event me-1 text-primary"></i>
                                    Válido hasta: <strong><?php echo date('d/m/Y', strtotime($producto['fecha_fin'])); ?></strong>
                                </div>

                                <!-- Botón Ver Detalles con apertura de modal interactivo -->
                                <button type="button" 
                                        class="btn btn-primary w-100 py-2 fw-semibold d-flex align-items-center justify-content-center shadow-sm"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#<?php echo $modalId; ?>">
                                    <i class="bi bi-eye-fill me-2"></i> Ver Detalles
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- MODAL CON INFORMACIÓN ADICIONAL COMPLETA DEL PRODUCTO -->
                    <div class="modal fade" id="<?php echo $modalId; ?>" tabindex="-1" aria-labelledby="<?php echo $modalId; ?>Label" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content border-0 shadow-lg">
                                <div class="modal-header bg-primary text-white">
                                    <div class="d-flex align-items-center">
                                        <i class="bi <?php echo $iconoCat; ?> fs-3 me-2"></i>
                                        <div>
                                            <h5 class="modal-title fw-bold mb-0" id="<?php echo $modalId; ?>Label">
                                                <?php echo htmlspecialchars($producto['nombre']); ?>
                                            </h5>
                                            <small class="text-white-50">Código: PRD-<?php echo str_pad($producto['id'], 4, '0', STR_PAD_LEFT); ?></small>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="row g-4">
                                        <div class="col-md-5 text-center d-flex flex-column justify-content-center align-items-center bg-light rounded-3 p-4 border">
                                            <div class="display-1 text-primary mb-3">
                                                <i class="bi <?php echo $iconoCat; ?>"></i>
                                            </div>
                                            <span class="badge bg-danger fs-6 px-3 py-2 rounded-pill mb-2">
                                                <i class="bi bi-tag-fill me-1"></i> <?php echo $descuento; ?>% de Descuento
                                            </span>
                                            <span class="badge bg-info text-dark px-3 py-2 rounded-pill">
                                                Categoría: <?php echo htmlspecialchars($producto['categoria'] ?? 'General'); ?>
                                            </span>
                                            <div class="mt-3 text-success fw-semibold small">
                                                <i class="bi bi-shield-check me-1"></i> Garantía oficial de 12 meses
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <h6 class="text-uppercase text-muted fw-bold small mb-2">Descripción Completa</h6>
                                            <p class="text-dark fs-6 mb-4">
                                                <?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?>
                                            </p>

                                            <div class="card bg-light border-0 p-3 mb-3">
                                                <div class="row align-items-center">
                                                    <div class="col-6">
                                                        <span class="text-muted text-decoration-line-through d-block small">
                                                            Precio Regular: $<?php echo number_format($precio_orig, 2); ?>
                                                        </span>
                                                        <span class="h3 text-success fw-bold mb-0">
                                                            $<?php echo number_format($precio_prom, 2); ?>
                                                        </span>
                                                    </div>
                                                    <div class="col-6 text-end">
                                                        <span class="badge bg-success fs-6 p-2">
                                                            Ahorro: $<?php echo number_format($ahorro, 2); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <h6 class="text-uppercase text-muted fw-bold small mb-2">Información de la Promoción</h6>
                                            <ul class="list-group list-group-flush small">
                                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                                                    <span class="text-muted"><i class="bi bi-calendar-check me-1 text-primary"></i>Fecha de Inicio:</span>
                                                    <span class="fw-bold"><?php echo !empty($producto['fecha_inicio']) ? date('d/m/Y', strtotime($producto['fecha_inicio'])) : 'Inmediato'; ?></span>
                                                </li>
                                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                                                    <span class="text-muted"><i class="bi bi-calendar-x me-1 text-danger"></i>Fecha Límite:</span>
                                                    <span class="fw-bold text-danger"><?php echo !empty($producto['fecha_fin']) ? date('d/m/Y', strtotime($producto['fecha_fin'])) : 'Hasta agotar existencias'; ?></span>
                                                </li>
                                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                                                    <span class="text-muted"><i class="bi bi-box-seam me-1 text-primary"></i>Estado de Stock:</span>
                                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Disponible en Bodega</span>
                                                </li>
                                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                                                    <span class="text-muted"><i class="bi bi-globe me-1 text-primary"></i>Envíos internacionales:</span>
                                                    <span>Colombia, Italia, Francia</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light">
                                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                                        <i class="bi bi-x-circle me-1"></i> Cerrar
                                    </button>
                                    <button type="button" class="btn btn-success px-4" onclick="agregarAlCarrito('<?php echo addslashes($producto['nombre']); ?>')">
                                        <i class="bi bi-cart-plus me-1"></i> Aprovechar Oferta
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Toast de Notificación interactiva al agregar producto -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
        <div id="toastCarrito" class="toast align-items-center text-bg-success border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastMensaje">
                    <i class="bi bi-check2-circle me-1"></i> ¡Producto agregado al carrito!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Pie de página informativo -->
  
<footer class="py-4 mt-auto border-top" style="background-color: #17365D; color: #FFFFFF;">
    <div class="container text-center">
        <p class="mb-1 fw-bold" style="color: #FFFFFF;">
            Proyecto Catálogo AP - Práctica de DevOps & Integración Continua
        </p>
        <p class="small mb-0" style="color: #E2EAF4;">
            Implementación de Git + Jenkins | Pipeline Automatizado
        </p>
    </div>
</footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function agregarAlCarrito(nombreProducto) {
            const toastElem = document.getElementById('toastCarrito');
            const toastMensaje = document.getElementById('toastMensaje');
            toastMensaje.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i> ¡Oferta de <strong>${nombreProducto}</strong> reservada con éxito!`;
            const toast = new bootstrap.Toast(toastElem);
            toast.show();
        }
    </script>
</body>
</html>