// ===============================
// REFERENCIAS DEL DOM
// ===============================
const btnEditar      = document.getElementById("btnEditar");
const btnGuardar     = document.getElementById("btnGuardar");
const btnCambiarFoto = document.getElementById("btnCambiarFoto");
const inputFoto      = document.getElementById("inputFoto");
const previewFoto    = document.getElementById("previewFoto");
const avatarIniciales = document.getElementById("avatarIniciales");

// ===============================
// PREVIEW DE FOTO AL SELECCIONAR
// Muestra la imagen inmediatamente
// sin necesitar guardar cambios
// ===============================
if (inputFoto) {
    inputFoto.addEventListener("change", (e) => {
        const archivo = e.target.files[0];
        if (!archivo) return;

        const reader = new FileReader();
        reader.onload = (event) => {

            if (previewFoto) {
                // Mostrar preview con la imagen seleccionada
                previewFoto.src = event.target.result;
                previewFoto.classList.remove("d-none");
            }

            // Ocultar avatar de iniciales si existe
            if (avatarIniciales) {
                avatarIniciales.classList.add("d-none");
            }
        };
        reader.readAsDataURL(archivo);
    });
}

// ===============================
// BOTÓN CAMBIAR FOTO
// Abre el selector de archivo
// solo cuando está habilitado
// ===============================
if (btnCambiarFoto) {
    btnCambiarFoto.addEventListener("click", () => {
        inputFoto.click();
    });
}

// ===============================
// PERFIL: EDITAR / GUARDAR
// Habilita campos y botón de foto
// al hacer click en editar
// ===============================
if (btnEditar && btnGuardar) {

    btnEditar.addEventListener("click", () => {

        // Campos que el usuario puede editar
        const camposEditables = [
            "telefono", "email", "direccion",
            "fecha_nacimiento", "nombre",
            "numero_ficha", "curp", "rfc"
        ];

        // Habilitar solo los campos permitidos
        document.querySelectorAll("input, textarea").forEach(el => {
            if (camposEditables.includes(el.name)) {
                el.removeAttribute("disabled");
            }
        });

        // Habilitar botón de foto
        if (btnCambiarFoto) {
            btnCambiarFoto.disabled      = false;
            btnCambiarFoto.style.opacity = "1";
            btnCambiarFoto.style.cursor  = "pointer";
        }

        // Mostrar guardar y ocultar editar
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