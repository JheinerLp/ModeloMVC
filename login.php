<?php
session_start();
require_once 'config/database.php';

if(isset($_SESSION['user_id'])) {
    header("Location: catalogo.php");
    exit();
}

$mensaje = '';
$tipo_mensaje = '';
$correo = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if(empty($correo) || empty($password)) {
        $mensaje = 'Todos los campos son obligatorios.';
        $tipo_mensaje = 'danger';
    } else {
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            if ($db) {
                $query = "SELECT id, numero_documento, nombre, apellidos, correo, password FROM usuarios WHERE correo = :correo";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':correo', $correo);
                $stmt->execute();
                
                if($stmt->rowCount() > 0) {
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if(password_verify($password, $user['password'])) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['nombre'] . ' ' . $user['apellidos'];
                        $_SESSION['user_email'] = $user['correo'];
                        header("Location: catalogo.php");
                        exit();
                    } else {
                        $mensaje = 'Contraseña incorrecta. Por favor intente de nuevo.';
                        $tipo_mensaje = 'danger';
                    }
                } else {
                    $mensaje = 'No existe una cuenta registrada con este correo electrónico.';
                    $tipo_mensaje = 'danger';
                }
            } else {
                $mensaje = 'No se pudo conectar a la base de datos MySQL.';
                $tipo_mensaje = 'danger';
            }
        } catch(Exception $e) {
            $mensaje = 'Error del sistema: ' . $e->getMessage();
            $tipo_mensaje = 'danger';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Catálogo AP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5 col-lg-4">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white text-center py-3 rounded-top">
                        <h3 class="mb-0"><i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión</h3>
                        <small class="text-white-50">Ingresa tus credenciales</small>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        
                        <?php if($mensaje): ?>
                            <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show d-flex align-items-center" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                                <div><?php echo $mensaje; ?></div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="login.php">
                            <div class="mb-3">
                                <label for="correo" class="form-label fw-semibold">
                                    <i class="bi bi-envelope me-1 text-primary"></i>Correo Electrónico
                                </label>
                                <input type="email" class="form-control" id="correo" name="correo" value="<?php echo htmlspecialchars($correo); ?>" placeholder="nombre@correo.com" required>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>