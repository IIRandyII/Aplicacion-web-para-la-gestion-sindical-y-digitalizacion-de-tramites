// ===============================
// REFERENCIAS DEL DOM
// ===============================
const modal        = document.getElementById("modalTramite");
const btnAbrir     = document.getElementById("btnAbrirModal");
const cerrar       = document.querySelector("#modalTramite .close");
const departamento = document.getElementById("departamento");
const tipoTramite  = document.getElementById("tipoTramite");
const formulario   = document.getElementById("formularioTramite");
const inputBuscar  = document.getElementById("buscarTramite");
const selectOrden  = document.getElementById("ordenFecha");

// ===============================
// PAGINACIÓN
// ===============================
const FILAS_POR_PAGINA = 5;
let paginaActual       = 1;
let tramitesData       = [];

function renderizarTabla() {
    const texto = inputBuscar.value.toLowerCase();
    const orden = selectOrden.value;

    let filtrados = tramitesData.filter(t =>
        JSON.stringify(t).toLowerCase().includes(texto)
    );

    filtrados.sort((a, b) => {
        const fa = new Date(a.fecha_creacion);
        const fb = new Date(b.fecha_creacion);
        return orden === "asc" ? fa - fb : fb - fa;
    });

    const total        = filtrados.length;
    const totalPaginas = Math.max(1, Math.ceil(total / FILAS_POR_PAGINA));
    if (paginaActual > totalPaginas) paginaActual = totalPaginas;

    const inicio      = (paginaActual - 1) * FILAS_POR_PAGINA;
    const filasPagina = filtrados.slice(inicio, inicio + FILAS_POR_PAGINA);

    const tbody = document.getElementById("listaTramites");
    tbody.innerHTML = "";

    if (filasPagina.length === 0) {
        tbody.innerHTML = `<tr><td colspan="9" class="vacio">Aún no has creado trámites</td></tr>`;
    } else {
        filasPagina.forEach(t => {
            tbody.innerHTML += construirFila(t);
        });
    }

    document.getElementById("infoPagina").textContent = `Página ${paginaActual} de ${totalPaginas}`;
    document.getElementById("btnAnterior").disabled   = paginaActual === 1;
    document.getElementById("btnSiguiente").disabled  = paginaActual === totalPaginas;

    document.getElementById("paginacion").style.display =
        total <= FILAS_POR_PAGINA ? "none" : "flex";
}

function construirFila(t) {
    return `
        <tr data-fecha="${t.fecha_creacion}">
            <td>${t.id_tramite}</td>
            <td>${t.departamento}</td>
            <td>${t.tipo_tramite}</td>
            <td>${t.nombre_completo}</td>
            <td>${t.numero_ficha}</td>
            <td>${t.categoria}</td>
            <td>${t.turno}</td>
            <td><span class="badge bg-${getColorEstado(t.estado)}">${t.estado}</span></td>
            <td>
                <button class="btn-ver" data-id="${t.id_tramite}">
                    <i class="fa-solid fa-eye"></i> Ver
                </button>
            </td>
        </tr>
    `;
}

document.getElementById("btnAnterior").addEventListener("click", () => {
    if (paginaActual > 1) { paginaActual--; renderizarTabla(); }
});

document.getElementById("btnSiguiente").addEventListener("click", () => {
    paginaActual++;
    renderizarTabla();
});

// ===============================
// UTILIDADES
// ===============================
function getColorEstado(estado) {
    switch (estado) {
        case "Pendiente":   return "warning";
        case "En revisión": return "info";
        case "Aprobado":    return "success";
        case "Rechazado":   return "danger";
        default:            return "secondary";
    }
}

function formatearMonto(input) {
    let valor = input.value.replace(/[^0-9.]/g, '');
    const partes = valor.split('.');
    if (partes.length > 2) valor = partes[0] + '.' + partes.slice(1).join('');
    if (valor === "") { input.value = ""; return; }
    const numero = parseFloat(valor);
    if (!isNaN(numero)) {
        input.value = "$" + numero.toLocaleString("es-MX", {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        });
    }
}

function validarDescuento() {
    const select   = document.getElementById("selectDescuento");
    const divMonto = document.getElementById("divMonto");
    divMonto.style.display = select.value === "Sí" ? "block" : "none";
}

// ===============================
// MODAL: ABRIR / CERRAR
// ===============================
function resetearFormularioCompleto() {
    departamento.value = "";
    tipoTramite.innerHTML = '<option value="">Seleccione</option>';
    formulario.innerHTML  = "";
    document.querySelector(".fila-selects").style.display = "flex";
}

btnAbrir.onclick = () => {
    resetearFormularioCompleto();
    modal.style.display = "block";
};

cerrar.onclick = () => {
    modal.style.display = "none";
    resetearFormularioCompleto();
};

window.onclick = (e) => {
    if (e.target === modal) {
        modal.style.display = "none";
        resetearFormularioCompleto();
    }
};

// ===============================
// SELECTS: DEPARTAMENTO Y TRÁMITE
// ===============================
departamento.addEventListener("change", () => {
    tipoTramite.innerHTML = '<option value="">Seleccione trámite</option>';
    formulario.innerHTML  = "";

    const opciones = {
        "1": [
            "Registro de acuerdos sindicales",
            "Registro de asistencia a asambleas",
            "Copia de actas de asamblea"
        ],
        "2": [
            "Pago de cuotas sindicales",
            "Solicitud de comprobante de pago",
            "Regularización de adeudos"
        ],
        "3": [
            "Corrección de datos del trabajador",
            "Aclaración de incidencias laborales",
            "Regularización de situación administrativa"
        ]
    };

    const lista = opciones[departamento.value] || [];
    lista.forEach(op => {
        tipoTramite.innerHTML += `<option value="${op}">${op}</option>`;
    });
});

tipoTramite.addEventListener("change", () => {
    formulario.innerHTML = "";
    if (!tipoTramite.value) return;
    const datosGenerales   = getDatosGenerales();
    const datosEspecificos = getFormularioEspecifico(tipoTramite.value);
    formulario.innerHTML   = generarFormulario(datosGenerales, datosEspecificos);
});

// ===============================
// FORMULARIO MULTIPASO
// ===============================
function generarFormulario(datosGenerales, datosEspecificos) {
    const tramiteTexto = tipoTramite.options[tipoTramite.selectedIndex].text;
    return `
        <form id="formMultipaso" enctype="multipart/form-data">
            <input type="hidden" name="id_departamento" value="${departamento.value}">
            <input type="hidden" name="tipo_tramite"    value="${tramiteTexto}">

            <div id="paso1">
                ${datosGenerales}
                <div class="botones-paso">
                    <button type="button" class="btn-primary" onclick="siguientePaso()">
                        <i class="fa-solid fa-arrow-right"></i> Siguiente
                    </button>
                </div>
            </div>

            <div id="paso2" style="display:none;">
                ${datosEspecificos}
                <div class="botones-paso">
                    <button type="button" class="btn-secondary" onclick="regresarPaso()">
                        <i class="fa-solid fa-arrow-left"></i> Regresar
                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-paper-plane"></i> Enviar trámite
                    </button>
                </div>
            </div>
        </form>
    `;
}

function siguientePaso() {
    const paso1  = document.getElementById("paso1");
    const campos = paso1.querySelectorAll("input, select, textarea");
    for (let campo of campos) {
        if (!campo.checkValidity()) { campo.reportValidity(); return; }
    }
    document.querySelector(".fila-selects").style.display = "none";
    document.getElementById("paso1").style.display = "none";
    document.getElementById("paso2").style.display = "block";
}

function regresarPaso() {
    document.querySelector(".fila-selects").style.display = "flex";
    document.getElementById("paso1").style.display = "block";
    document.getElementById("paso2").style.display = "none";
}

// ===============================
// ENVÍO DEL FORMULARIO
// ===============================
document.addEventListener("submit", function (e) {
    if (e.target.id !== "formMultipaso") return;
    e.preventDefault();

    fetch("guardar_tramite.php", { method: "POST", body: new FormData(e.target) })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                tramitesData.unshift({
                    id_tramite:      data.id_tramite,
                    departamento:    data.departamento,
                    tipo_tramite:    data.tramite,
                    nombre_completo: data.nombre,
                    numero_ficha:    data.ficha,
                    categoria:       data.categoria,
                    turno:           data.turno,
                    estado:          "Pendiente",
                    fecha_creacion:  new Date().toISOString()
                });
                paginaActual = 1;
                renderizarTabla();

                modal.style.display = "none";
                e.target.reset();
                formulario.innerHTML = "";
                document.querySelector(".fila-selects").style.display = "flex";
                tipoTramite.value = "";
            } else {
                alert("Error al guardar el trámite");
            }
        });
});

// ===============================
// CARGA INICIAL DE TRÁMITES
// ===============================
function cargarTramites() {
    fetch("obtener_tramites.php")
        .then(res => res.json())
        .then(tramites => {
            tramitesData = tramites;
            paginaActual = 1;
            renderizarTabla();
        });
}

document.addEventListener("DOMContentLoaded", cargarTramites);

// ===============================
// BUSCADOR Y FILTRO DE FECHA
// ===============================
inputBuscar.addEventListener("input",  () => { paginaActual = 1; renderizarTabla(); });
selectOrden.addEventListener("change", () => { paginaActual = 1; renderizarTabla(); });

// ===============================
// VER DETALLE DEL TRÁMITE
// ===============================
document.addEventListener("click", function (e) {
    if (!e.target.closest(".btn-ver")) return;
    const idTramite = e.target.closest(".btn-ver").dataset.id;

    fetch(`../usuario/ver_tramite.php?id=${idTramite}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) mostrarDetalleTramite(data.tramite);
            else alert("No se pudo cargar el trámite");
        })
        .catch(() => alert("Error de conexión"));
});

function mostrarDetalleTramite(tramite) {
    const contenedor = document.getElementById("detalleTramite");
    const datos = JSON.parse(tramite.datos_especificos || "{}");

    let datosEspecificosHTML = "";
    for (const campo in datos) {
        datosEspecificosHTML += `
            <div class="detalle-fila">
                <span class="detalle-label">${campo.replace(/_/g, " ")}</span>
                <span class="detalle-valor">${datos[campo]}</span>
            </div>
        `;
    }

    const documentoHTML = tramite.documento_respaldo ? `
        <div class="detalle-documento">
            <div class="archivo-box">
                <div class="archivo-info">
                    <i class="fas fa-file-pdf"></i>
                    <span>${tramite.documento_respaldo.split('/').pop()}</span>
                </div>
                <a href="../${tramite.documento_respaldo}" download class="btn-descargar">
                    <i class="fas fa-download"></i> Descargar
                </a>
            </div>
        </div>
    ` : `
        <div class="detalle-documento">
            <p class="sin-documento"><i class="fas fa-info-circle"></i> Sin documento adjunto</p>
        </div>
    `;

    contenedor.innerHTML = `
        <div class="detalle-header">
            <div class="detalle-header-info">
                <span class="detalle-id">#${tramite.id_tramite}</span>
                <span class="badge bg-${getColorEstado(tramite.estado)} detalle-badge">${tramite.estado}</span>
            </div>
            <div class="detalle-header-meta">
                <span><i class="fas fa-building"></i> ${tramite.departamento}</span>
                <span><i class="fas fa-file-alt"></i> ${tramite.tipo_tramite}</span>
            </div>
        </div>

        <div class="detalle-body">
            <div class="detalle-seccion">
                <h4 class="detalle-seccion-titulo"><i class="fas fa-user"></i> Información general</h4>
                <div class="detalle-grid">
                    <div class="detalle-fila"><span class="detalle-label">Nombre</span><span class="detalle-valor">${tramite.nombre_completo}</span></div>
                    <div class="detalle-fila"><span class="detalle-label">Ficha</span><span class="detalle-valor">${tramite.numero_ficha}</span></div>
                    <div class="detalle-fila"><span class="detalle-label">Categoría</span><span class="detalle-valor">${tramite.categoria}</span></div>
                    <div class="detalle-fila"><span class="detalle-label">Turno</span><span class="detalle-valor">${tramite.turno}</span></div>
                    <div class="detalle-fila"><span class="detalle-label">Email</span><span class="detalle-valor">${tramite.email}</span></div>
                    <div class="detalle-fila"><span class="detalle-label">Teléfono</span><span class="detalle-valor">${tramite.telefono}</span></div>
                    <div class="detalle-fila"><span class="detalle-label">CURP</span><span class="detalle-valor">${tramite.curp}</span></div>
                </div>
            </div>

            <div class="detalle-seccion">
                <h4 class="detalle-seccion-titulo"><i class="fas fa-list-alt"></i> Datos específicos</h4>
                <div class="detalle-grid">${datosEspecificosHTML}</div>
            </div>

            <div class="detalle-seccion">
                <h4 class="detalle-seccion-titulo"><i class="fas fa-paperclip"></i> Documento adjunto</h4>
                ${documentoHTML}
            </div>
        </div>
    `;

    const modalVer = document.getElementById("modalVerTramite");
    modalVer.style.display = "block";
    const contenido = modalVer.querySelector(".modal-content.modal-grande");
    contenido.classList.remove("modal-entrada");
    void contenido.offsetWidth;
    contenido.classList.add("modal-entrada");
}

document.getElementById("cerrarVerTramite").onclick = () => {
    const modalVer  = document.getElementById("modalVerTramite");
    const contenido = modalVer.querySelector(".modal-content.modal-grande");
    contenido.classList.add("modal-salida");
    setTimeout(() => {
        modalVer.style.display = "none";
        contenido.classList.remove("modal-salida");
    }, 300);
};