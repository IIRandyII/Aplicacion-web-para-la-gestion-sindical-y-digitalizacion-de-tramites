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
// CONTEO DINÁMICO
// Obtiene y actualiza los contadores
// de trámites cada 10 segundos
// ===============================
function cargarConteo() {
    fetch('obtener_conteo_usuario.php')
    .then(response => response.json())
    .then(data => {
        document.getElementById('total').textContent      = data.total     || 0;
        document.getElementById('pendientes').textContent = data.pendientes || 0;
        document.getElementById('revision').textContent   = data.revision   || 0;
        document.getElementById('aprobados').textContent  = data.aprobados  || 0;
    })
    .catch(error => console.error('Error al cargar conteo:', error));
}

// Ejecutar al cargar la página
document.addEventListener("DOMContentLoaded", cargarConteo);

// Actualizar cada 10 segundos
setInterval(cargarConteo, 10000);