// ===============================
// VER DETALLE DEL TRÁMITE
// ===============================
function verTramite(id) {
    fetch("ver_tramite_afiliado.php?id=" + id)
        .then(res => res.text())
        .then(html => {
            document.getElementById("contenidoModal").innerHTML = html;
            const modal = new bootstrap.Modal(document.getElementById("modalTramite"));
            modal.show();
        });
}

// ===============================
// PAGINACIÓN + FILTROS
// ===============================
const REGISTROS_POR_PAGINA = 5;
let paginaActual = 1;

const inputBuscar  = document.getElementById("buscarArchivado");
const selectEstado = document.getElementById("filtroEstado");

function getFilasVisibles() {
    const texto  = inputBuscar.value.toLowerCase();
    const estado = selectEstado.value;
    const filas  = document.querySelectorAll("#tablaArchivados tbody tr");

    return Array.from(filas).filter(fila => {
        const contenido      = fila.textContent.toLowerCase();
        const estadoFila     = fila.dataset.estado;
        const coincideTexto  = contenido.includes(texto);
        const coincideEstado = estado === "Todos" || estadoFila === estado;
        return coincideTexto && coincideEstado;
    });
}

function renderPagina() {
    const filas        = document.querySelectorAll("#tablaArchivados tbody tr");
    const filasVisible = getFilasVisibles();
    const totalPaginas = Math.ceil(filasVisible.length / REGISTROS_POR_PAGINA);

    if (paginaActual > totalPaginas && totalPaginas > 0) paginaActual = totalPaginas;
    if (paginaActual < 1) paginaActual = 1;

    const inicio = (paginaActual - 1) * REGISTROS_POR_PAGINA;
    const fin    = inicio + REGISTROS_POR_PAGINA;

    filas.forEach(f => f.style.display = "none");
    filasVisible.slice(inicio, fin).forEach(f => f.style.display = "");

    const paginacion = document.getElementById("paginacion");

    if (totalPaginas <= 1) {
        paginacion.innerHTML = "";
        return;
    }

    let html = "";

    html += paginaActual > 1
        ? `<a class="pag-btn" onclick="irPagina(${paginaActual - 1})"><i class="fa-solid fa-chevron-left"></i></a>`
        : `<span class="pag-btn disabled"><i class="fa-solid fa-chevron-left"></i></span>`;

    for (let i = 1; i <= totalPaginas; i++) {
        html += `<a class="pag-btn ${i === paginaActual ? 'active' : ''}" onclick="irPagina(${i})">${i}</a>`;
    }

    html += paginaActual < totalPaginas
        ? `<a class="pag-btn" onclick="irPagina(${paginaActual + 1})"><i class="fa-solid fa-chevron-right"></i></a>`
        : `<span class="pag-btn disabled"><i class="fa-solid fa-chevron-right"></i></span>`;

    html += `<span class="pag-info">Página ${paginaActual} de ${totalPaginas} &nbsp;·&nbsp; ${filasVisible.length} registros en total</span>`;

    paginacion.innerHTML = html;
}

function irPagina(n) {
    paginaActual = n;
    renderPagina();
}

function aplicarFiltros() {
    paginaActual = 1;
    renderPagina();
}

inputBuscar.addEventListener("input",   aplicarFiltros);
selectEstado.addEventListener("change", aplicarFiltros);

renderPagina();