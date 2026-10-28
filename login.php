<?php
/**
 * Punto de entrada para Inicio de Sesión (Arquitectura MVC)
 * Despacha la petición a UsuarioController::login()
 * 
 * Controlador: controllers/UsuarioController.php
 * Vista: views/usuarios/login.php
 * Modelo: models/Usuario.php
 */
require_once __DIR__ . '/controllers/UsuarioController.php';

$controller = new UsuarioController();
$controller->login();
