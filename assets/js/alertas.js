    const alerta = document.getElementById("alerta");
    if (alerta) {
        setTimeout(() => {
            alerta.classList.add("ocultar");
            setTimeout(() => alerta.remove(), 600);
        }, 3000);
    }