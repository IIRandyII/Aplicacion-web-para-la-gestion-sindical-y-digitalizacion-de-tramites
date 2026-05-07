// ===============================
// SIDEBAR - REUTILIZABLE
// Maneja apertura/cierre del menú
// lateral y overlay para móvil
// ===============================

const toggleBtn = document.getElementById("toggleSidebar");
const sidebar   = document.getElementById("sidebar");
const main      = document.querySelector(".main");

// Crear overlay dinámico para móvil
const overlay = document.createElement("div");
overlay.classList.add("overlay");
document.body.appendChild(overlay);

// Abrir/cerrar sidebar al click del botón
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