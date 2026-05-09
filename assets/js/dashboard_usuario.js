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
// para un efecto más limpio y visual
// ===============================
function animarContador(elemento, valorFinal, duracion = 800) {

    // Si el valor es 0 no hay nada que animar
    if (valorFinal === 0) {
        elemento.textContent = 0;
        return;
    }

    let inicio    = 0;
    let startTime = null;

    function paso(timestamp) {

        if (!startTime) startTime = timestamp;

        // Progreso entre 0 y 1
        const progreso = Math.min((timestamp - startTime) / duracion, 1);

        // Curva ease-out: desacelera al final para efecto suave
        const easeOut = 1 - Math.pow(1 - progreso, 3);

        // Calcular número actual
        elemento.textContent = Math.floor(easeOut * valorFinal);

        // Continuar animación hasta llegar al final
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
setInterval(cargarConteo, 10000);

// ===============================
// CARRUSEL DE AVISOS
// Automático cada 4 segundos
// con navegación manual
// ===============================
document.addEventListener("DOMContentLoaded", () => {

    const contenedor   = document.getElementById("carruselContenedor");
    const btnPrev      = document.getElementById("btnPrev");
    const btnNext      = document.getElementById("btnNext");
    const indicadores  = document.getElementById("carruselIndicadores");

    if (!contenedor) return;

    const items        = contenedor.querySelectorAll(".carrusel-item");
    const totalItems   = items.length;
    const itemsPorVista = window.innerWidth <= 768 ? 1 : window.innerWidth <= 992 ? 2 : 3;
    let   indiceActual = 0;
    let   intervalo;

    // Crear indicadores de puntos
    const totalPuntos = Math.ceil(totalItems / itemsPorVista);
    for (let i = 0; i < totalPuntos; i++) {
        const punto = document.createElement("div");
        punto.classList.add("carrusel-punto");
        if (i === 0) punto.classList.add("activo");
        punto.addEventListener("click", () => irA(i));
        indicadores.appendChild(punto);
    }

    // Mover carrusel a un índice
    function irA(indice) {
        indiceActual = indice;
        const anchoItem = items[0].offsetWidth + 20;
        contenedor.scrollLeft = anchoItem * itemsPorVista * indice;

        // Actualizar puntos
        document.querySelectorAll(".carrusel-punto").forEach((p, i) => {
            p.classList.toggle("activo", i === indice);
        });
    }

    // Siguiente
    function siguiente() {
        const siguienteIndice = (indiceActual + 1) % totalPuntos;
        irA(siguienteIndice);
    }

    // Anterior
    function anterior() {
        const anteriorIndice = (indiceActual - 1 + totalPuntos) % totalPuntos;
        irA(anteriorIndice);
    }

    btnNext.addEventListener("click", () => {
        siguiente();
        reiniciarIntervalo();
    });

    btnPrev.addEventListener("click", () => {
        anterior();
        reiniciarIntervalo();
    });

    // Autoplay cada 4 segundos
    function iniciarIntervalo() {
        intervalo = setInterval(siguiente, 4000);
    }

    function reiniciarIntervalo() {
        clearInterval(intervalo);
        iniciarIntervalo();
    }

    iniciarIntervalo();
});

// ===============================
// MODAL DE AVISO COMPLETO
// ===============================
function abrirModalAviso(titulo, mensaje, departamento, fecha) {
    document.getElementById("modalAvisoDeptNombre").textContent = departamento;
    document.getElementById("modalAvisoTitulo").textContent     = titulo;
    document.getElementById("modalAvisoMensaje").textContent    = mensaje;
    document.getElementById("modalAvisoFecha").textContent      = "Publicado: " + fecha;

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