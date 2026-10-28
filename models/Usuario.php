<?php
require_once __DIR__ . '/../config/database.php';

class Usuario {
    private $db;

    // Catálogo de ciudades por país (Colombia, Italia, Francia)
    public static $catalogo_ciudades = [
        'Colombia' => [
            'Bogotá', 'Medellin', 'Cali', 'Barranquilla', 'Cartagena',
            'Bucaramanga', 'Pereira', 'Santa Marta', 'Manizales', 'Cúcuta',
            'Pasto', 'Armenia', 'Ibagué', 'Villavicencio'
        ],
        'Italia' => [
            'Roma', 'Milán', 'Nápoles', 'Florencia', 'Venecia',
            'Turín', 'Bolonia', 'Palermo', 'Génova', 'Verona'
        ],
        'Francia' => [
            'París', 'Marsella', 'Lyon', 'Toulouse', 'Niza',
            'Nantes', 'Estrasburgo', 'Montpellier', 'Burdeos', 'Lille'
        ]
    ];

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Retorna el catálogo completo de ciudades agrupadas por país.
     */
    public static function getCatalogoCiudades() {
        return self::$catalogo_ciudades;
    }

    /**
     * Genera un mapa inverso para resolver País a partir del nombre de la Ciudad.
     */
    public static function getMapaCiudadPais() {
        $mapa = [];
        foreach (self::$catalogo_ciudades as $pais => $ciudades) {
            foreach ($ciudades as $ciudad) {
                $normalizada = mb_strtolower(trim($ciudad), 'UTF-8');
                $mapa[$normalizada] = $pais;
            }
        }
        return $mapa;
    }

    /**
     * Obtiene el país asociado a una ciudad si existe en el catálogo.
     */
    public static function obtenerPaisPorCiudad($ciudad) {
        $mapa = self::getMapaCiudadPais();
        $normalizada = mb_strtolower(trim($ciudad), 'UTF-8');
        return $mapa[$normalizada] ?? null;
    }

    /**
     * Busca un usuario por su correo electrónico.
     */
    public function buscarPorCorreo($correo) {
        if (!$this->db) return null;
        $query = "SELECT id, documento, nombre, apellidos, correo, password FROM usuarios WHERE correo = :correo LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Busca usuarios existentes por documento o correo para validar duplicados.
     */
    public function buscarPorDocumentoOCorreo($documento, $correo) {
        if (!$this->db) return [];
        $query = "SELECT documento, correo FROM usuarios WHERE documento = :documento OR correo = :correo";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':documento', $documento);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lista todos los usuarios registrados (útil para vistas/usuarios/listar.php).
     */
    public function listarTodos() {
        if (!$this->db) return [];
        $query = "SELECT id, documento, nombre, apellidos, correo, ciudad, pais, fecha_registro FROM usuarios ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Registra un nuevo usuario en la base de datos con contraseña cifrada.
     */
    public function registrar($datos, $password) {
        if (!$this->db) return false;
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO usuarios (documento, nombre, apellidos, correo, ciudad, pais, password) 
                  VALUES (:documento, :nombre, :apellidos, :correo, :ciudad, :pais, :password)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':documento', $datos['documento']);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':apellidos', $datos['apellidos']);
        $stmt->bindParam(':correo', $datos['correo']);
        $stmt->bindParam(':ciudad', $datos['ciudad']);
        $stmt->bindParam(':pais', $datos['pais']);
        $stmt->bindParam(':password', $password_hash);
        return $stmt->execute();
    }

    /**
     * Valida los campos ingresados en el formulario de registro.
     * Retorna array asociativo con los errores detectados por campo.
     */
    public static function validarRegistro($datos, $password, $confirm_password) {
        $errores = [];

        // Validación de Documento
        if (empty($datos['documento'])) {
            $errores['documento'] = 'El número de documento es obligatorio.';
        } elseif (!preg_match('/^[0-9A-Za-z\.\-]{5,20}$/', $datos['documento'])) {
            $errores['documento'] = 'El documento debe tener entre 5 y 20 caracteres válidos.';
        }

        // Validación de Nombre
        if (empty($datos['nombre'])) {
            $errores['nombre'] = 'El nombre es obligatorio.';
        } elseif (mb_strlen($datos['nombre']) < 2) {
            $errores['nombre'] = 'El nombre debe tener al menos 2 caracteres.';
        }

        // Validación de Apellidos
        if (empty($datos['apellidos'])) {
            $errores['apellidos'] = 'Los apellidos son obligatorios.';
        } elseif (mb_strlen($datos['apellidos']) < 2) {
            $errores['apellidos'] = 'Los apellidos deben tener al menos 2 caracteres.';
        }

        // Validación de Correo
        if (empty($datos['correo'])) {
            $errores['correo'] = 'El correo electrónico es obligatorio.';
        } elseif (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            $errores['correo'] = 'Ingrese un correo electrónico válido (ejemplo: usuario@correo.com).';
        }

        // Validación de Ciudad y País en catálogo
        $ciudadNormalizada = mb_strtolower(trim($datos['ciudad'] ?? ''), 'UTF-8');
        $mapa = self::getMapaCiudadPais();
        if (empty($datos['ciudad'])) {
            $errores['ciudad'] = 'Debe seleccionar o escribir una ciudad de Colombia, Italia o Francia.';
        } elseif (!isset($mapa[$ciudadNormalizada])) {
            $errores['ciudad'] = 'La ciudad ingresada no pertenece al catálogo (Colombia, Italia o Francia).';
        }

        if (empty($datos['pais'])) {
            $errores['pais'] = 'El país es obligatorio (se autocompleta automáticamente según la ciudad).';
        }

        // Validación de Contraseña
        if (empty($password)) {
            $errores['password'] = 'La contraseña es obligatoria.';
        } elseif (strlen($password) < 6) {
            $errores['password'] = 'La contraseña debe tener al menos 6 caracteres.';
        }

        if (empty($confirm_password)) {
            $errores['confirm_password'] = 'Debe confirmar su contraseña.';
        } elseif ($password !== $confirm_password) {
            $errores['confirm_password'] = 'Las contraseñas no coinciden.';
        }

        return $errores;
    }
}
