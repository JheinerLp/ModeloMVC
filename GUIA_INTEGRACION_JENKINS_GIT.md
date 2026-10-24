# Guía de Práctica: Integración de Catálogo-AP con Jenkins y Git (CI/CD)

Este documento detalla la distribución de roles, la implementación técnica de los cuatro requerimientos solicitados y el procedimiento paso a paso para ejecutar y verificar la Integración y Entrega Continua (CI/CD) en Jenkins y Git.

---

## 1. Distribución de Roles y Asignación de Requerimientos

| Rol | Integrante | Responsabilidad y Requerimientos Asignados |
| :--- | :--- | :--- |
| **Líder de Proyecto / DevOps** | Líder de Equipo | • Coordinar y administrar el repositorio Git y la arquitectura general.<br>• Diseñar y configurar el pipeline automatizado `Jenkinsfile` (6 etapas).<br>• Gestionar el despliegue continuo (CD) a `C:\xampp\htdocs\catalogo-ap`.<br>• Integración de Git con Jenkins y control del historial de builds (**Requerimiento 4 - 2 ptos**). |
| **Integrante 1 (Backend & Validaciones)** | Integrante 1 | • Implementar la persistencia de datos en el formulario de inscripción `registro.php` para evitar que la información se borre ante errores.<br>• Señalización visual precisa de la ubicación del error mediante clases de Bootstrap (`is-invalid`) y mensajes contextuales (`invalid-feedback`) (**Requerimiento 1 - 1 pto**). |
| **Integrante 2 (Frontend Reactivo & Catálogo)** | Integrante 2 | • Desarrollar el catálogo estructurado de ciudades para **Colombia**, **Italia** y **Francia**.<br>• Programar la lógica JavaScript reactiva para autocompletar automáticamente el país correspondiente al elegir o ingresar una ciudad (ej. Roma -> Italia).<br>• Validación y coherencia ciudad-país en backend PHP (**Requerimiento 2 - 1 pto**). |
| **Integrante 3 (Módulo de Productos & UX)** | Integrante 3 | • Corregir la funcionalidad inerte de los botones "Ver detalles" en `catalogo.php`.<br>• Implementar modales dinámicos de Bootstrap 5 con la información extendida de cada producto (descuentos, fechas de vigencia, categoría, SKU y disponibilidad) (**Requerimiento 3 - 1 pto**). |

---

## 2. Detalle de Implementación Técnica de los Requerimientos

### 📌 Requerimiento 1 (1 pto): Persistencia de Datos y Señalización de Errores en Registro
* **Archivo modificado**: `registro.php`
* **Solución técnica**:
  1. Se creó el array asociativo `$datos` para capturar y preservar los valores ingresados por el usuario (`numero_documento`, `nombre`, `apellidos`, `correo`, `ciudad`, `pais`).
  2. En caso de error, los campos del formulario mantienen sus valores mediante `value="<?php echo htmlspecialchars($datos['campo']); ?>"` en lugar de reiniciarse.
  3. Se diseñó el array asociativo `$errores` que evalúa individualmente cada campo (longitud, caracteres válidos, formato de correo, contraseñas coincidentes y unicidad en la base de datos).
  4. Si un campo presenta error, se le añade dinámicamente la clase `is-invalid` y se muestra un bloque `<div class="invalid-feedback">` debajo de este, señalando con precisión el problema al usuario.
  5. Mediante JavaScript, el formulario enfoca automáticamente el primer campo erróneo para facilitar la corrección inmediata.

---

### 📌 Requerimiento 2 (1 pto): Catálogo de Ciudades (Colombia, Italia, Francia) y Autocompletado de País
* **Archivo modificado**: `registro.php`
* **Solución técnica**:
  1. Se definió un catálogo estructurado tanto en PHP como en JavaScript:
     * **Colombia**: Bogotá, Medellín, Cali, Barranquilla, Cartagena, Bucaramanga, Pereira, Santa Marta, Manizales, Cúcuta, Pasto, Armenia, Ibagué, Villavicencio.
     * **Italia**: Roma, Milán, Nápoles, Florencia, Venecia, Turín, Bolonia, Palermo, Génova, Verona.
     * **Francia**: París, Marsella, Lyon, Toulouse, Niza, Nantes, Estrasburgo, Montpellier, Burdeos, Lille.
  2. Se implementó un campo de entrada con `<datalist id="lista-ciudades">`, permitiendo al usuario tanto teclear como seleccionar cualquier ciudad del catálogo.
  3. Se añadió un escuchador de eventos JavaScript (`input` y `change`) que normaliza la entrada del usuario y consulta el catálogo. Al ingresar por ejemplo **"Roma"**, el campo **País** se autocompleta instantáneamente con **"Italia"** (mostrando bandera e indicador de éxito).
  4. En el backend de PHP, se verifica la relación ciudad-país antes de permitir la inserción en la base de datos.

---

### 📌 Requerimiento 3 (1 pto): Funcionalidad Extendida de los Botones "Ver Detalles"
* **Archivos modificados**: `catalogo.php`, `assets/style.css`
* **Solución técnica**:
  1. El botón estático fue reemplazado por un disparador interactivo:
     ```html
     <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalDetalle_<?php echo $producto['id']; ?>">
         <i class="bi bi-eye-fill me-2"></i> Ver Detalles
     </button>
     ```
  2. Se implementó un componente modal de Bootstrap 5 para cada producto (`modalDetalle_{ID}`) que muestra la información completa:
     * **Código único SKU**: (ej. `PRD-0001`).
     * **Categoría e icono contextual** (Electrónica, Accesorios, Wearables, etc.).
     * **Precio regular vs. Precio de promoción** con el cálculo del porcentaje de descuento y el ahorro monetario exacto.
     * **Descripción técnica y detallada**.
     * **Fechas de vigencia de la oferta**: Inicio y límite de la promoción.
     * **Estado de disponibilidad**: Indicador de stock y cobertura de envíos internacionales.
     * **Botón interactivo de compra/reserva** con notificación Toast flotante.

---

### 📌 Requerimiento 4 (2 ptos): Integración Continua con Git y Jenkins
* **Archivos implementados**: `Jenkinsfile`, `test_pipeline.ps1`, `ArchvosJenkins/`
* **Modificación visible en la aplicación**:
  * Se integró en la barra de navegación de `catalogo.php` y en `index.php` el distintivo visual:
    **`Git + Jenkins CI/CD v2.0`** y un banner de confirmación de despliegue continuo.
* **Pipeline automatizado de Jenkins (`Jenkinsfile`) compuesto por 6 fases**:
  1. **Fase 1 (Checkout)**: Descarga o sincronización del repositorio desde Git.
  2. **Fase 2 (Verificar PHP)**: Inspección del entorno PHP con `C:\xampp\php\php.exe -v`.
  3. **Fase 3 (Explorar Estructura)**: Comprobación del árbol de directorios del proyecto.
  4. **Fase 4 (Linter / Sintaxis PHP)**: Escaneo iterativo de todos los archivos `.php` con `php -l` para asegurar 0 errores de sintaxis.
  5. **Fase 5 (Validar Requerimientos Clave)**: Validación automatizada de los archivos y del cumplimiento de las funcionalidades solicitadas.
  6. **Fase 6 (Despliegue Continuo a XAMPP)**: Copia automatizada a `C:\xampp\htdocs\catalogo-ap` para que los cambios se reflejen de inmediato en el servidor web.

---

## 3. Guía Paso a Paso para Ejecutar y Ver el Historial en Jenkins

### Paso 1: Iniciar Jenkins
1. Si Jenkins está instalado como servicio de Windows, asegúrate de que esté iniciado en el panel de `Servicios` (`services.msc`) o ejecutando:
   ```powershell
   Start-Service jenkins
   ```
   *(Si se ejecuta como archivo `.war` independiente, ejecuta: `java -jar jenkins.war`)*.
2. Abre tu navegador web en: [http://localhost:8080](http://localhost:8080).

### Paso 2: Crear el Job / Pipeline en Jenkins
1. En el panel principal de Jenkins, haz clic en **Nueva Tarea** (o *New Item*).
2. Ingresa el nombre del proyecto: `catalogo-ap-pipeline`.
3. Selecciona la opción **Pipeline** y haz clic en **OK**.

### Paso 3: Configurar el Pipeline
Existen dos formas de configurarlo en Jenkins:

* **Opción A (Recomendada - Pipeline desde SCM / Git)**:
  1. En la sección **Pipeline**, en el menú desplegable **Definition**, selecciona **Pipeline script from SCM**.
  2. En **SCM**, selecciona **Git**.
  3. En **Repository URL**, ingresa la ruta de tu repositorio Git:
     * Si es local: `C:/Users/dana-/Downloads/catalogo-ap`
     * O la URL del repositorio remoto de GitHub: `https://github.com/jhonrot/catalogo-ap.git`
  4. En **Branch Specifier**, coloca `*/main`.
  5. En **Script Path**, escribe `Jenkinsfile`.
  6. Haz clic en **Guardar** (*Save*).

* **Opción B (Directo con Pipeline Script)**:
  1. En la sección **Pipeline**, selecciona **Pipeline script**.
  2. Abre el archivo `Jenkinsfile` del proyecto, copia todo su contenido y pégalo en el editor de Jenkins.
  3. Haz clic en **Guardar** (*Save*).

### Paso 4: Ejecutar la Integración (Build Now)
1. En el menú lateral izquierdo del proyecto en Jenkins, haz clic en **Construir ahora** (*Build Now*).
2. Verás que en la sección **Historial de ejecuciones** (*Build History*) aparece el build **#1**.
3. Haz clic en **#1** y luego en **Salida de la consola** (*Console Output*) para observar en tiempo real la ejecución de las 6 fases del pipeline.
4. Al finalizar, la fase mostrará el mensaje:
   ```
   🎉 SUCCESS: ¡Build e Integración Continua exitosos! El código está limpio y desplegado.
   Finished: SUCCESS
   ```

### Paso 5: Demostración de Integración Continua (Nuevo Commit -> Nuevo Build)
Para evidenciar el historial de integraciones:
1. Realiza una pequeña modificación en `catalogo.php` o `index.php` (por ejemplo, cambia el texto del banner o la versión a `v2.1`).
2. Confirma el cambio en Git:
   ```powershell
   & "C:\Program Files\Git\cmd\git.exe" add catalogo.php
   & "C:\Program Files\Git\cmd\git.exe" commit -m "docs: actualizacion de version para prueba de CI/CD"
   ```
3. Ejecuta nuevamente **Construir ahora** en Jenkins.
4. Verás aparecer el build **#2** en el **Historial de ejecuciones** (*Build History*).
5. Abre en tu navegador [http://localhost/catalogo-ap/catalogo.php](http://localhost/catalogo-ap/catalogo.php) y verifica que el cambio se reflejó automáticamente.

---

## 4. Historial de Commits en Git del Equipo

Puedes visualizar en la terminal el historial de commits ejecutando:
```powershell
& "C:\Program Files\Git\cmd\git.exe" log --oneline --graph
```

Salida registrada en el repositorio:
```
* a1cb574 ci(pipeline): configuracion de Jenkinsfile de 6 etapas, script de pruebas continuas y reflejo visual de CI/CD en la aplicacion (Req 4) [Líder de Proyecto]
* f5f2f2b feat(catalogo): corregir funcionalidad de botones 'Ver detalles' con modales interactivos e informacion extendida de productos (Req 3) [Integrante 3]
* 63cf1a3 feat(registro): persistencia de datos en errores (Req 1) y catalogo de ciudades Colombia-Italia-Francia con autocompletado de pais (Req 2) [Integrante 1 & 2]
* 20eb278 chore(config): configuracion inicial de gitignore y archivos de referencia Jenkins [Líder de Proyecto]
* 85853ee Primer commit: estructura inicial del proyecto catalogo
```
