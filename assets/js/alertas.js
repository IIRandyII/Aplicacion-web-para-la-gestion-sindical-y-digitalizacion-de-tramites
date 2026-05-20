const alerta = document.getElementById("alerta");

if (alerta) {
    const esExito  = alerta.classList.contains("alert-success");
    const mensaje  = alerta.textContent.trim();

    // Ocultar el div original para no mostrarlo en el formulario
    alerta.style.display = "none";

    Swal.fire({
        toast:             true,
        position:          "top-end",       // esquina superior derecha
        icon:              esExito ? "success" : "error",
        title:             mensaje,
        showConfirmButton: false,
        timer:             5000,
        timerProgressBar:  true,
        didOpen: (toast) => {
            toast.addEventListener("mouseenter", Swal.stopTimer);
            toast.addEventListener("mouseleave", Swal.resumeTimer);
        }
    });
}