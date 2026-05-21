// ===============================
// DASHBOARD USUARIO
// Lógica principal del dashboard:
// cards, contador animado, carrusel
// y modal de aviso completo
// ===============================

// -----------------------------------------------
// Cache de elementos del modal de aviso
// (una sola búsqueda en el DOM al cargar)
// -----------------------------------------------
const modalAvisoEl    = document.getElementById("modalAvisoUsuario");
const modalDeptNombre = document.getElementById("modalAvisoDeptNombre");
const modalTitulo     = document.getElementById("modalAvisoTitulo");
const modalMensaje    = document.getElementById("modalAvisoMensaje");
const modalFecha      = document.getElementById("modalAvisoFecha");
const modalBadge      = document.getElementById("modalAvisoTipoBadge");

// ===============================
// PUNTO DE ENTRADA ÚNICO
// Todo se inicializa aquí al cargar
// ===============================
document.addEventListener("DOMContentLoaded", () => {
    inicializarCards();
    inicializarCarrusel();
    inicializarModalAviso();
    cargarConteo();
});

// ===============================
// CARDS - CLICK
// Abre modal según estado al
// hacer click en una card
// ===============================
function inicializarCards() {
    document.querySelectorAll(".info-card").forEach(card => {
        card.addEventListener("click", () => {
            const estado = card.dataset.estado;
            if (estado) {
                openModal(estado); // función existente en otro archivo
            }
        });
    });
}

// ===============================
// EFECTO CONTADOR ANIMADO
// El número sube progresivamente
// con una curva de desaceleración
// ===============================
function animarContador(elemento, valorFinal, duracion = 800) {
    if (valorFinal === 0) {
        elemento.textContent = 0;
        return;
    }

    let startTime = null;

    function paso(timestamp) {
        if (!startTime) startTime = timestamp;
        const progreso = Math.min((timestamp - startTime) / duracion, 1);
        const easeOut  = 1 - Math.pow(1 - progreso, 3);
        elemento.textContent = Math.floor(easeOut * valorFinal);
        if (progreso < 1) {
            requestAnimationFrame(paso);
        } else {
            elemento.textContent = valorFinal;
        }
    }

    requestAnimationFrame(paso);
}

// ===============================
// CONTEO DINÁMICO
// Trae los totales del usuario
// desde el servidor
// ===============================
function cargarConteo() {
    fetch("obtener_conteo_usuario.php")
        .then(response => response.json())
        .then(data => {
            animarContador(document.getElementById("total"),      data.total      || 0);
            animarContador(document.getElementById("pendientes"), data.pendientes || 0);
            animarContador(document.getElementById("revision"),   data.revision   || 0);
            animarContador(document.getElementById("aprobados"),  data.aprobados  || 0);
        })
        .catch(error => console.error("Error al cargar conteo:", error));
}

// ===============================
// CARRUSEL DE AVISOS
// Automático cada 4 segundos,
// navegación manual y swipe táctil
// ===============================
function inicializarCarrusel() {

    const contenedor  = document.getElementById("carruselContenedor");
    const btnPrev     = document.getElementById("btnPrev");
    const btnNext     = document.getElementById("btnNext");
    const indicadores = document.getElementById("carruselIndicadores");

    if (!contenedor) return;

    const items      = contenedor.querySelectorAll(".carrusel-item");
    const totalItems = items.length;
    let indiceActual = 0;
    let intervalo;

    // -----------------------------------------------
    // Calcula cuántos items caben según el ancho actual
    // -----------------------------------------------
    function calcularItemsPorVista() {
        if (window.innerWidth <= 768)  return 1;
        if (window.innerWidth <= 1199) return 2;
        return 3;
    }

    let itemsPorVista = calcularItemsPorVista();
    let totalPuntos   = Math.ceil(totalItems / itemsPorVista);

    // -----------------------------------------------
    // Crear / recrear indicadores de puntos
    // -----------------------------------------------
    function crearIndicadores() {
        indicadores.innerHTML = "";
        totalPuntos = Math.ceil(totalItems / itemsPorVista);

        for (let i = 0; i < totalPuntos; i++) {
            const punto = document.createElement("div");
            punto.classList.add("carrusel-punto");
            if (i === indiceActual) punto.classList.add("activo");
            punto.addEventListener("click", () => {
                irA(i);
                reiniciarIntervalo();
            });
            indicadores.appendChild(punto);
        }
    }

    // -----------------------------------------------
    // Mover carrusel a un índice
    // -----------------------------------------------
    function irA(indice) {
        indiceActual = Math.max(0, Math.min(indice, totalPuntos - 1));

        // gap de 16px igual que en CSS
        const anchoItem = items[0].offsetWidth + 16;
        contenedor.scrollLeft = anchoItem * itemsPorVista * indiceActual;

        document.querySelectorAll(".carrusel-punto").forEach((p, i) => {
            p.classList.toggle("activo", i === indiceActual);
        });
    }

    function siguiente() { irA((indiceActual + 1) % totalPuntos); }
    function anterior()  { irA((indiceActual - 1 + totalPuntos) % totalPuntos); }

    // -----------------------------------------------
    // Botones prev / next (visibles en tablet/desktop)
    // -----------------------------------------------
    if (btnNext) btnNext.addEventListener("click", () => { siguiente(); reiniciarIntervalo(); });
    if (btnPrev) btnPrev.addEventListener("click", () => { anterior();  reiniciarIntervalo(); });

    // -----------------------------------------------
    // Swipe táctil para móvil
    // -----------------------------------------------
    let touchStartX = 0;

    contenedor.addEventListener("touchstart", (e) => {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    contenedor.addEventListener("touchend", (e) => {
        const diff = touchStartX - e.changedTouches[0].screenX;
        if (Math.abs(diff) > 50) { // umbral mínimo de 50px
            diff > 0 ? siguiente() : anterior();
            reiniciarIntervalo();
        }
    }, { passive: true });

    // -----------------------------------------------
    // Autoplay cada 4 segundos
    // -----------------------------------------------
    function iniciarIntervalo()    { intervalo = setInterval(siguiente, 4000); }
    function reiniciarIntervalo()  { clearInterval(intervalo); iniciarIntervalo(); }

    // -----------------------------------------------
    // Recalcular al cambiar tamaño de ventana
    // -----------------------------------------------
    let resizeTimer;
    window.addEventListener("resize", () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            const nuevosItemsPorVista = calcularItemsPorVista();
            if (nuevosItemsPorVista !== itemsPorVista) {
                itemsPorVista = nuevosItemsPorVista;
                indiceActual  = 0;
                crearIndicadores();
                irA(0);
            }
        }, 200); // debounce de 200ms
    });

    // Init
    crearIndicadores();
    irA(0);
    iniciarIntervalo();
}

// ===============================
// MODAL DE AVISO COMPLETO
// Lee los datos desde data-attributes
// para evitar inyección en onclick
// ===============================
function inicializarModalAviso() {

    // Abrir modal al hacer click en cualquier aviso del carrusel
    document.getElementById("carruselContenedor")?.addEventListener("click", (e) => {
        const item = e.target.closest(".carrusel-item");
        if (!item) return;

        modalDeptNombre.textContent = item.dataset.departamento;
        modalTitulo.textContent     = item.dataset.titulo;
        modalMensaje.textContent    = item.dataset.mensaje;
        modalFecha.textContent      = "Publicado: " + item.dataset.fecha;

        // Badge de tipo en el header del modal
        modalBadge.textContent = item.dataset.tipoLabel || "General";
        modalBadge.className   = "aviso-tipo-badge tipo-" + (item.dataset.tipo || "general");

        abrirModal("modalAvisoUsuario"); // función de utils.js
    });
}