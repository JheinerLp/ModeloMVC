# 🛠️ Tarea para Compañera de Equipo: Corrección de Requerimiento 3

Hola compañera. Como parte de la administración del proyecto y del trabajo colaborativo en equipo para la entrega de DevOps, se te ha asignado la corrección del **Requerimiento 3**.

---

## 📋 Requerimiento Asignado (1 pto)
> *"Corregir la funcionalidad de los botones 'Ver detalles', asegurando que muestren la información adicional correspondiente al producto."*

---

## ❌ Descripción del Error Actual
Actualmente, si entras a `catalogo.php` y haces clic en el botón azul **"Ver Detalles"** de cualquiera de los productos, **no ocurre nada (el botón está inactivo)**. Debe abrirse un modal interactivo de Bootstrap con la información ampliada del producto (SKU, descuentos, fechas de vigencia, stock, garantía, etc.).

---

## 📍 Ubicación Exacta en el Código
* **Archivo:** `catalogo.php`
* **Líneas aproximadas:** Alrededor de la línea 195 a 205.

---

## 🔍 Código Actual con el Error
En `catalogo.php` encontrarás el botón señalado con este comentario:

```html
<!-- ====================================================================================
     ⚠️ [ACTIVIDAD PARA MI COMPAÑERA - REQUERIMIENTO 3] ⚠️
     ❌ ERROR / TAREA PENDIENTE: El botón "Ver Detalles" no abre el modal con la
     información adicional del producto porque le faltan los atributos de Bootstrap.
     ==================================================================================== -->
<!-- Botón actualmente con error (no abre el modal al dar clic) -->
<button type="button" 
        class="btn btn-primary w-100 py-2 fw-semibold d-flex align-items-center justify-content-center shadow-sm">
    <i class="bi bi-eye-fill me-2"></i> Ver Detalles
</button>
```

---

## ✅ Solución: ¿Cómo debes dejar el código?
Solo debes agregarle a la etiqueta `<button>` los dos atributos de Bootstrap que vinculan el modal:
1. `data-bs-toggle="modal"`
2. `data-bs-target="#<?php echo $modalId; ?>"`

El botón debe quedar exactamente así:

```html
<!-- Botón CORREGIDO (ahora sí abre el modal interactivo) -->
<button type="button" 
        class="btn btn-primary w-100 py-2 fw-semibold d-flex align-items-center justify-content-center shadow-sm"
        data-bs-toggle="modal" 
        data-bs-target="#<?php echo $modalId; ?>">
    <i class="bi bi-eye-fill me-2"></i> Ver Detalles
</button>
```

*(Todo el modal con los detalles del producto ya está programado justo debajo esperando estos dos atributos).*

---

## 🧪 ¿Cómo probar que ya quedó bien?
1. Guarda el archivo `catalogo.php`.
2. Abre en tu navegador: [http://localhost/catalogo-ap/catalogo.php](http://localhost/catalogo-ap/catalogo.php)
3. Haz clic en el botón azul **"Ver Detalles"** de cualquier producto (Laptop, Smartphone, Auriculares, etc.).
4. Verifica que se abra la ventana emergente (modal) con la información completa:
   * Porcentaje de descuento y ahorro en dinero.
   * Fechas de vigencia de la promoción.
   * Código de producto (SKU).
   * Estado de inventario y países de envío disponibles.

---

## 🚀 Subir tu corrección a GitHub
Cuando lo hayas probado y funcione, abre una terminal en la carpeta del proyecto y ejecuta estos comandos para registrar tu aporte en Git:

```bash
git add catalogo.php
git commit -m "fix(catalogo): corregir funcionalidad del boton Ver detalles con modal interactivo (Req 3)"
git push origin main
```

¡Con esto tu aporte queda registrado en el historial de Git y listo para la calificación!
