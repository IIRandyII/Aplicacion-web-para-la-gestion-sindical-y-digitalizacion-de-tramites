// ===============================
// SIDEBAR + RESPONSIVE
// ===============================
const toggleBtn = document.getElementById("toggleSidebar");
const sidebar = document.getElementById("sidebar");
const main = document.querySelector(".main");

// Crear overlay dinámico (para móvil)
const overlay = document.createElement("div");
overlay.classList.add("overlay");
document.body.appendChild(overlay);

toggleBtn.addEventListener("click", () => {
    sidebar.classList.toggle("active");
    main.classList.toggle("shifted");
    overlay.classList.toggle("active");
});

// Cerrar sidebar al hacer click fuera (móvil)
overlay.addEventListener("click", () => {
    sidebar.classList.remove("active");
    main.classList.remove("shifted");
    overlay.classList.remove("active");
});


// ===============================
// CARDS (CLICK)
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
// ===============================
function cargarConteo() {
    fetch('obtener_conteo_usuario.php')
    .then(response => response.json())
    .then(data => {
        document.getElementById('total').textContent = data.total || 0;
        document.getElementById('pendientes').textContent = data.pendientes || 0;
        document.getElementById('revision').textContent = data.revision || 0;
        document.getElementById('aprobados').textContent = data.aprobados || 0;
    })
    .catch(error => console.error('Error:', error));
}

// Ejecutar al cargar
document.addEventListener("DOMContentLoaded", cargarConteo);

// Actualizar cada 10 segundos
setInterval(cargarConteo, 10000);