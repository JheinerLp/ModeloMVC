<?php
/**
 * Punto de Entrada Público (Public Front Controller)
 * Arquitectura MVC
 */

// Cargar enrutador web
require_once __DIR__ . '/../routes/web.php';

// Despachar la petición
Router::dispatch();
