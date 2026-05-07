// ===============================
// SIDEBAR AFILIADO - REUTILIZABLE
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
    toggleBtn.classList.toggle("rotado");
});

// Cerrar sidebar al hacer click fuera (móvil)
overlay.addEventListener("click", () => {
    sidebar.classList.remove("active");
    main.classList.remove("shifted");
    overlay.classList.remove("active");
    toggleBtn.classList.remove("rotado");
});

// ===============================
// CONFIRMACIÓN AL CERRAR SESIÓN
// Muestra un SweetAlert antes
// de redirigir al logout
// ===============================
document.querySelector(".logout").addEventListener("click", (e) => {
    e.preventDefault();

    Swal.fire({
        title: "¿Cerrar sesión?",
        text: "¿Estás seguro que deseas salir del sistema?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí, salir",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#C62828",
        cancelButtonColor: "#003A8F"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "../sesion/logout.php";
        }
    });
});