<?php
require_once __DIR__ . '/BaseController.php';

class HomeController extends BaseController {
    /**
     * Página de bienvenida / Landing page.
     * Renderiza la vista HTML views/home/inicio.html
     */
    public function index() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('catalogo.php');
        }

        $this->render('home/inicio', [
            'titulo' => 'Bienvenido - Catálogo de Productos AP'
        ]);
    }
}
