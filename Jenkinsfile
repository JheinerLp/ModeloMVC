pipeline {
    agent any

    environment {
        PHP_BIN = 'C:\\xampp\\php\\php.exe'
        DEPLOY_DIR = 'C:\\xampp\\htdocs\\catalogo-ap'
    }

    stages {
        stage('1. Checkout de Código') {
            steps {
                echo '=============================================='
                echo '  FASE 1: DESCARGA Y CHECKOUT DESDE GIT       '
                echo '=============================================='
                checkout scm
            }
        }

        stage('2. Verificar Entorno PHP') {
            steps {
                echo '=============================================='
                echo '  FASE 2: VERIFICACIÓN DEL ENTORNO PHP        '
                echo '=============================================='
                bat "\"${PHP_BIN}\" -v"
            }
        }

        stage('3. Explorar Estructura del Proyecto (MVC + HTML Views)') {
            steps {
                echo '=============================================='
                echo '  FASE 3: EXPLORANDO ARQUITECTURA MVC         '
                echo '=============================================='
                bat 'dir'
            }
        }

        stage('4. Revisión de Sintaxis PHP (Linter)') {
            steps {
                echo '=============================================='
                echo '  FASE 4: ANÁLISIS ESTÁTICO Y SINTAXIS PHP    '
                echo '=============================================='
                powershell """
                    \$archivos = Get-ChildItem -Path '.' -Filter *.php -Recurse
                    \$errores = 0
                    foreach (\$archivo in \$archivos) {
                        Write-Host "Verificando sintaxis: \$(\$archivo.FullName)"
                        & '${PHP_BIN}' -l \$archivo.FullName
                        if (\$LASTEXITCODE -ne 0) {
                            \$errores++
                        }
                    }
                    if (\$errores -gt 0) {
                        Write-Error "Se encontraron \$errores archivos con errores de sintaxis."
                        exit 1
                    } else {
                        Write-Host '✅ Todos los archivos PHP pasaron la validación de sintaxis con éxito.'
                    }
                """
            }
        }

        stage('5. Validar Requerimientos Clave y Vistas HTML') {
            steps {
                echo '=============================================='
                echo '  FASE 5: VALIDACIÓN DE REQUERIMIENTOS Y MVC  '
                echo '=============================================='
                powershell """
                    \$archivosRequeridos = @(
                        '.env',
                        'routes/web.php',
                        'config/database.php',
                        'sql/database.sql',
                        'models/Usuario.php',
                        'models/Producto.php',
                        'controllers/BaseController.php',
                        'controllers/UsuarioController.php',
                        'controllers/CatalogoController.php',
                        'views/layouts/plantilla.html',
                        'views/usuarios/listar.html',
                        'views/usuarios/crear.html',
                        'views/catalogo/listar.html',
                        'public/index.php',
                        'public/css/style.css',
                        'public/js/ciudades.js',
                        'registro.php',
                        'catalogo.php',
                        'login.php',
                        'index.php',
                        'assets/style.css'
                    )

                    foreach (\$req in \$archivosRequeridos) {
                        if (Test-Path \$req) {
                            Write-Host "✅ Archivo / Vista HTML presente: \$req"
                        } else {
                            Write-Error "❌ Falta el archivo requerido: \$req"
                            exit 1
                        }
                    }

                    # Validar presencia del catálogo de ciudades (Col, Ita, Fra) en vistas HTML
                    \$contenidoRegistro = if (Test-Path 'views/usuarios/crear.html') { Get-Content 'views/usuarios/crear.html' -Raw } else { Get-Content 'registro.php' -Raw }
                    if (\$contenidoRegistro -match 'Colombia' -and \$contenidoRegistro -match 'Italia' -and \$contenidoRegistro -match 'Francia') {
                        Write-Host '✅ Catálogo de ciudades (Colombia, Italia, Francia) validado correctamente en HTML.'
                    } else {
                        Write-Error '❌ El catálogo de ciudades no contiene todos los países requeridos.'
                        exit 1
                    }

                    # Validar presencia de 'Ver Detalles' en vistas HTML y catálogo
                    \$contenidoCatalogo = if (Test-Path 'views/catalogo/listar.html') { Get-Content 'views/catalogo/listar.html' -Raw } else { Get-Content 'catalogo.php' -Raw }
                    if (\$contenidoCatalogo -match 'modalDetalle' -or \$contenidoCatalogo -match 'data-bs-toggle') {
                        Write-Host '✅ Funcionalidad de modales para Ver Detalles validada correctamente en HTML.'
                    } else {
                        Write-Error '❌ No se detectó la funcionalidad de modales en el catálogo.'
                        exit 1
                    }
                """
            }
        }

        stage('6. Entrega Continua (CD) - Despliegue a XAMPP') {
            steps {
                echo '=============================================='
                echo '  FASE 6: ENTREGA CONTINUA A XAMPP HTDOCS     '
                echo '=============================================='
                powershell """
                    Write-Host "Desplegando archivos a: ${DEPLOY_DIR}..."
                    if (!(Test-Path '${DEPLOY_DIR}')) {
                        New-Item -ItemType Directory -Path '${DEPLOY_DIR}' -Force | Out-Null
                    }
                    Copy-Item -Path './*' -Destination '${DEPLOY_DIR}' -Recurse -Force -Exclude '.git'
                    Write-Host '🚀 Despliegue completado con éxito en el servidor web local.'
                """
            }
        }
    }

    post {
        always {
            echo '🏁 Pipeline de Jenkins finalizado.'
        }
        success {
            echo '🎉 SUCCESS: ¡Build e Integración Continua exitosos! El código está limpio y desplegado.'
        }
        failure {
            echo '❌ FAILURE: Falló el build en Jenkins. Por favor revisa los logs de la consola.'
        }
    }
}
