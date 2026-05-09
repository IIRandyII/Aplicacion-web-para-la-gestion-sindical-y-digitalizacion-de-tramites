// ===============================
// MODAL CREAR/EDITAR AVISO
// ===============================
const modal        = document.getElementById("modalAviso");
const btnNuevo     = document.getElementById("btnNuevoAviso");
const btnCerrar    = document.getElementById("cerrarModalAviso");
const btnCancelar  = document.getElementById("cancelarAviso");
const modalTitulo  = document.getElementById("modalAvisoTitulo");
const avisoId      = document.getElementById("avisoId");
const avisoTitulo  = document.getElementById("avisoTitulo");
const avisoMensaje = document.getElementById("avisoMensaje");

// Abrir modal para nuevo aviso
btnNuevo.addEventListener("click", () => {
    modalTitulo.textContent = "Nuevo aviso";
    avisoId.value      = "";
    avisoTitulo.value  = "";
    avisoMensaje.value = "";
    modal.classList.add("activo");
});

// Cerrar modal
function cerrarModal() {
    modal.classList.remove("activo");
}

btnCerrar.addEventListener("click",   cerrarModal);
btnCancelar.addEventListener("click", cerrarModal);

// Cerrar al hacer click fuera del modal
modal.addEventListener("click", (e) => {
    if (e.target === modal) cerrarModal();
});

// ===============================
// EDITAR AVISO
// Precarga los datos en el modal
// ===============================
function editarAviso(id, titulo, mensaje) {
    modalTitulo.textContent = "Editar aviso";
    avisoId.value      = id;
    avisoTitulo.value  = titulo;
    avisoMensaje.value = mensaje;
    modal.classList.add("activo");
}

// ===============================
// ELIMINAR AVISO
// Confirmación con SweetAlert
// ===============================
function eliminarAviso(id) {
    Swal.fire({
        title: "¿Eliminar aviso?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#dc2626",
        cancelButtonColor: "#002855"
    }).then((result) => {
        if (result.isConfirmed) {
            fetch("eliminar_aviso.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: "success",
                        title: "Aviso eliminado",
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire("Error", "No se pudo eliminar el aviso", "error");
                }
            });
        }
    });
}

// ===============================
// GUARDAR AVISO (CREAR O EDITAR)
// ===============================
document.getElementById("formAviso").addEventListener("submit", (e) => {
    e.preventDefault();

    const datos = {
        id:      avisoId.value,
        titulo:  avisoTitulo.value.trim(),
        mensaje: avisoMensaje.value.trim()
    };

    fetch("guardar_aviso.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(datos)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: "success",
                title: datos.id ? "Aviso actualizado" : "Aviso publicado",
                timer: 1500,
                showConfirmButton: false
            }).then(() => location.reload());
        } else {
            Swal.fire("Error", "No se pudo guardar el aviso", "error");
        }
    });
});