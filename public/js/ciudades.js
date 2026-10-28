/**
 * Catálogo AP - Script de Autocompletado de Ciudades y Países
 * Países soportados: Colombia, Italia, Francia
 */

const catalogoCiudades = {
    // Colombia
    "bogota": "Colombia", "bogotá": "Colombia",
    "medellin": "Colombia", "medellín": "Colombia",
    "cali": "Colombia", "barranquilla": "Colombia",
    "cartagena": "Colombia", "bucaramanga": "Colombia",
    "pereira": "Colombia", "santa marta": "Colombia",
    "manizales": "Colombia", "cucuta": "Colombia", "cúcuta": "Colombia",
    "pasto": "Colombia", "armenia": "Colombia",
    "ibague": "Colombia", "ibagué": "Colombia",
    "villavicencio": "Colombia",

    // Italia
    "roma": "Italia", "milan": "Italia", "milán": "Italia",
    "napoles": "Italia", "nápoles": "Italia", "florencia": "Italia",
    "venecia": "Italia", "turin": "Italia", "turín": "Italia",
    "bolonia": "Italia", "palermo": "Italia", "genova": "Italia",
    "génova": "Italia", "verona": "Italia",

    // Francia
    "paris": "Francia", "parís": "Francia", "marsella": "Francia",
    "lyon": "Francia", "toulouse": "Francia", "niza": "Francia",
    "nantes": "Francia", "estrasburgo": "Francia", "montpellier": "Francia",
    "burdeos": "Francia", "lille": "Francia"
};

const flags = {
    "Colombia": "🇨🇴 Colombia",
    "Italia": "🇮🇹 Italia",
    "Francia": "🇫🇷 Francia"
};

function normalizarTexto(texto) {
    return (texto || '').toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .trim();
}

function inicializarAutocompletado() {
    const inputCiudad = document.getElementById('ciudad');
    const inputPais = document.getElementById('pais');
    const paisHelper = document.getElementById('pais-helper');

    if (!inputCiudad || !inputPais) return;

    function autocompletarPais() {
        const ciudadIngresada = (inputCiudad.value || '').trim();
        const ciudadNormalizada = normalizarTexto(ciudadIngresada);
        const claveDirecta = ciudadIngresada.toLowerCase();

        const paisEncontrado = catalogoCiudades[claveDirecta] || catalogoCiudades[ciudadNormalizada];

        if (paisEncontrado) {
            inputPais.value = paisEncontrado;
            inputPais.classList.remove('is-invalid');
            inputPais.classList.add('is-valid');
            if (paisHelper) {
                paisHelper.innerHTML = `<span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i>País autocompletado: ${flags[paisEncontrado] || paisEncontrado}</span>`;
            }
        } else if (ciudadIngresada.length > 0) {
            inputPais.value = '';
            inputPais.classList.remove('is-valid');
            if (paisHelper) {
                paisHelper.innerHTML = `<span class="text-warning"><i class="bi bi-info-circle me-1"></i>Ciudad no reconocida en el catálogo (Col, Ita, Fra)</span>`;
            }
        } else {
            inputPais.value = '';
            inputPais.classList.remove('is-valid', 'is-invalid');
            if (paisHelper) {
                paisHelper.textContent = 'Selecciona una ciudad primero';
            }
        }
    }

    inputCiudad.addEventListener('input', autocompletarPais);
    inputCiudad.addEventListener('change', autocompletarPais);

    if (inputCiudad.value.trim() !== '') {
        autocompletarPais();
    }

    const primerError = document.querySelector('.is-invalid');
    if (primerError) {
        primerError.focus();
    }
}

document.addEventListener('DOMContentLoaded', inicializarAutocompletado);
