// ===============================
// REFERENCIAS DEL DOM
// ===============================
const modal       = document.getElementById("modalTramite");
const btnAbrir    = document.getElementById("btnAbrirModal");
const cerrar      = document.querySelector(".close");
const departamento = document.getElementById("departamento");
const tipoTramite  = document.getElementById("tipoTramite");
const formulario   = document.getElementById("formularioTramite");
const inputBuscar  = document.getElementById("buscarTramite");
const selectOrden  = document.getElementById("ordenFecha");

// ===============================
// UTILIDADES
// ===============================

// Retorna clase de color Bootstrap según estado del trámite
function getColorEstado(estado) {
    switch (estado) {
        case "Pendiente":   return "warning";
        case "En revisión": return "info";
        case "Aprobado":    return "success";
        case "Rechazado":   return "danger";
        default:            return "secondary";
    }
}

// Formatea input de monto a formato moneda mexicana
function formatearMonto(input) {
    let valor = input.value.replace(/[^0-9.]/g, '');
    const partes = valor.split('.');
    if (partes.length > 2) valor = partes[0] + '.' + partes.slice(1).join('');
    if (valor === "") { input.value = ""; return; }
    let numero = parseFloat(valor);
    if (!isNaN(numero)) {
        input.value = "$" + numero.toLocaleString("es-MX", {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        });
    }
}

// Muestra/oculta campo de monto según si hubo descuento
function validarDescuento() {
    const select  = document.getElementById("selectDescuento");
    const divMonto = document.getElementById("divMonto");
    divMonto.style.display = select.value === "Sí" ? "block" : "none";
}

// ===============================
// MODAL: ABRIR / CERRAR
// ===============================

// Resetea el formulario completo al abrir o cerrar el modal
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

// Cerrar modal al hacer click fuera
window.onclick = (e) => {
    if (e.target === modal) {
        modal.style.display = "none";
        resetearFormularioCompleto();
    }
};

// ===============================
// SELECTS: DEPARTAMENTO Y TRÁMITE
// ===============================

// Carga los tipos de trámite según el departamento seleccionado
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

// Genera el formulario dinámico al seleccionar tipo de trámite
tipoTramite.addEventListener("change", () => {
    formulario.innerHTML = "";
    if (!tipoTramite.value) return;

    const datosGenerales   = getDatosGenerales();
    const datosEspecificos = getFormularioEspecifico(tipoTramite.value);

    formulario.innerHTML = generarFormulario(datosGenerales, datosEspecificos);
});

// ===============================
// FORMULARIO MULTIPASO
// ===============================

// Estructura base del formulario en dos pasos
function generarFormulario(datosGenerales, datosEspecificos) {
    const tramiteTexto = tipoTramite.options[tipoTramite.selectedIndex].text;
    return `
        <form id="formMultipaso" enctype="multipart/form-data">
            <input type="hidden" name="id_departamento" value="${departamento.value}">
            <input type="hidden" name="tipo_tramite"    value="${tramiteTexto}">

            <div id="paso1">
                ${datosGenerales}
                <button type="button" class="btn-primary mt-3" onclick="siguientePaso()">
                    Siguiente
                </button>
            </div>

            <div id="paso2" style="display:none;">
                ${datosEspecificos}
                <div class="mt-3">
                    <button type="button" class="btn-secondary" onclick="regresarPaso()">Regresar</button>
                    <button type="submit"  class="btn-primary">Enviar trámite</button>
                </div>
            </div>
        </form>
    `;
}

// Valida paso 1 y avanza al paso 2
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

// Regresa al paso 1
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
                agregarTramiteATabla(data);
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
// TABLA DE TRÁMITES
// ===============================

// Agrega una fila nueva a la tabla al crear un trámite
function agregarTramiteATabla(data) {
    const tbody = document.getElementById("listaTramites");
    if (tbody.querySelector(".vacio")) tbody.innerHTML = "";

    const fila = document.createElement("tr");
    fila.dataset.fecha = new Date().toISOString();
    fila.innerHTML = `
        <td>${data.id_tramite}</td>
        <td>${data.departamento}</td>
        <td>${data.tramite}</td>
        <td>${data.nombre}</td>
        <td>${data.ficha}</td>
        <td>${data.categoria}</td>
        <td>${data.turno}</td>
        <td><span class="badge bg-${getColorEstado("Pendiente")}">Pendiente</span></td>
        <td>
            <button class="btn-ver" data-id="${data.id_tramite}">
                <i class="fa-regular fa-eye"></i> Ver más
            </button>
        </td>
    `;
    tbody.prepend(fila);
}

// Carga los trámites existentes desde el servidor al cargar la página
function cargarTramites() {
    fetch("obtener_tramites.php")
        .then(res => res.json())
        .then(tramites => {
            const tbody = document.getElementById("listaTramites");
            tbody.innerHTML = "";

            if (tramites.length === 0) {
                tbody.innerHTML = `
                    <tr><td colspan="8" class="vacio">Aún no has creado trámites</td></tr>
                `;
                return;
            }

            tramites.forEach(t => {
                tbody.innerHTML += `
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
            });
        });
}

document.addEventListener("DOMContentLoaded", cargarTramites);

// ===============================
// VER DETALLE DEL TRÁMITE
// ===============================

// Abre el modal de detalle al hacer click en "Ver"
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

// Construye y muestra el contenido del modal de detalle
function mostrarDetalleTramite(tramite) {
    const contenedor = document.getElementById("detalleTramite");

    let html = `
        <div class="modal-grid">
            <div class="card-detalle">
                <h3>Información general</h3>
                <div class="detalle-item"><strong>ID:</strong> ${tramite.id_tramite}</div>
                <div class="detalle-item"><strong>Departamento:</strong> ${tramite.departamento}</div>
                <div class="detalle-item"><strong>Trámite:</strong> ${tramite.tipo_tramite}</div>
                <div class="detalle-item"><strong>Nombre:</strong> ${tramite.nombre_completo}</div>
                <div class="detalle-item"><strong>Ficha:</strong> ${tramite.numero_ficha}</div>
                <div class="detalle-item"><strong>Categoría:</strong> ${tramite.categoria}</div>
                <div class="detalle-item"><strong>Turno:</strong> ${tramite.turno}</div>
                <div class="detalle-item"><strong>Email:</strong> ${tramite.email}</div>
                <div class="detalle-item"><strong>Teléfono:</strong> ${tramite.telefono}</div>
                <div class="detalle-item"><strong>CURP:</strong> ${tramite.curp}</div>
                <div class="estado-contenedor">
                    <span class="badge bg-${getColorEstado(tramite.estado)}">${tramite.estado}</span>
                </div>
            </div>

            <div class="card-detalle">
                <h3>Datos específicos</h3>
    `;

    // Renderizar datos específicos del trámite
    const datos = JSON.parse(tramite.datos_especificos || "{}");
    for (const campo in datos) {
        html += `
            <div class="detalle-item">
                <strong>${campo.replace(/_/g, " ")}:</strong> ${datos[campo]}
            </div>
        `;
    }

    html += `</div>`;

    // Documento adjunto si existe
    if (tramite.documento_respaldo) {
        html += `
            <div class="detalle-bloque">
                <h4>Documento adjunto</h4>
                <div class="archivo-box">
                    <i class="fas fa-file-pdf"></i>
                    <span>${tramite.documento_respaldo.split('/').pop()}</span>
                    <a href="../${tramite.documento_respaldo}" download class="btn-descargar">
                        Descargar PDF
                    </a>
                </div>
            </div>
        `;
    }

    html += `</div>`;
    contenedor.innerHTML = html;
    document.getElementById("modalVerTramite").style.display = "block";
}

// Cerrar modal de detalle
document.getElementById("cerrarVerTramite").onclick = () => {
    document.getElementById("modalVerTramite").style.display = "none";
};

// ===============================
// BUSCADOR Y FILTRO DE FECHA
// ===============================
if (inputBuscar && selectOrden) {

    inputBuscar.addEventListener("input", aplicarFiltros);
    selectOrden.addEventListener("change", aplicarFiltros);

    function aplicarFiltros() {
        const texto = inputBuscar.value.toLowerCase();
        const orden = selectOrden.value;
        const filas = Array.from(document.querySelectorAll("#listaTramites tr"));

        // Filtrar por texto
        filas.forEach(fila => {
            fila.style.display = fila.textContent.toLowerCase().includes(texto) ? "" : "none";
        });

        // Ordenar por fecha
        const visibles = filas.filter(f => f.style.display !== "none");
        visibles.sort((a, b) => {
            const fechaA = new Date(a.dataset.fecha);
            const fechaB = new Date(b.dataset.fecha);
            return orden === "asc" ? fechaA - fechaB : fechaB - fechaA;
        });

        const tbody = document.getElementById("listaTramites");
        visibles.forEach(fila => tbody.appendChild(fila));
    }
}