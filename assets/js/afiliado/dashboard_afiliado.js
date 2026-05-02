let estadoActual = "Todos";
let busquedaActual = "";
let fechaActual = "Todos";
// Sidebar toggle
document.getElementById("toggleSidebar").addEventListener("click", () => {
    document.getElementById("sidebar").classList.toggle("active");
    document.querySelector(".main").classList.toggle("shifted");
});

// Al cargar la página
document.addEventListener("DOMContentLoaded", () => {
    cargarTramites("Todos", document.querySelector(".info-card"));
    cargarContadores();
});

// Buscador en tiempo real
document.getElementById("buscador").addEventListener("input", function() {
    busquedaActual = this.value;
    cargarTramites(estadoActual);
});

// Filtro por fecha
document.getElementById("filtroFecha").addEventListener("change", function() {
    fechaActual = this.value;
    cargarTramites(estadoActual);
});

function cargarTramites(estado, elementoCard = null) {

    estadoActual = estado;

    document.querySelectorAll(".info-card").forEach(card => {
        card.classList.remove("active");
    });

    if (elementoCard) {
        elementoCard.classList.add("active");
    }

    fetch(`obtener_tramites_afiliado.php?estado=${encodeURIComponent(estadoActual)}&buscar=${encodeURIComponent(busquedaActual)}&fecha=${encodeURIComponent(fechaActual)}`)
        .then(res => res.json())
        .then(data => {

            const tabla = document.getElementById("tabla_tramites");
            tabla.innerHTML = "";

            if (data.length === 0) {
                tabla.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center">No hay trámites</td>
                    </tr>
                `;
                return;
            }

            data.forEach(tramite => {
                tabla.innerHTML += `
                    <tr>
                        <td>${tramite.id_tramite}</td>
                        <td>${tramite.nombre_completo}</td>
                        <td>${tramite.tipo_tramite}</td>
                        <td>
                            <span class="badge bg-${getColorEstado(tramite.estado)}">
                                ${tramite.estado}
                            </span>
                        </td>
                        <td>${tramite.fecha_creacion}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" 
                                onclick="verTramite(${tramite.id_tramite})">
                                <i class="fa-solid fa-eye"></i> Ver más
                            </button>
                        </td>
                    </tr>
                `;
            });
        });
}

// Función para cargar contadores de las cards
function cargarContadores() {
    fetch("contar_tramites_afiliado.php")
        .then(res => res.json())
        .then(data => {
            document.getElementById("count_todos").textContent = data.total ?? 0;
            document.getElementById("count_pendientes").textContent = data.pendientes ?? 0;
            document.getElementById("count_revision").textContent = data.revision ?? 0;
            document.getElementById("count_aprobados").textContent = data.aprobados ?? 0;
            document.getElementById("count_rechazados").textContent = data.rechazados ?? 0;
        });
}

// Colores visuales por estado (extra profesional)
function getColorEstado(estado) {
    switch (estado) {
        case "Pendiente": return "warning";
        case "En revisión": return "info";
        case "Aprobado": return "success";
        case "Rechazado": return "danger";
        default: return "secondary";
    }
}

function verTramite(id) {
    fetch("ver_tramite_afiliado.php?id=" + id)
        .then(res => res.text()) // 👈 CAMBIAR A text()
        .then(html => {

            document.getElementById("contenidoModal").innerHTML = html;

            const modal = new bootstrap.Modal(document.getElementById("modalTramite"));
            modal.show();
        })
        .catch(() => {
            document.getElementById("contenidoModal").innerHTML = `
                <div class="alert alert-danger">
                    Error de conexión con el servidor.
                </div>
            `;
            const modal = new bootstrap.Modal(document.getElementById("modalTramite"));
            modal.show();
        });
}

function actualizarEstado(id) {

    const estado = document.getElementById("nuevoEstado").value;

    Swal.fire({
        title: '¿Guardar cambios?',
        text: 'Se actualizará el estado del trámite',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33'
    }).then((result) => {

        if (result.isConfirmed) {

            fetch("actualizar_estado_afiliado.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    id: id,
                    estado: estado
                })
            })
            .then(res => res.json())
            .then(data => {

                if (data.success) {

                    Swal.fire({
                        icon: 'success',
                        title: '¡Actualizado!',
                        text: 'Estado actualizado correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    setTimeout(() => {
                        location.reload();
                    }, 2000);

                } else {

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo actualizar el estado'
                    });

                }

            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un problema con la conexión'
                });
            });

        }

    });
}