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