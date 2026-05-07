// ===============================
// PERFIL: EDITAR / GUARDAR
// Habilita los campos del formulario
// al hacer click en el botón editar
// ===============================
const btnEditar  = document.getElementById("btnEditar");
const btnGuardar = document.getElementById("btnGuardar");

if (btnEditar && btnGuardar) {

    btnEditar.addEventListener("click", () => {

        // Campos que el usuario puede editar
        const camposEditables = [
            "telefono",
            "email",
            "direccion",
            "fecha_nacimiento",
            "nombre",
            "numero_ficha",
            "curp",
            "rfc"
        ];

        // Habilitar solo los campos permitidos
        document.querySelectorAll("input, textarea").forEach(el => {
            if (camposEditables.includes(el.name)) {
                el.removeAttribute("disabled");
            }
        });

        // Mostrar botón guardar y ocultar editar
        btnGuardar.classList.remove("d-none");
        btnEditar.classList.add("d-none");
    });
}

// ===============================
// ALERTA DE PERFIL GUARDADO
// Se ejecuta solo cuando la URL
// contiene ?status=ok
// ===============================
document.addEventListener("DOMContentLoaded", () => {

    const params = new URLSearchParams(window.location.search);

    if (params.get('status') === 'ok') {

        Swal.fire({
            icon: 'success',
            title: 'Perfil actualizado',
            text: 'Tus cambios se guardaron correctamente',
            timer: 1600,
            showConfirmButton: false,
            toast: true,
            position: 'top',
            width: 300,
            background: '#ffffff',
            iconColor: '#22c55e',
            showClass: {
                popup: 'animate__animated animate__fadeInDown animate__faster'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp animate__faster'
            },
            customClass: {
                popup:         'swal-perfil',
                title:         'swal-title',
                htmlContainer: 'swal-text'
            }
        });

        // Limpiar ?status=ok de la URL
        const url = new URL(window.location);
        url.searchParams.delete('status');
        window.history.replaceState({}, document.title, url.pathname);
    }
});