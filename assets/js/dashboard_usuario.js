// ===============================
// CARDS - CLICK
// Abre modal según estado al
// hacer click en una card
// ===============================
document.querySelectorAll(".info-card").forEach(card => {
    card.addEventListener("click", () => {
        const estado = card.dataset.estado;
        if (estado) {
            openModal(estado);
        }
    });
});

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

    let inicio    = 0;
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
// ===============================
function cargarConteo() {
    fetch('obtener_conteo_usuario.php')
    .then(response => response.json())
    .then(data => {
        animarContador(document.getElementById('total'),      data.total      || 0);
        animarContador(document.getElementById('pendientes'), data.pendientes || 0);
        animarContador(document.getElementById('revision'),   data.revision   || 0);
        animarContador(document.getElementById('aprobados'),  data.aprobados  || 0);
    })
    .catch(error => console.error('Error al cargar conteo:', error));
}

document.addEventListener("DOMContentLoaded", cargarConteo);


// ===============================
// CARRUSEL DE AVISOS
// Automático cada 4 segundos,
// navegación manual y swipe táctil
// ===============================
document.addEventListener("DOMContentLoaded", () => {

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

    function siguiente() {
        irA((indiceActual + 1) % totalPuntos);
    }

    function anterior() {
        irA((indiceActual - 1 + totalPuntos) % totalPuntos);
    }

    // -----------------------------------------------
    // Botones prev / next (visibles en tablet/desktop)
    // -----------------------------------------------
    if (btnNext) {
        btnNext.addEventListener("click", () => {
            siguiente();
            reiniciarIntervalo();
        });
    }

    if (btnPrev) {
        btnPrev.addEventListener("click", () => {
            anterior();
            reiniciarIntervalo();
        });
    }

    // -----------------------------------------------
    // Swipe táctil para móvil
    // -----------------------------------------------
    let touchStartX = 0;
    let touchEndX   = 0;

    contenedor.addEventListener("touchstart", (e) => {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    contenedor.addEventListener("touchend", (e) => {
        touchEndX = e.changedTouches[0].screenX;
        const diff = touchStartX - touchEndX;

        if (Math.abs(diff) > 50) { // umbral mínimo de 50px
            if (diff > 0) {
                siguiente();
            } else {
                anterior();
            }
            reiniciarIntervalo();
        }
    }, { passive: true });

    // -----------------------------------------------
    // Autoplay cada 4 segundos
    // -----------------------------------------------
    function iniciarIntervalo() {
        intervalo = setInterval(siguiente, 4000);
    }

    function reiniciarIntervalo() {
        clearInterval(intervalo);
        iniciarIntervalo();
    }

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
                indiceActual  = 0;   // resetear al inicio
                crearIndicadores();
                irA(0);
            }
        }, 200); // debounce de 200ms
    });

    // Init
    crearIndicadores();
    irA(0);
    iniciarIntervalo();
});

// ===============================
// MODAL DE AVISO COMPLETO
// Se agregaron tipo y tipoLabel
// para mostrar el badge de tipo
// ===============================
function abrirModalAviso(titulo, mensaje, departamento, fecha, tipo, tipoLabel) {
    document.getElementById("modalAvisoDeptNombre").textContent = departamento;
    document.getElementById("modalAvisoTitulo").textContent     = titulo;
    document.getElementById("modalAvisoMensaje").textContent    = mensaje;
    document.getElementById("modalAvisoFecha").textContent      = "Publicado: " + fecha;

    // Badge de tipo en el header del modal
    const badge       = document.getElementById("modalAvisoTipoBadge");
    badge.textContent = tipoLabel || "General";
    badge.className   = "aviso-tipo-badge tipo-" + (tipo || "general");

    document.getElementById("modalAvisoUsuario").classList.add("activo");
}

document.getElementById("cerrarModalAvisoUsuario").addEventListener("click", () => {
    document.getElementById("modalAvisoUsuario").classList.remove("activo");
});

document.getElementById("modalAvisoUsuario").addEventListener("click", (e) => {
    if (e.target === document.getElementById("modalAvisoUsuario")) {
        document.getElementById("modalAvisoUsuario").classList.remove("activo");
    }
});