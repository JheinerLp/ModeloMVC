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
     * Renderiza una vista exclusivamente en formato .html dentro de la plantilla maestra (views/layouts/plantilla.html).
     */
    protected function render($vista, $datos = [], $usarPlantilla = true) {
        extract($datos);

        // En la arquitectura MVC las vistas son exclusivamente archivos .html
        $vistaPath = __DIR__ . '/../views/' . $vista . '.html';

        if (!file_exists($vistaPath)) {
            die("Error MVC: La vista '{$vista}' no fue encontrada en formato .html en " . __DIR__ . "/../views/{$vista}.html");
        }

        if ($usarPlantilla) {
            // Capturar el contenido renderizado de la vista .html
            ob_start();
            require $vistaPath;
            $contenido = ob_get_clean();

            // Cargar la plantilla maestra .html
            $plantillaPath = __DIR__ . '/../views/layouts/plantilla.html';
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
