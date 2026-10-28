<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController extends BaseController {
    private $usuarioModel;

    public function __construct() {
        parent::__construct();
        $this->usuarioModel = new Usuario();
    }

    /**
     * Muestra la lista de usuarios registrados.
     */
    public function listar() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('login.php');
        }

        $usuarios = $this->usuarioModel->listarTodos();

        $this->render('usuarios/listar', [
            'titulo' => 'Lista de Usuarios - Catálogo AP',
            'usuarios' => $usuarios
        ]);
    }

    /**
     * Muestra el formulario para crear/registrar un nuevo usuario y procesa el POST.
     */
    public function crear() {
        $this->obtenerCsrfToken();
        $mensaje = '';
        $tipo_mensaje = '';
        $errores = [];

        $datos = [
            'documento' => '',
            'nombre' => '',
            'apellidos' => '',
            'correo' => '',
            'ciudad' => '',
            'pais' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf_token = $_POST['csrf_token'] ?? '';
            if (!$this->validarCsrfToken($csrf_token)) {
                $errores['seguridad'] = 'Error de seguridad: Token CSRF inválido o expirado. Por favor recarga el formulario.';
            }

            // Capturar datos y preservar ante errores
            $datos['documento'] = trim($_POST['documento'] ?? '');
            $datos['nombre']    = trim($_POST['nombre'] ?? '');
            $datos['apellidos'] = trim($_POST['apellidos'] ?? '');
            $datos['correo']    = trim($_POST['correo'] ?? '');
            $datos['ciudad']    = trim($_POST['ciudad'] ?? '');
            $datos['pais']      = trim($_POST['pais'] ?? '');
            $password           = $_POST['password'] ?? '';
            $confirm_password   = $_POST['confirm_password'] ?? '';

            // Autocompletar país en backend según catálogo de ciudades
            $paisDetectado = Usuario::obtenerPaisPorCiudad($datos['ciudad']);
            if ($paisDetectado) {
                $datos['pais'] = $paisDetectado;
            }

            // Validar formato y restricciones
            $erroresFormato = Usuario::validarRegistro($datos, $password, $confirm_password);
            $errores = array_merge($errores, $erroresFormato);

            // Verificar duplicados si el formato es válido
            if (empty($errores)) {
                try {
                    $existentes = $this->usuarioModel->buscarPorDocumentoOCorreo($datos['documento'], $datos['correo']);
                    foreach ($existentes as $reg) {
                        if ($reg['documento'] === $datos['documento']) {
                            $errores['documento'] = 'Este número de documento ya se encuentra registrado.';
                        }
                        if (strcasecmp($reg['correo'], $datos['correo']) === 0) {
                            $errores['correo'] = 'Este correo electrónico ya se encuentra registrado.';
                        }
                    }

                    if (empty($errores)) {
                        $exito = $this->usuarioModel->registrar($datos, $password);
                        if ($exito) {
                            $mensaje = '¡Registro exitoso! Ya puedes iniciar sesión con tu nueva cuenta.';
                            $tipo_mensaje = 'success';
                            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                            $datos = [
                                'documento' => '',
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
                } catch (Exception $e) {
                    $mensaje = 'Error en el sistema: ' . $e->getMessage();
                    $tipo_mensaje = 'danger';
                }
            } else {
                $mensaje = isset($errores['seguridad']) ? $errores['seguridad'] : 'Se encontraron errores en el formulario. Los datos ingresados se han conservado para su corrección.';
                $tipo_mensaje = 'danger';
            }
        }

        $catalogo_ciudades = Usuario::getCatalogoCiudades();

        $vista = file_exists(__DIR__ . '/../views/usuarios/crear.php') ? 'usuarios/crear' : 'registro';

        $this->render($vista, [
            'titulo' => 'Registro de Usuario - Catálogo AP',
            'mensaje' => $mensaje,
            'tipo_mensaje' => $tipo_mensaje,
            'errores' => $errores,
            'datos' => $datos,
            'catalogo_ciudades' => $catalogo_ciudades
        ]);
    }

    /**
     * Alias para registro de usuario.
     */
    public function registro() {
        $this->crear();
    }

    /**
     * Gestión del Inicio de Sesión.
     */
    public function login() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('catalogo.php');
        }

        $this->obtenerCsrfToken();
        $mensaje = '';
        $tipo_mensaje = '';
        $correo = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf_token = $_POST['csrf_token'] ?? '';
            if (!$this->validarCsrfToken($csrf_token)) {
                $mensaje = 'Error de seguridad: Token CSRF inválido o expirado. Por favor intenta de nuevo.';
                $tipo_mensaje = 'danger';
            } else {
                $correo = trim($_POST['correo'] ?? '');
                $password = $_POST['password'] ?? '';

                if (empty($correo) || empty($password)) {
                    $mensaje = 'Todos los campos son obligatorios.';
                    $tipo_mensaje = 'danger';
                } else {
                    try {
                        $user = $this->usuarioModel->buscarPorCorreo($correo);

                        if ($user) {
                            if (password_verify($password, $user['password'])) {
                                session_regenerate_id(true);
                                $_SESSION['user_id'] = $user['id'];
                                $_SESSION['user_name'] = $user['nombre'] . ' ' . $user['apellidos'];
                                $_SESSION['user_email'] = $user['correo'];
                                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                                $this->redirect('catalogo.php');
                            } else {
                                $mensaje = 'Contraseña incorrecta. Por favor intente de nuevo.';
                                $tipo_mensaje = 'danger';
                            }
                        } else {
                            $mensaje = 'No existe una cuenta registrada con este correo electrónico.';
                            $tipo_mensaje = 'danger';
                        }
                    } catch (Exception $e) {
                        $mensaje = 'Error del sistema: ' . $e->getMessage();
                        $tipo_mensaje = 'danger';
                    }
                }
            }
        }

        $vista = file_exists(__DIR__ . '/../views/usuarios/login.php') ? 'usuarios/login' : 'login';

        $this->render($vista, [
            'titulo' => 'Iniciar Sesión - Catálogo AP',
            'mensaje' => $mensaje,
            'tipo_mensaje' => $tipo_mensaje,
            'correo' => $correo
        ]);
    }

    /**
     * Muestra el formulario para editar datos de usuario.
     */
    public function editar() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('login.php');
        }

        $user = $this->usuarioModel->buscarPorCorreo($_SESSION['user_email'] ?? '');

        $this->render('usuarios/editar', [
            'titulo' => 'Editar Perfil - Catálogo AP',
            'user' => $user
        ]);
    }

    /**
     * Cierre de sesión seguro.
     */
    public function logout() {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
        $this->redirect('index.php');
    }
}
