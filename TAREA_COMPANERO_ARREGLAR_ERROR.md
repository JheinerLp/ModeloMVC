# 🛠️ Tarea para Compañero de Equipo: Corrección de Requerimiento 2

Hola compañero. Como parte de la administración del proyecto y del trabajo en equipo para la entrega de DevOps, se te ha asignado la corrección del **Requerimiento 2**.

---

## 📋 Requerimiento Asignado (1 pto)
> *"El formulario incluirá un catálogo de ciudades de Colombia, Italia y Francia. Si se selecciona o ingresa una ciudad (por ejemplo, Roma), el sistema debe autocompletar automáticamente el país correspondiente (Italia)."*

---

## ❌ Descripción del Error Actual
Actualmente, si entras a `registro.php` y escribes o seleccionas la ciudad **"Roma"** o **"Milán"**, el sistema comete un error y **autocompleta automáticamente "Francia"** en lugar de **"Italia"**. Además, varias ciudades de Italia están temporalmente deshabilitadas.

---

## 📍 Ubicación Exacta del Error
* **Archivo:** `registro.php`
* **Líneas aproximadas:** Entre la línea 425 y la 450 (en la sección `<script>` al final del archivo).

---

## 🔍 Código Actual con el Error
En `registro.php` encontrarás este bloque claramente señalado:

```javascript
// ====================================================================================
// ⚠️ [ACTIVIDAD PARA MI COMPAÑERO - REQUERIMIENTO 2] ⚠️
// ❌ ERROR INTENCIONAL: Roma y Milán están autocompletando erróneamente con "Francia"
// en lugar de "Italia", y las demás ciudades italianas quedaron comentadas.
// ====================================================================================
// Italia (CON ERROR INTENCIONAL)
"roma": "Francia", // ❌ ERROR: Cambiar "Francia" por "Italia"
"milan": "Francia", // ❌ ERROR: Cambiar "Francia" por "Italia"
"milán": "Francia", // ❌ ERROR: Cambiar "Francia" por "Italia"
// "napoles": "Italia", "nápoles": "Italia",
// "florencia": "Italia",
// "venecia": "Italia",
// "turin": "Italia", "turín": "Italia",
// "bolonia": "Italia",
// "palermo": "Italia",
// "genova": "Italia", "génova": "Italia",
// "verona": "Italia",
```

---

## ✅ Solución: ¿Cómo debes dejar el código?
Reemplaza ese bloque para que quede correctamente asignado a **"Italia"** y descomenta las demás ciudades italianas:

```javascript
// Italia (CORREGIDO)
"roma": "Italia",
"milan": "Italia", "milán": "Italia",
"napoles": "Italia", "nápoles": "Italia",
"florencia": "Italia",
"venecia": "Italia",
"turin": "Italia", "turín": "Italia",
"bolonia": "Italia",
"palermo": "Italia",
"genova": "Italia", "génova": "Italia",
"verona": "Italia",
```

---

## 🧪 ¿Cómo probar que ya quedó bien?
1. Guarda el archivo `registro.php`.
2. Abre en tu navegador: [http://localhost/catalogo-ap/registro.php](http://localhost/catalogo-ap/registro.php)
3. En el campo **Ciudad**, escribe **Roma**.
4. Verifica que en el campo **País** aparezca automáticamente: **🇮🇹 Italia**.

---

## 🚀 Subir tu corrección a GitHub
Cuando lo hayas arreglado y probado, abre una terminal en la carpeta del proyecto y ejecuta estos comandos para registrar tu aporte en Git:

```bash
git add registro.php
git commit -m "fix(registro): corregir autocompletado de ciudades de Italia a su pais correspondiente (Req 2)"
git push origin main
```

¡Con esto tu aporte queda registrado en el historial de Git y verificado para la entrega!
