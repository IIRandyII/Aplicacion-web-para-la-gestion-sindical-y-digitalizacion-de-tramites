// ===============================
// SIDEBAR + RESPONSIVE
// ===============================
const toggleBtn = document.getElementById("toggleSidebar");
const sidebar = document.getElementById("sidebar");

// Crear overlay dinámico (para móvil)
const overlay = document.createElement("div");
overlay.classList.add("overlay");
document.body.appendChild(overlay);

// Toggle sidebar
toggleBtn.addEventListener("click", () => {
    sidebar.classList.toggle("active");
    overlay.classList.toggle("active");
});

// Cerrar sidebar al dar click en overlay
overlay.addEventListener("click", () => {
    sidebar.classList.remove("active");
    overlay.classList.remove("active");
});

// ===============================
// RESPONSIVE AUTOMÁTICO
// ===============================
function ajustarSidebar() {
    if (window.innerWidth <= 768) {
        sidebar.classList.remove("active");
        overlay.classList.remove("active");
    } else {
        sidebar.classList.remove("active"); 
        overlay.classList.remove("active");
    }
}

// Ejecutar al cargar y al cambiar tamaño
window.addEventListener("load", ajustarSidebar);
window.addEventListener("resize", ajustarSidebar);

// ===============================
// PERFIL: EDITAR / GUARDAR
// ===============================
const btnEditar = document.getElementById("btnEditar");
const btnGuardar = document.getElementById("btnGuardar");

if (btnEditar && btnGuardar) {

    btnEditar.addEventListener("click", () => {

        // Habilitar solo campos editables
        const camposEditables = ["telefono", "email", "direccion", "fecha_nacimiento", "nombre", "numero_ficha", "curp", "rfc"];

        document.querySelectorAll("input, textarea").forEach(el => {
            if (camposEditables.includes(el.name)) {
                el.removeAttribute("disabled");
            }
        });

        btnGuardar.classList.remove("d-none");
        btnEditar.classList.add("d-none");
    });

}
