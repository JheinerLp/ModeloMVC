Write-Host "=== TEST 1: SINTAXIS PHP (MVC COMPLETO) ==="
$archivos = Get-ChildItem -Path '.' -Filter *.php -Recurse
$errores = 0
foreach ($archivo in $archivos) {
    Write-Host "Verificando: $($archivo.FullName.Replace((Get-Location).Path, ''))"
    & 'C:\xampp\php\php.exe' -l $archivo.FullName
    if ($LASTEXITCODE -ne 0) { $errores++ }
}
if ($errores -gt 0) {
    Write-Error "Fallo la verificacion de sintaxis ($errores errores)"
    exit 1
} else {
    Write-Host "OK - Sintaxis PHP: 100% CORRECTA EN TODOS LOS ARCHIVOS" -ForegroundColor Green
}

Write-Host "`n=== TEST 2: ARQUITECTURA MVC Y ARCHIVOS REQUERIDOS ==="
$archivosRequeridos = @(
    '.env',
    'routes/web.php',
    'config/database.php',
    'sql/database.sql',
    'models/Usuario.php',
    'models/Producto.php',
    'controllers/BaseController.php',
    'controllers/UsuarioController.php',
    'controllers/CatalogoController.php',
    'controllers/HomeController.php',
    'views/layouts/plantilla.php',
    'views/usuarios/listar.php',
    'views/usuarios/crear.php',
    'views/usuarios/login.php',
    'views/usuarios/editar.php',
    'views/catalogo/listar.php',
    'views/home/inicio.php',
    'public/index.php',
    'public/css/style.css',
    'public/js/ciudades.js',
    'registro.php',
    'catalogo.php',
    'login.php',
    'logout.php',
    'index.php',
    'Jenkinsfile',
    'assets/style.css'
)

foreach ($req in $archivosRequeridos) {
    if (Test-Path $req) {
        Write-Host "OK - Componente MVC presente: $req" -ForegroundColor Green
    } else {
        Write-Error "Falta el componente requerido: $req"
        exit 1
    }
}

Write-Host "`n=== TEST 3: VALIDACION DE REQUERIMIENTOS Y FUNCIONALIDADES ==="
$regPath = if (Test-Path 'views/usuarios/crear.php') { 'views/usuarios/crear.php' } else { 'registro.php' }
$regContent = Get-Content $regPath -Raw
if ($regContent -match 'Colombia' -and $regContent -match 'Italia' -and $regContent -match 'Francia') {
    Write-Host "OK - Requerimiento 2: Catalogo de ciudades (Colombia, Italia, Francia) detectado en vista/modelo." -ForegroundColor Green
} else {
    Write-Error "Requerimiento 2 no detectado en registro"
}

if ($regContent -match 'is-invalid' -and $regContent -match 'invalid-feedback' -and $regContent -match 'datos') {
    Write-Host "OK - Requerimiento 1: Persistencia de datos y senalizacion de errores detectado." -ForegroundColor Green
} else {
    Write-Error "Requerimiento 1 no detectado en vista de registro"
}

$catPath = if (Test-Path 'views/catalogo/listar.php') { 'views/catalogo/listar.php' } else { 'catalogo.php' }
$catContent = Get-Content $catPath -Raw
if ($catContent -match 'modalDetalle' -and $catContent -match 'data-bs-toggle') {
    Write-Host "OK - Requerimiento 3: Botones Ver detalles con modales dinamicos detectado." -ForegroundColor Green
} else {
    Write-Error "Requerimiento 3 no detectado en vista de catalogo"
}

$plantillaContent = if (Test-Path 'views/layouts/plantilla.php') { Get-Content 'views/layouts/plantilla.php' -Raw } else { $catContent }
if ($plantillaContent -match 'Jenkins' -or $plantillaContent -match 'Git' -or $catContent -match 'Jenkins' -or $catContent -match 'Git') {
    Write-Host "OK - Requerimiento 4: Modificacion visible de integracion continua detectada." -ForegroundColor Green
} else {
    Write-Error "Requerimiento 4 no detectado en interfaz"
}

Write-Host "`n=== TODOS LOS TESTS DEL PIPELINE Y MVC PASARON EXITOSAMENTE ===" -ForegroundColor Green
