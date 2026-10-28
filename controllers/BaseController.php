<?php

class BaseController {
    public function __construct() {
        $this->iniciarSesion();
    }

    /**
     * Asegura que la sesión esté iniciada.
     */
    protected function iniciarSesion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Renderiza una vista dentro de la plantilla maestra (views/layouts/plantilla.php)
     * separando completamente el contenido HTML del diseño general.
     */
    protected function render($vista, $datos = [], $usarPlantilla = true) {
        extract($datos);

        $vistaPath = __DIR__ . '/../views/' . $vista . '.php';
        if (!file_exists($vistaPath)) {
            die("Error: La vista '{$vista}' no fue encontrada en {$vistaPath}.");
        }

        if ($usarPlantilla) {
            ob_start();
            require $vistaPath;
            $contenido = ob_get_clean();

            $plantillaPath = __DIR__ . '/../views/layouts/plantilla.php';
            if (file_exists($plantillaPath)) {
                require $plantillaPath;
            } else {
                echo $contenido;
            }
        } else {
            require $vistaPath;
        }
    }

    /**
     * Redirecciona a una URL y finaliza la ejecución.
     */
    protected function redirect($url) {
        header("Location: {$url}");
        exit();
    }

    /**
     * Genera o retorna el token CSRF de la sesión actual.
     */
    protected function obtenerCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Valida el token CSRF recibido.
     */
    protected function validarCsrfToken($token) {
        return !empty($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }
}
