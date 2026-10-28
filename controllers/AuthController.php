<?php
require_once __DIR__ . '/UsuarioController.php';

/**
 * AuthController actúa como alias de compatibilidad para UsuarioController.
 */
class AuthController extends UsuarioController {
    // Hereda login(), crear(), registro(), logout()
}
