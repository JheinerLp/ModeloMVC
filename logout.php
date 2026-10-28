<?php
/**
 * Punto de entrada para Cierre de Sesión (Arquitectura MVC)
 * Despacha la petición a UsuarioController::logout()
 * 
 * Controlador: controllers/UsuarioController.php -> logout()
 */
require_once __DIR__ . '/controllers/UsuarioController.php';

$controller = new UsuarioController();
$controller->logout();
