<?php
/**
 * Definición de Rutas Web del Sistema (Arquitectura MVC)
 * Asocia rutas URI y métodos HTTP con su respectivo Controlador y Método.
 */

class Router {
    public static function getRoutes() {
        return [
            'GET' => [
                ''                  => ['HomeController', 'index'],
                'home'              => ['HomeController', 'index'],
                'login'             => ['UsuarioController', 'login'],
                'registro'          => ['UsuarioController', 'crear'],
                'usuarios'          => ['UsuarioController', 'listar'],
                'usuarios/crear'    => ['UsuarioController', 'crear'],
                'usuarios/listar'   => ['UsuarioController', 'listar'],
                'usuarios/editar'   => ['UsuarioController', 'editar'],
                'catalogo'          => ['CatalogoController', 'listar'],
                'logout'            => ['UsuarioController', 'logout'],
            ],
            'POST' => [
                'login'             => ['UsuarioController', 'login'],
                'registro'          => ['UsuarioController', 'crear'],
                'usuarios/crear'    => ['UsuarioController', 'crear'],
            ]
        ];
    }

    /**
     * Resuelve y despacha la petición actual al controlador correspondiente.
     */
    public static function dispatch() {
        $routes = self::getRoutes();
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_GET['route'] ?? $_GET['action'] ?? $_GET['a'] ?? '';
        $uri = trim($uri, '/');

        if (isset($routes[$method][$uri])) {
            list($controllerName, $actionName) = $routes[$method][$uri];
            $controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                $controller = new $controllerName();
                $controller->$actionName();
                return;
            }
        }

        // Si no coincide o es la raíz, ir a HomeController@index
        require_once __DIR__ . '/../controllers/HomeController.php';
        $controller = new HomeController();
        $controller->index();
    }
}

return Router::getRoutes();
