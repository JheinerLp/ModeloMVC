<?php
/**
 * Punto de entrada para Registro de Usuario (Arquitectura MVC)
 * 
 * Requerimiento 1: Persistencia de $datos y señalización visual de errores con is-invalid e invalid-feedback.
 * Requerimiento 2: Catálogo de ciudades para Colombia, Italia y Francia con autocompletado reactivo de país.
 * 
 * Controlador: controllers/UsuarioController.php -> crear()
 * Vista: views/usuarios/crear.php
 * Modelo: models/Usuario.php
 */
require_once __DIR__ . '/controllers/UsuarioController.php';

$controller = new UsuarioController();
$controller->crear();
