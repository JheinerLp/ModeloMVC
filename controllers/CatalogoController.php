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
     * Renderiza views/catalogo/listar.html
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

        $this->render('catalogo/listar', [
            'titulo' => 'Catálogo de Productos - Catálogo AP',
            'productos' => $productos,
            'error_db' => $error_db,
            'usuario_nombre' => $_SESSION['user_name'] ?? 'Usuario'
        ]);
    }

    /**
     * Muestra la vista detallada de un producto específico.
     * Renderiza views/catalogo/detalle.html
     */
    public function detalle() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('login.php');
        }

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $producto = null;
        $error_db = null;

        try {
            $producto = $this->productoModel->obtenerPorId($id);
        } catch (Exception $e) {
            $error_db = $e->getMessage();
        }

        if (!$producto) {
            $this->redirect('catalogo.php');
        }

        $this->render('catalogo/detalle', [
            'titulo' => htmlspecialchars($producto['nombre']) . ' - Ficha de Producto',
            'producto' => $producto,
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
