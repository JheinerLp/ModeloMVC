<?php
require_once __DIR__ . '/BaseController.php';

class HomeController extends BaseController {
    /**
     * Página de bienvenida / Landing page.
     */
    public function index() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('catalogo.php');
        }

        $vista = file_exists(__DIR__ . '/../views/home/inicio.php') ? 'home/inicio' : 'home';

        $this->render($vista, [
            'titulo' => 'Bienvenido - Catálogo de Productos'
        ]);
    }
}
