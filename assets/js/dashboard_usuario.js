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
// EFECTO CASINO EN CONTADORES
// Anima el número desde 0 hasta
// el valor real al cargar
// ===============================
function animarContador(elemento, valorFinal, duracion = 1000) {
    const inicio    = 0;
    const incremento = valorFinal / (duracion / 16);
    let valorActual  = inicio;

    const intervalo = setInterval(() => {
        valorActual += incremento;

        if (valorActual >= valorFinal) {
            elemento.textContent = valorFinal;
            clearInterval(intervalo);
        } else {
            elemento.textContent = Math.floor(valorActual);
        }
    }, 16);
}

// ===============================
// CONTEO DINÁMICO
// Obtiene y actualiza los contadores
// de trámites cada 10 segundos
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

// Ejecutar al cargar la página
document.addEventListener("DOMContentLoaded", cargarConteo);

// Actualizar cada 10 segundos
setInterval(cargarConteo, 10000);