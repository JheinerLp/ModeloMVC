<?php
require_once __DIR__ . '/../config/database.php';

class Producto {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Obtiene la lista de todos los productos activos ordenados por fecha de inicio descendente.
     */
    public function obtenerActivos() {
        if (!$this->db) {
            throw new Exception("No se pudo conectar a la base de datos MySQL.");
        }
        $query = "SELECT * FROM productos WHERE activo = TRUE ORDER BY fecha_inicio DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna el icono Bootstrap correspondiente a la categoría del producto.
     */
    public static function obtenerIconoCategoria($categoria) {
        $cat = mb_strtolower($categoria ?? '', 'UTF-8');
        if (strpos($cat, 'electr') !== false) return 'bi-laptop';
        if (strpos($cat, 'acces') !== false) return 'bi-headphones';
        if (strpos($cat, 'wear') !== false || strpos($cat, 'reloj') !== false) return 'bi-smartwatch';
        if (strpos($cat, 'foto') !== false || strpos($cat, 'cámara') !== false) return 'bi-camera';
        return 'bi-box-seam';
    }

    /**
     * Calcula métricas de precio (ahorro y porcentaje de descuento).
     */
    public static function calcularMetricas($precioNormal, $precioPromo) {
        $orig = floatval($precioNormal);
        $prom = floatval($precioPromo);
        $ahorro = max(0, $orig - $prom);
        $descuento = ($orig > 0) ? round(($ahorro / $orig) * 100) : 0;
        return [
            'precio_normal' => $orig,
            'precio_promo' => $prom,
            'ahorro' => $ahorro,
            'descuento' => $descuento
        ];
    }
}
