<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Producto.php';

class CatalogoController extends BaseController {
    private $productoModel;

    public function __construct() {
        parent::__construct();
        $this->productoModel = new Producto();
    }

    /**
     * Muestra el catálogo de productos con promociones y modales interactivos.
     */
    public function listar() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('login.php');
        }

        $productos = [];
        $error_db = null;

        try {
            $productos = $this->productoModel->obtenerActivos();
        } catch (Exception $e) {
            $error_db = $e->getMessage();
        }

        $vista = file_exists(__DIR__ . '/../views/catalogo/listar.php') ? 'catalogo/listar' : 'catalogo';

        $this->render($vista, [
            'titulo' => 'Catálogo de Productos - Catálogo AP',
            'productos' => $productos,
            'error_db' => $error_db,
            'usuario_nombre' => $_SESSION['user_name'] ?? 'Usuario'
        ]);
    }

    /**
     * Alias para compatibilidad con index().
     */
    public function index() {
        $this->listar();
    }
}
