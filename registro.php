<?php
session_start();
require_once 'config/database.php';

$mensaje = '';
$tipo_mensaje = '';
$errores = [];

// Generar token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Catálogo de ciudades por país (Colombia, Italia, Francia)
$catalogo_ciudades = [
    'Colombia' => [
        'Bogotá', 'Medellin', 'Cali', 'Barranquilla', 'Cartagena',
        'Bucaramanga', 'Pereira', 'Santa Marta', 'Manizales', 'Cúcuta',
        'Pasto', 'Armenia', 'Ibagué', 'Villavicencio'
    ],
    'Italia' => [
        'Roma', 'Milán', 'Nápoles', 'Florencia', 'Venecia',
        'Turín', 'Bolonia', 'Palermo', 'Génova', 'Verona'
    ],
    'Francia' => [
        'París', 'Marsella', 'Lyon', 'Toulouse', 'Niza',
        'Nantes', 'Estrasburgo', 'Montpellier', 'Burdeos', 'Lille'
    ]
];

// Mapa inverso para lookup de ciudad -> país
$mapa_ciudad_pais = [];
foreach ($catalogo_ciudades as $pais_nombre => $ciudades) {
    foreach ($ciudades as $ciu) {
        $mapa_ciudad_pais[mb_strtolower(trim($ciu), 'UTF-8')] = $pais_nombre;
    }
}

// Inicialización de campos para conservar la información en caso de error
$datos = [
    'numero_documento' => '',
    'nombre' => '',
    'apellidos' => '',
    'correo' => '',
    'ciudad' => '',
    'pais' => ''
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validación de seguridad CSRF
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrf_token)) {
        $errores['seguridad'] = 'Error de seguridad: Token CSRF inválido o expirado. Por favor recarga el formulario.';
    }

    // Conservar datos ingresados
    $datos['numero_documento'] = trim($_POST['numero_documento'] ?? '');
    $datos['nombre']           = trim($_POST['nombre'] ?? '');
    $datos['apellidos']        = trim($_POST['apellidos'] ?? '');
    $datos['correo']           = trim($_POST['correo'] ?? '');
    $datos['ciudad']           = trim($_POST['ciudad'] ?? '');
    $datos['pais']             = trim($_POST['pais'] ?? '');
    $password                  = $_POST['password'] ?? '';
    $confirm_password          = $_POST['confirm_password'] ?? '';

    // Autocompletado del país en el backend según la ciudad seleccionada
    $ciudad_normalizada = mb_strtolower($datos['ciudad'], 'UTF-8');
    if (isset($mapa_ciudad_pais[$ciudad_normalizada])) {
        $datos['pais'] = $mapa_ciudad_pais[$ciudad_normalizada];
    }

    // 1. Validaciones campo por campo señalando la ubicación exacta del error
    if (empty($datos['numero_documento'])) {
        $errores['numero_documento'] = 'El número de documento es obligatorio.';
    } elseif (!preg_match('/^[0-9A-Za-z\.\-]{5,20}$/', $datos['numero_documento'])) {
        $errores['numero_documento'] = 'El documento debe tener entre 5 y 20 caracteres válidos.';
    }

    if (empty($datos['nombre'])) {
        $errores['nombre'] = 'El nombre es obligatorio.';
    } elseif (mb_strlen($datos['nombre']) < 2) {
        $errores['nombre'] = 'El nombre debe tener al menos 2 caracteres.';
    }

    if (empty($datos['apellidos'])) {
        $errores['apellidos'] = 'Los apellidos son obligatorios.';
    } elseif (mb_strlen($datos['apellidos']) < 2) {
        $errores['apellidos'] = 'Los apellidos deben tener al menos 2 caracteres.';
    }

    if (empty($datos['correo'])) {
        $errores['correo'] = 'El correo electrónico es obligatorio.';
    } elseif (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
        $errores['correo'] = 'Ingrese un correo electrónico válido (ejemplo: usuario@correo.com).';
    }

    if (empty($datos['ciudad'])) {
        $errores['ciudad'] = 'Debe seleccionar o escribir una ciudad de Colombia, Italia o Francia.';
    } elseif (!isset($mapa_ciudad_pais[$ciudad_normalizada])) {
        $errores['ciudad'] = 'La ciudad ingresada no pertenece al catálogo (Colombia, Italia o Francia).';
    }

    if (empty($datos['pais'])) {
        $errores['pais'] = 'El país es obligatorio (se autocompleta automáticamente según la ciudad).';
    }

    if (empty($password)) {
        $errores['password'] = 'La contraseña es obligatoria.';
    } elseif (strlen($password) < 6) {
        $errores['password'] = 'La contraseña debe tener al menos 6 caracteres.';
    }

    if (empty($confirm_password)) {
        $errores['confirm_password'] = 'Debe confirmar su contraseña.';
    } elseif ($password !== $confirm_password) {
        $errores['confirm_password'] = 'Las contraseñas no coinciden.';
    }

    // 2. Si no hay errores de formato, verificar duplicados en la base de datos
    if (empty($errores)) {
        try {
            $database = new Database();
            $db = $database->getConnection();

            if ($db) {
                // Verificar si el documento o correo ya existen
                $query = "SELECT numero_documento, correo FROM usuarios WHERE numero_documento = :documento OR correo = :correo";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':documento', $datos['numero_documento']);
                $stmt->bindParam(':correo', $datos['correo']);
                $stmt->execute();
                $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($registros as $reg) {
                    if ($reg['numero_documento'] === $datos['numero_documento']) {
                        $errores['numero_documento'] = 'Este número de documento ya se encuentra registrado.';
                    }
                    if (strcasecmp($reg['correo'], $datos['correo']) === 0) {
                        $errores['correo'] = 'Este correo electrónico ya se encuentra registrado.';
                    }
                }

                if (empty($errores)) {
                    // Insertar nuevo usuario
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $query = "INSERT INTO usuarios (numero_documento, nombre, apellidos, correo, ciudad, pais, password) 
                              VALUES (:documento, :nombre, :apellidos, :correo, :ciudad, :pais, :password)";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':documento', $datos['numero_documento']);
                    $stmt->bindParam(':nombre', $datos['nombre']);
                    $stmt->bindParam(':apellidos', $datos['apellidos']);
                    $stmt->bindParam(':correo', $datos['correo']);
                    $stmt->bindParam(':ciudad', $datos['ciudad']);
                    $stmt->bindParam(':pais', $datos['pais']);
                    $stmt->bindParam(':password', $password_hash);

                    if ($stmt->execute()) {
                        $mensaje = '¡Registro exitoso! Ya puedes iniciar sesión con tu nueva cuenta.';
                        $tipo_mensaje = 'success';
                        // Regenerar token CSRF tras registro exitoso
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                        // Limpiar formulario tras registro exitoso
                        $datos = [
                            'numero_documento' => '',
                            'nombre' => '',
                            'apellidos' => '',
                            'correo' => '',
                            'ciudad' => '',
                            'pais' => ''
                        ];
                    } else {
                        $mensaje = 'Error al registrar el usuario en el sistema.';
                        $tipo_mensaje = 'danger';
                    }
                } else {
                    $mensaje = 'Por favor, corrija los datos señalados a continuación.';
                    $tipo_mensaje = 'danger';
                }
            } else {
                $mensaje = 'No fue posible conectar con la base de datos MySQL.';
                $tipo_mensaje = 'danger';
            }
        } catch (Exception $e) {
            $mensaje = 'Error en el sistema: ' . $e->getMessage();
            $tipo_mensaje = 'danger';
        }
    } else {
        $mensaje = isset($errores['seguridad']) ? $errores['seguridad'] : 'Se encontraron errores en el formulario. Los datos ingresados se han conservado para su corrección.';
        $tipo_mensaje = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - Catálogo AP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container py-4">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-7 col-lg-6">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white text-center py-3 rounded-top">
                        <h3 class="mb-1"><i class="bi bi-person-plus-fill me-2"></i>Registro de Usuario</h3>
                        <small class="text-white-50">Diligencia el formulario. Los datos se mantendrán si ocurre algún error.</small>
                    </div>
                    <div class="card-body p-4 p-md-5">

                        <?php if($mensaje): ?>
                            <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show d-flex align-items-center" role="alert">
                                <i class="bi <?php echo ($tipo_mensaje == 'success') ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'; ?> me-2 fs-5"></i>
                                <div><?php echo $mensaje; ?></div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="registro.php" novalidate id="formRegistro">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                            <!-- Número de Documento -->
                            <div class="mb-3">
                                <label for="numero_documento" class="form-label fw-semibold">
                                    <i class="bi bi-card-heading me-1 text-primary"></i>Número de Documento
                                </label>
                                <input type="text" 
                                       class="form-control <?php echo isset($errores['numero_documento']) ? 'is-invalid' : (!empty($datos['numero_documento']) ? 'is-valid' : ''); ?>" 
                                       id="numero_documento" 
                                       name="numero_documento" 
                                       value="<?php echo htmlspecialchars($datos['numero_documento']); ?>" 
                                       placeholder="Ej: 1020304050" 
                                       required>
                                <?php if(isset($errores['numero_documento'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <i class="bi bi-x-circle me-1"></i><?php echo $errores['numero_documento']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Nombres y Apellidos -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nombre" class="form-label fw-semibold">
                                        <i class="bi bi-person me-1 text-primary"></i>Nombre
                                    </label>
                                    <input type="text" 
                                           class="form-control <?php echo isset($errores['nombre']) ? 'is-invalid' : (!empty($datos['nombre']) ? 'is-valid' : ''); ?>" 
                                           id="nombre" 
                                           name="nombre" 
                                           value="<?php echo htmlspecialchars($datos['nombre']); ?>" 
                                           placeholder="Ej: Carlos" 
                                           required>
                                    <?php if(isset($errores['nombre'])): ?>
                                        <div class="invalid-feedback d-block">
                                            <i class="bi bi-x-circle me-1"></i><?php echo $errores['nombre']; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="apellidos" class="form-label fw-semibold">
                                        <i class="bi bi-person me-1 text-primary"></i>Apellidos
                                    </label>
                                    <input type="text" 
                                           class="form-control <?php echo isset($errores['apellidos']) ? 'is-invalid' : (!empty($datos['apellidos']) ? 'is-valid' : ''); ?>" 
                                           id="apellidos" 
                                           name="apellidos" 
                                           value="<?php echo htmlspecialchars($datos['apellidos']); ?>" 
                                           placeholder="Ej: Rodríguez" 
                                           required>
                                    <?php if(isset($errores['apellidos'])): ?>
                                        <div class="invalid-feedback d-block">
                                            <i class="bi bi-x-circle me-1"></i><?php echo $errores['apellidos']; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Correo Electrónico -->
                            <div class="mb-3">
                                <label for="correo" class="form-label fw-semibold">
                                    <i class="bi bi-envelope me-1 text-primary"></i>Correo Electrónico
                                </label>
                                <input type="email" 
                                       class="form-control <?php echo isset($errores['correo']) ? 'is-invalid' : (!empty($datos['correo']) ? 'is-valid' : ''); ?>" 
                                       id="correo" 
                                       name="correo" 
                                       value="<?php echo htmlspecialchars($datos['correo']); ?>" 
                                       placeholder="nombre@correo.com" 
                                       required>
                                <?php if(isset($errores['correo'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <i class="bi bi-x-circle me-1"></i><?php echo $errores['correo']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Ciudad y País (Catálogo y Autocompletado) -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="ciudad" class="form-label fw-semibold">
                                        <i class="bi bi-geo-alt-fill me-1 text-primary"></i>Ciudad
                                        <span class="badge bg-secondary-subtle text-primary border" style="font-size: 0.72rem;">Catálogo</span>
                                    </label>
                                    <input list="lista-ciudades" 
                                           type="text" 
                                           class="form-control <?php echo isset($errores['ciudad']) ? 'is-invalid' : (!empty($datos['ciudad']) ? 'is-valid' : ''); ?>" 
                                           id="ciudad" 
                                           name="ciudad" 
                                           value="<?php echo htmlspecialchars($datos['ciudad']); ?>" 
                                           placeholder="Ej: Roma, Bogotá, París..." 
                                           autocomplete="off" 
                                           required>
                                    <datalist id="lista-ciudades">
                                        <!-- Ciudades Colombia -->
                                        <?php foreach($catalogo_ciudades['Colombia'] as $c): ?>
                                            <option value="<?php echo $c; ?>">Colombia</option>
                                        <?php endforeach; ?>
                                        <!-- Ciudades Italia -->
                                        <?php foreach($catalogo_ciudades['Italia'] as $c): ?>
                                            <option value="<?php echo $c; ?>">Italia</option>
                                        <?php endforeach; ?>
                                        <!-- Ciudades Francia -->
                                        <?php foreach($catalogo_ciudades['Francia'] as $c): ?>
                                            <option value="<?php echo $c; ?>">Francia</option>
                                        <?php endforeach; ?>
                                    </datalist>
                                    <?php if(isset($errores['ciudad'])): ?>
                                        <div class="invalid-feedback d-block">
                                            <i class="bi bi-x-circle me-1"></i><?php echo $errores['ciudad']; ?>
                                        </div>
                                    <?php else: ?>
                                        <small class="text-muted d-block mt-1" style="font-size: 0.8rem;">
                                            Disponibles: Colombia, Italia y Francia
                                        </small>
                                    <?php endif; ?>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="pais" class="form-label fw-semibold">
                                        <i class="bi bi-flag-fill me-1 text-primary"></i>País
                                        <span class="badge bg-success" style="font-size: 0.72rem;">Autocompletado</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control bg-light <?php echo isset($errores['pais']) ? 'is-invalid' : (!empty($datos['pais']) ? 'is-valid' : ''); ?>" 
                                           id="pais" 
                                           name="pais" 
                                           value="<?php echo htmlspecialchars($datos['pais']); ?>" 
                                           placeholder="Se llena automáticamente" 
                                           readonly 
                                           required>
                                    <?php if(isset($errores['pais'])): ?>
                                        <div class="invalid-feedback d-block">
                                            <i class="bi bi-x-circle me-1"></i><?php echo $errores['pais']; ?>
                                        </div>
                                    <?php else: ?>
                                        <small id="pais-helper" class="text-muted d-block mt-1" style="font-size: 0.8rem;">
                                            <?php echo !empty($datos['pais']) ? 'País detectado: ' . htmlspecialchars($datos['pais']) : 'Selecciona una ciudad primero'; ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Contraseñas -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label fw-semibold">
                                        <i class="bi bi-lock me-1 text-primary"></i>Contraseña
                                    </label>
                                    <input type="password" 
                                           class="form-control <?php echo isset($errores['password']) ? 'is-invalid' : ''; ?>" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Mínimo 6 caracteres" 
                                           required>
                                    <?php if(isset($errores['password'])): ?>
                                        <div class="invalid-feedback d-block">
                                            <i class="bi bi-x-circle me-1"></i><?php echo $errores['password']; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label fw-semibold">
                                        <i class="bi bi-shield-check me-1 text-primary"></i>Confirmar Contraseña
                                    </label>
                                    <input type="password" 
                                           class="form-control <?php echo isset($errores['confirm_password']) ? 'is-invalid' : ''; ?>" 
                                           id="confirm_password" 
                                           name="confirm_password" 
                                           placeholder="Repite la contraseña" 
                                           required>
                                    <?php if(isset($errores['confirm_password'])): ?>
                                        <div class="invalid-feedback d-block">
                                            <i class="bi bi-x-circle me-1"></i><?php echo $errores['confirm_password']; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mt-2 shadow-sm">
                                <i class="bi bi-check2-circle me-1"></i> Registrarse
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <p class="mb-0 text-muted">¿Ya tienes una cuenta? <a href="login.php" class="fw-bold text-decoration-none">Inicia sesión aquí</a></p>
                            <p class="mt-2 mb-0"><a href="index.php" class="text-muted small text-decoration-none"><i class="bi bi-arrow-left"></i> Volver al inicio</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script de Autocompletado Ciudad -> País y validación visual -->
    <script>
        // Catálogo de ciudades y sus respectivos países
        const catalogoCiudades = {
            // Colombia
            "bogota": "Colombia", "bogotá": "Colombia",
            "medellin": "Colombia", "medellín": "Colombia",
            "cali": "Colombia",
            "barranquilla": "Colombia",
            "cartagena": "Colombia",
            "bucaramanga": "Colombia",
            "pereira": "Colombia",
            "santa marta": "Colombia",
            "manizales": "Colombia",
            "cucuta": "Colombia", "cúcuta": "Colombia",
            "pasto": "Colombia",
            "armenia": "Colombia",
            "ibague": "Colombia", "ibagué": "Colombia",
            "villavicencio": "Colombia",

            // Italia
            "roma": "Italia",
            "milan": "Italia", "milán": "Italia",
            "napoles": "Italia", "nápoles": "Italia",
            "florencia": "Italia",
            "venecia": "Italia",
            "turin": "Italia", "turín": "Italia",
            "bolonia": "Italia",
            "palermo": "Italia",
            "genova": "Italia", "génova": "Italia",
            "verona": "Italia",

            // Francia
            "paris": "Francia", "parís": "Francia",
            "marsella": "Francia",
            "lyon": "Francia",
            "toulouse": "Francia",
            "niza": "Francia",
            "nantes": "Francia",
            "estrasburgo": "Francia",
            "montpellier": "Francia",
            "burdeos": "Francia",
            "lille": "Francia"
        };

        const flags = {
            "Colombia": "🇨🇴 Colombia",
            "Italia": "🇮🇹 Italia",
            "Francia": "🇫🇷 Francia"
        };

        function normalizarTexto(texto) {
            return (texto || '').toLowerCase()
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "")
                .trim();
        }

        const inputCiudad = document.getElementById('ciudad');
        const inputPais = document.getElementById('pais');
        const paisHelper = document.getElementById('pais-helper');

        function autocompletarPais() {
            const ciudadIngresada = (inputCiudad.value || '').trim();
            const ciudadNormalizada = normalizarTexto(ciudadIngresada);
            const claveDirecta = ciudadIngresada.toLowerCase();

            const paisEncontrado = catalogoCiudades[claveDirecta] || catalogoCiudades[ciudadNormalizada];

            if (paisEncontrado) {
                inputPais.value = paisEncontrado;
                inputPais.classList.remove('is-invalid');
                inputPais.classList.add('is-valid');
                if (paisHelper) {
                    paisHelper.innerHTML = `<span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i>País autocompletado: ${flags[paisEncontrado] || paisEncontrado}</span>`;
                }
            } else if (ciudadIngresada.length > 0) {
                // Si la ciudad no está en el catálogo
                inputPais.value = '';
                inputPais.classList.remove('is-valid');
                if (paisHelper) {
                    paisHelper.innerHTML = `<span class="text-warning"><i class="bi bi-info-circle me-1"></i>Ciudad no reconocida en el catálogo (Col, Ita, Fra)</span>`;
                }
            } else {
                inputPais.value = '';
                inputPais.classList.remove('is-valid', 'is-invalid');
                if (paisHelper) {
                    paisHelper.textContent = 'Selecciona una ciudad primero';
                }
            }
        }

        inputCiudad.addEventListener('input', autocompletarPais);
        inputCiudad.addEventListener('change', autocompletarPais);

        // Ejecutar al cargar la página si ya había una ciudad seleccionada (preservación de datos)
        document.addEventListener('DOMContentLoaded', function() {
            if (inputCiudad.value.trim() !== '') {
                autocompletarPais();
            }

            // Enfocar automáticamente el primer campo con error si existe
            const primerError = document.querySelector('.is-invalid');
            if (primerError) {
                primerError.focus();
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>