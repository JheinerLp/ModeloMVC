<?php
/**
 * Punto de entrada para Catálogo de Productos (Arquitectura MVC)
 * 
 * Requerimiento 3: Botones "Ver Detalles" con modalDetalle y atributos data-bs-toggle interactivos.
 * Requerimiento 4: Despliegue e integración continua validado con Git y Jenkins CI/CD v2.0.
 * 
 * Controlador: controllers/CatalogoController.php -> listar()
 * Vista: views/catalogo/listar.php
 * Modelo: models/Producto.php
 */
require_once __DIR__ . '/controllers/CatalogoController.php';

$controller = new CatalogoController();
$controller->listar();
