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
     * Renderiza una vista en formato .html (o .php como fallback) dentro de la plantilla maestra (views/layouts/plantilla.html).
     */
    protected function render($vista, $datos = [], $usarPlantilla = true) {
        extract($datos);

        // Buscar primero la vista en formato .html, luego .php
        $vistaPath = __DIR__ . '/../views/' . $vista . '.html';
        if (!file_exists($vistaPath)) {
            $vistaPath = __DIR__ . '/../views/' . $vista . '.php';
        }

        if (!file_exists($vistaPath)) {
            die("Error: La vista '{$vista}' no fue encontrada en formato .html ni .php en " . __DIR__ . "/../views/{$vista}.html");
        }

        if ($usarPlantilla) {
            // Capturar el contenido procesado de la vista .html
            ob_start();
            require $vistaPath;
            $contenido = ob_get_clean();

            // Buscar la plantilla maestra en formato .html, luego .php
            $plantillaPath = __DIR__ . '/../views/layouts/plantilla.html';
            if (!file_exists($plantillaPath)) {
                $plantillaPath = __DIR__ . '/../views/layouts/plantilla.php';
            }

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
