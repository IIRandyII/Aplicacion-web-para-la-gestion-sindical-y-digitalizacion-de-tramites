// ===============================
// SIDEBAR
// ===============================
document.getElementById("toggleSidebar").addEventListener("click", () => {
    document.getElementById("sidebar").classList.toggle("active");
    document.querySelector(".main").classList.toggle("shifted");
});

function resetearFormularioCompleto() {

    // Limpiar selects principales
    departamento.value = "";
    tipoTramite.innerHTML = '<option value="">Seleccione</option>';

    // Limpiar formulario dinámico
    formulario.innerHTML = "";

    // Volver a mostrar fila de selects
    document.querySelector(".fila-selects").style.display = "flex";
}

// ===============================
// MODAL
// ===============================
const modal = document.getElementById("modalTramite");
const btnAbrir = document.getElementById("btnAbrirModal");
const cerrar = document.querySelector(".close");

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


function getColorEstado(estado) {
    switch (estado) {
        case "Pendiente": return "warning";
        case "En revisión": return "info";
        case "Aprobado": return "success";
        case "Rechazado": return "danger";
        default: return "secondary";
    }
}
// ===============================
// SELECTS
// ===============================
const departamento = document.getElementById("departamento");
const tipoTramite = document.getElementById("tipoTramite");
const formulario = document.getElementById("formularioTramite");

departamento.addEventListener("change", () => {
    tipoTramite.innerHTML = '<option value="">Seleccione trámite</option>';
    formulario.innerHTML = "";

    if (departamento.value == 3) {
        tipoTramite.innerHTML += `
            <option value="Corrección de datos del trabajador">Corrección de datos</option>
            <option value="Aclaración de incidencias laborales">Aclaración de incidencias</option>
            <option value="Regularización de situación administrativa">Regularización administrativa</option>
        `;
    }

    if (departamento.value == 2) {
        tipoTramite.innerHTML += `
            <option value="Pago de cuotas sindicales">Pago de cuotas</option>
            <option value="Solicitud de comprobante de pago">Comprobante de pago</option>
            <option value="Regularización de adeudos">Regularización de adeudos</option>
        `;
    }

    if (departamento.value == 1) {
        tipoTramite.innerHTML += `
            <option value="Registro de acuerdos sindicales">Registro de acuerdos</option>
            <option value="Registro de asistencia a asambleas">Registro de asistencia</option>
            <option value="Copia de actas de asamblea">Copia de actas</option>
        `;
    }
});

// ===============================
// FUNCIONES MULTIPASO
// ===============================

function validarDescuento() {
    const select = document.getElementById("selectDescuento");
    const divMonto = document.getElementById("divMonto");

    if (select.value === "Sí") {
        divMonto.style.display = "block";
    } else {
        divMonto.style.display = "none";
    }
}

function formatearMonto(input) {
    // Obtener solo números y punto
    let valor = input.value.replace(/[^0-9.]/g, '');

    // Evitar más de un punto decimal
    const partes = valor.split('.');
    if (partes.length > 2) {
        valor = partes[0] + '.' + partes.slice(1).join('');
    }

    // Si está vacío permitir borrar
    if (valor === "") {
        input.value = "";
        return;
    }

    // Convertir a número
    let numero = parseFloat(valor);

    if (!isNaN(numero)) {
        input.value = "$" + numero.toLocaleString("es-MX", {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        });
    }
}


function siguientePaso() {
    const paso1 = document.getElementById("paso1");
    const campos = paso1.querySelectorAll("input, select, textarea");

    for (let campo of campos) {
        if (!campo.checkValidity()) {
            campo.reportValidity();
            return;
        }
    }

    // Ocultar selects principales al pasar al paso 2
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
// ESTRUCTURA BASE
// ===============================

function generarFormulario(datosGenerales, datosEspecificos) {

    const depTexto = departamento.options[departamento.selectedIndex].text;
    const tramiteTexto = tipoTramite.options[tipoTramite.selectedIndex].text;

    return `
        <form id="formMultipaso" enctype="multipart/form-data">

            <input type="hidden" name="id_departamento" value="${departamento.value}">
            <input type="hidden" name="tipo_tramite" value="${tramiteTexto}">

            <div id="paso1">

                ${datosGenerales}

                <button type="button" class="btn-primary mt-3" onclick="siguientePaso()">
                    Siguiente
                </button>

            </div>

            <div id="paso2" style="display:none;">

                ${datosEspecificos}

                <div class="mt-3">
                    <button type="button" class="btn-secondary" onclick="regresarPaso()">
                        Regresar
                    </button>

                    <button type="submit" class="btn-primary">
                        Enviar trámite
                    </button>
                </div>

            </div>

        </form>
    `;
}

// ===============================
// FORMULARIOS DINÁMICOS
// ===============================

tipoTramite.addEventListener("change", () => {

    formulario.innerHTML = "";

    const datosGenerales = `
        <div class="form-section">
            <h4>Datos generales del trabajador</h4>

            <div class="form-grid">
                <input type="text" name="nombre_completo" required placeholder="Nombre completo">
                <input type="text" name="numero_ficha" required placeholder="Número de ficha">
                <input type="text" name="categoria" required placeholder="Categoría o puesto">
                <input type="text" name="turno" required placeholder="Turno">
                <input type="email" name="email" required placeholder="Email">
                <input type="text" name="curp" required placeholder="CURP">
                <input type="tel" name="telefono" required placeholder="Teléfono">
            </div>
        </div>
    `;

    let datosEspecificos = "";

    switch (tipoTramite.value) {

        case "Corrección de datos del trabajador":
            datosEspecificos = `
                <div class="form-section">
                    <h4>Datos a corregir</h4>

                    <select name="tipo_dato" required>
                        <option value="">Tipo de dato</option>
                        <option value="Nombre">Nombre</option>
                        <option value="CURP">CURP</option>
                        <option value="RFC">RFC</option>
                        <option value="Categoría">Categoría</option>
                        <option value="Número de ficha">Número de ficha</option>
                        <option value="Turno">Turno</option>
                        <option value="Antiguedad">Antigüedad</option>
                    </select>

                    <div class="form-grid">
                        <input type="text" name="dato_actual" required placeholder="Dato actual">
                        <input type="text" name="dato_correcto" required placeholder="Dato correcto">
                    </div>

                    <textarea name="motivo" required placeholder="Motivo de la corrección"></textarea>

                    <input type="file" name="documento_respaldo" accept="application/pdf" required>
                </div>
            `;
        break;

        case "Aclaración de incidencias laborales":
            datosEspecificos = `
                <div class="form-section">
                    <h4>Datos específicos de la incidencia</h4>
                <div class="fila-doble">
                <div>
                  <label>Tipo de incidencia</label>
                     <select name="tipo_incidencia" required>
            <option value="">Tipo de incidencia</option>
            <option value="Falta injustificada">Falta injustificada</option>
            <option value="Retardo">Retardo</option>
            <option value="Descuento indebido">Descuento indebido</option>
            <option value="Incapacidad no registrada">Incapacidad no registrada</option>
            <option value="Permiso no aplicado">Permiso no aplicado</option>
                 </select>
             </div>

            <div>
        <label>Fecha de incidencia</label>
        <input type="date" name="fecha_incidencia" required>
    </div>
</div>
                    <textarea name="descripcion" required placeholder="Descripción del problema"></textarea>

                    <div class="fila-doble-descuento">
    <div>
        <label>¿Hubo descuento?</label>
        <select name="descuento" id="selectDescuento" required onchange="validarDescuento()">
            <option value="">Seleccione</option>
            <option value="Sí">Sí</option>
            <option value="No">No</option>
        </select>
    </div>

    <div id="divMonto" style="display:none;">
        <label>Monto descontado</label>
        <input type="number" name="monto" placeholder="Monto descontado">
    </div>
</div>

                    <textarea name="justificacion" required placeholder="Justificación del trabajador"></textarea>
                    <input type="file" name="documento_respaldo" accept="application/pdf" required>
                </div>
            `;
        break;

        case "Regularización de situación administrativa":
            datosEspecificos = `
                <div class="form-section">
                    <h4>Datos de regularización</h4>

                    <div class="fila-doble">
    <div>
        <label>Tipo de regularización</label>
        <select name="tipo_regularizacion" required>
            <option value="">Tipo de regularización</option>
            <option value="Corrección de categoría">Corrección de categoría</option>
            <option value="Ajuste salarial">Ajuste salarial</option>
            <option value="Antigüedad">Antigüedad</option>
            <option value="Prestaciones">Prestaciones</option>
        </select>
    </div>

    <div>
        <label>Fecha de aplicación</label>
        <input type="date" name="fecha_aplicacion" required>
    </div>
</div>

                    <textarea name="motivo" required placeholder="Motivo de la regularización"></textarea>

                    <input type="file" name="documento_respaldo" accept="application/pdf" required>
                </div>
            `;
        break;

        case "Pago de cuotas sindicales":
            datosEspecificos = `
                <div class="form-section">
                    <h4>Datos del pago</h4>

                    <div class="fila-doble">
    <div>
        <label>Monto a pagar</label>
        <input type="text" id="montoPago" name="monto" required placeholder="$0.00" oninput="formatearMonto(this)">
    </div>

    <div>
        <label>Fecha de pago</label>
        <input type="date" name="fecha_pago" required>
    </div>
</div>

                    <div class="fila-doble">
    <div>
        <label>Periodo que se está cubriendo</label>
        <input type="text" name="periodo" required placeholder="Periodo que se está cubriendo">
    </div>

    <div>
        <label>Método de pago</label>
        <select name="metodo_pago" required>
            <option value="">Método de pago</option>
            <option value="Efectivo">Efectivo</option>
            <option value="Transferencia">Transferencia</option>
            <option value="Descuento vía nómina">Descuento vía nómina</option>
        </select>
    </div>
</div>

                    <input type="file" name="documento_respaldo" accept="application/pdf" required>
                </div>
            `;
        break;

        case "Solicitud de comprobante de pago":
            datosEspecificos = `
                <div class="form-section">
                    <h4>Datos del comprobante</h4>

                    <div class="fila-doble">
    <div>
        <label>Tipo de comprobante</label>
        <select name="tipo_comprobante" required>
            <option value="">Tipo de comprobante</option>
            <option value="Cuotas sindicales">Cuotas sindicales</option>
            <option value="Descuento especial">Descuento especial</option>
            <option value="Aportación extraordinaria">Aportación extraordinaria</option>
        </select>
    </div>

    <div>
        <label>Periodo del comprobante</label>
        <input type="text" name="periodo" required placeholder="Periodo del comprobante">
    </div>
</div>

                    <textarea name="motivo" required placeholder="Motivo de la solicitud"></textarea>
                </div>
            `;
        break;

        case "Regularización de adeudos":
            datosEspecificos = `
                <div class="form-section">
                    <h4>Datos del adeudo</h4>

                    <input type="text" name="periodo" required placeholder="Periodo adeudado">

                    <textarea name="descripcion" required placeholder="Descripción del adeudo"></textarea>

                    <select name="forma_regularizacion" required>
                        <option value="">Forma de regularización</option>
                        <option value="Pago en una sola exhibición">Pago en una sola exhibición</option>
                        <option value="Descuento vía nómina">Descuento vía nómina</option>
                        <option value="Convenio de pagos">Convenio de pagos</option>
                    </select>

                    <input type="file" name="documento_respaldo" accept="application/pdf" required>
                </div>
            `;
        break;

        case "Registro de acuerdos sindicales":
            datosEspecificos = `
                <div class="form-section">
                    <h4>Datos del acuerdo</h4>

                    <div class="fila-doble">
                        <div>
                            <label>Tipo de acuerdo</label>
                            <select name="tipo_acuerdo" required>
                                <option value="">Tipo de acuerdo</option>
                                <option value="Laboral">Laboral</option>
                                <option value="Administrativo">Administrativo</option>
                                <option value="Sindical">Sindical</option>
                                <option value="Extraordinario">Extraordinario</option>
                            </select>
                        </div>

                        <div>
                            <label>Fecha del acuerdo</label>
                            <input type="date" name="fecha_asamblea" required>
                        </div>
                    </div>

                    <input type="text" name="tema_acuerdo" required placeholder="Tema del acuerdo">

                    <textarea name="descripcion_acuerdo" required placeholder="Descripción del acuerdo"></textarea>

                    <input type="file" name="documento_respaldo" accept="application/pdf" required>
                </div>
            `;
        break;

        case "Registro de asistencia a asambleas":
            datosEspecificos = `
                <div class="form-section">
                    <h4>Datos de la asamblea</h4>

                    <div class="fila-doble">
                        <div>
                            <label>Tipo de asamblea</label>
                            <select name="tipo_asamblea" required>
                                <option value="">Tipo de asamblea</option>
                                <option value="Ordinaria">Ordinaria</option>
                                <option value="Extraordinaria">Extraordinaria</option>
                                <option value="Informativa">Informativa</option>
                            </select>
                        </div>

                        <div>
                            <label>Fecha de asamblea</label>
                            <input type="date" name="fecha_asamblea" required>
                        </div>
                    </div>

                    <input type="text" name="lugar_asamblea" required placeholder="Lugar de la asamblea">

                    <textarea name="observaciones" placeholder="Observaciones"></textarea>

                    <input type="file" name="documento_respaldo" accept="application/pdf" required>
                </div>
            `;
        break;

        case "Copia de actas de asamblea":
            datosEspecificos = `
                <div class="form-section">
                    <h4>Datos del acta solicitada</h4>

                    <div class="fila-doble">
                        <div>
                            <label>Tipo de acta</label>
                            <select name="tipo_acta" required>
                                <option value="">Tipo de acta</option>
                                <option value="Ordinaria">Ordinaria</option>
                                <option value="Extraordinaria">Extraordinaria</option>
                                <option value="Acuerdos">De acuerdos</option>
                            </select>
                        </div>

                        <div>
                            <label>Fecha del acta</label>
                            <input type="date" name="fecha_acta" required>
                        </div>
                    </div>

                    <textarea name="motivo_solicitud" required placeholder="Motivo de la solicitud"></textarea>

                    <select name="formato_entrega" required>
                        <option value="">Formato de entrega</option>
                        <option value="Digital">Digital</option>
                        <option value="Impreso">Impreso</option>
                    </select>
                </div>
            `;
        break;
}
    formulario.innerHTML = generarFormulario(datosGenerales, datosEspecificos);
});

    document.addEventListener("submit", function (e) {
    if (e.target.id === "formMultipaso") {
        e.preventDefault();

        const formData = new FormData(e.target);

        fetch("guardar_tramite.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
.then(data => {
    if (data.success) {

        agregarTramiteATabla({
            id_tramite: data.id_tramite,
            departamento: data.departamento,
            tramite: data.tramite,
            nombre: data.nombre,
            ficha: data.ficha,
            categoria: data.categoria,
            turno: data.turno
        });

        modal.style.display = "none";
        e.target.reset();
        formulario.innerHTML = "";
        document.querySelector(".fila-selects").style.display = "flex";
        tipoTramite.value = "";

    } else {
        alert("Error al guardar el trámite");
    }
});

    }
});

function agregarTramiteATabla(data) {

    const tbody = document.getElementById("listaTramites");

    if (tbody.querySelector(".vacio")) {
        tbody.innerHTML = "";
    }

    const fila = document.createElement("tr");

    // Fecha actual para el dataset
    fila.dataset.fecha = new Date().toISOString();

    fila.innerHTML = `
        <td>${data.id_tramite}</td>
        <td>${data.departamento}</td>
        <td>${data.tramite}</td>
        <td>${data.nombre}</td>
        <td>${data.ficha}</td>
        <td>${data.categoria}</td>
        <td>${data.turno}</td>
        <td>
        <span class="badge bg-${getColorEstado("Pendiente")}">
            Pendiente
        </span>
        </td>
        <td>
            <button class="btn-ver" data-id="${data.id_tramite}">
                <i class="fa-regular fa-eye"></i>
                <span>Ver más</span>
            </button>
        </td>
    `;

    tbody.prepend(fila);
}


document.addEventListener("click", function (e) {
    if (e.target.closest(".btn-ver")) {

        const btn = e.target.closest(".btn-ver");
        const idTramite = btn.dataset.id;

        fetch(`../usuario/ver_tramite.php?id=${idTramite}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    mostrarDetalleTramite(data.tramite);
                } else {
                    alert("No se pudo cargar el trámite");
                }
            })
            .catch(() => {
                alert("Error de conexión");
            });
    }
});

function mostrarDetalleTramite(tramite) {

    const contenedor = document.getElementById("detalleTramite");

    const estadoClase = tramite.estado
        ? tramite.estado.toLowerCase().replace(" ", "-")
        : "pendiente";

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
                <span class="badge bg-${getColorEstado(tramite.estado)}">
                    ${tramite.estado}
                </span>
                </div>
            </div>

            <div class="card-detalle">
                <h3>Datos específicos</h3>
    `;

    const datos = JSON.parse(tramite.datos_especificos || "{}");

    for (const campo in datos) {
        html += `
            <div class="detalle-item">
                <strong>${campo.replace(/_/g, " ")}:</strong> ${datos[campo]}
            </div>
        `;
    }

    html += `</div>`;

    // ===============================
    // DOCUMENTO PDF (SI EXISTE)
    // ===============================
  if (tramite.documento_respaldo) {
    html += `
        <div class="detalle-bloque">
            <h4>Documento adjunto</h4>
            
            <div class="archivo-box">
                <i class="fas fa-file-pdf"></i>
                <span>${tramite.documento_respaldo.split('/').pop()}</span>
                
                <a href="../${tramite.documento_respaldo}" 
                   download 
                   class="btn-descargar">
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

document.getElementById("cerrarVerTramite").onclick = () => {
    document.getElementById("modalVerTramite").style.display = "none";
};

document.addEventListener("DOMContentLoaded", () => {
    cargarTramites();
});

function cargarTramites() {
    fetch("obtener_tramites.php")
        .then(res => res.json())
        .then(tramites => {
            const tbody = document.getElementById("listaTramites");
            tbody.innerHTML = "";

            if (tramites.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="vacio">
                            Aún no has creado trámites
                        </td>
                    </tr>
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
            <td>
                <span class="badge bg-${getColorEstado(t.estado)}">
                     ${t.estado}
                </span>
            </td>
            <td>
                <button class="btn-ver" data-id="${t.id_tramite}">
                    <i class="fa-solid fa-eye"></i>
                    Ver
                </button>
            </td>
        </tr>
    `;
});
        });
}



// ===============================
// BUSCADOR Y ORDEN
// ===============================

const inputBuscar = document.getElementById("buscarTramite");
const selectOrden = document.getElementById("ordenFecha");

if (inputBuscar && selectOrden) {

    inputBuscar.addEventListener("input", aplicarFiltros);
    selectOrden.addEventListener("change", aplicarFiltros);

    function aplicarFiltros() {

        const texto = inputBuscar.value.toLowerCase();
        const orden = selectOrden.value;

        const filas = Array.from(document.querySelectorAll("#listaTramites tr"));

        // FILTRAR
        filas.forEach(fila => {
            const contenido = fila.textContent.toLowerCase();
            fila.style.display = contenido.includes(texto) ? "" : "none";
        });

        // ORDENAR SOLO LAS VISIBLES
        const visibles = filas.filter(f => f.style.display !== "none");

        visibles.sort((a, b) => {

            const fechaA = new Date(a.dataset.fecha);
            const fechaB = new Date(b.dataset.fecha);

            return orden === "asc"
                ? fechaA - fechaB
                : fechaB - fechaA;
        });

        const tbody = document.getElementById("listaTramites");
        visibles.forEach(fila => tbody.appendChild(fila));
    }
}
