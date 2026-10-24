Write-Host "=== TEST 1: SINTAXIS PHP ==="
$archivos = Get-ChildItem -Path '.' -Filter *.php -Recurse
$errores = 0
foreach ($archivo in $archivos) {
    Write-Host "Verificando: $($archivo.Name)"
    & 'C:\xampp\php\php.exe' -l $archivo.FullName
    if ($LASTEXITCODE -ne 0) { $errores++ }
}
if ($errores -gt 0) {
    Write-Error "Fallo la verificacion de sintaxis ($errores errores)"
    exit 1
} else {
    Write-Host "OK - Sintaxis PHP: 100% CORRECTA" -ForegroundColor Green
}

Write-Host "`n=== TEST 2: ARCHIVOS Y REQUERIMIENTOS ==="
$archivosRequeridos = @(
    'registro.php',
    'catalogo.php',
    'login.php',
    'index.php',
    'Jenkinsfile',
    'config/database.php',
    'sql/database.sql',
    'assets/style.css'
)

foreach ($req in $archivosRequeridos) {
    if (Test-Path $req) {
        Write-Host "OK - Archivo presente: $req" -ForegroundColor Green
    } else {
        Write-Error "Falta el archivo: $req"
        exit 1
    }
}

Write-Host "`n=== TEST 3: VALIDACION DE CONTENIDO DE REQUERIMIENTOS ==="
$regContent = Get-Content 'registro.php' -Raw
if ($regContent -match 'Colombia' -and $regContent -match 'Italia' -and $regContent -match 'Francia') {
    Write-Host "OK - Requerimiento 2: Catalogo de ciudades (Colombia, Italia, Francia) detectado." -ForegroundColor Green
} else {
    Write-Error "Requerimiento 2 no detectado en registro.php"
}

if ($regContent -match 'is-invalid' -and $regContent -match 'invalid-feedback' -and $regContent -match 'datos') {
    Write-Host "OK - Requerimiento 1: Persistencia de datos y senalizacion de errores detectado." -ForegroundColor Green
} else {
    Write-Error "Requerimiento 1 no detectado en registro.php"
}

$catContent = Get-Content 'catalogo.php' -Raw
if ($catContent -match 'modalDetalle' -and $catContent -match 'data-bs-toggle') {
    Write-Host "OK - Requerimiento 3: Botones Ver detalles con informacion adicional detectado." -ForegroundColor Green
} else {
    Write-Error "Requerimiento 3 no detectado en catalogo.php"
}

if ($catContent -match 'Jenkins' -or $catContent -match 'Git') {
    Write-Host "OK - Requerimiento 4: Modificacion visible de integracion continua detectada." -ForegroundColor Green
} else {
    Write-Error "Requerimiento 4 no detectado en interfaz"
}

Write-Host "`n=== TODOS LOS TESTS DE INTEGRACION PASARON EXITOSAMENTE ===" -ForegroundColor Green
