// ===============================
// DATOS GENERALES DEL TRABAJADOR
// Campos comunes a todos los trámites
// ===============================
function getDatosGenerales() {
    return `
        <div class="form-section">
            <h4>Datos generales del trabajador</h4>
            <div class="form-grid">
                <input type="text"  name="nombre_completo" required placeholder="Nombre completo">
                <input type="text"  name="numero_ficha"    required placeholder="Número de ficha">
                <input type="text"  name="categoria"       required placeholder="Categoría o puesto">
                <input type="text"  name="turno"           required placeholder="Turno">
                <input type="email" name="email"           required placeholder="Email">
                <input type="text"  name="curp"            required placeholder="CURP">
                <input type="tel"   name="telefono"        required placeholder="Teléfono">
            </div>
        </div>
    `;
}

// ===============================
// FORMULARIOS ESPECÍFICOS
// Retorna el HTML según el tipo de trámite
// ===============================
function getFormularioEspecifico(tipo) {

    switch (tipo) {

        // -------------------------------------------------------
        // SECRETARÍA DE AJUSTES
        // -------------------------------------------------------

        case "Corrección de datos del trabajador":
            return `
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
                        <input type="text" name="dato_actual"  required placeholder="Dato actual">
                        <input type="text" name="dato_correcto" required placeholder="Dato correcto">
                    </div>
                    <textarea name="motivo" required placeholder="Motivo de la corrección"></textarea>
                    <input type="file" name="documento_respaldo" accept="application/pdf" required>
                </div>
            `;

        case "Aclaración de incidencias laborales":
            return `
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

        case "Regularización de situación administrativa":
            return `
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

        // -------------------------------------------------------
        // TESORERÍA
        // -------------------------------------------------------

        case "Pago de cuotas sindicales":
            return `
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

        case "Solicitud de comprobante de pago":
            return `
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

        case "Regularización de adeudos":
            return `
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

        // -------------------------------------------------------
        // SECRETARÍA DE ACTAS
        // -------------------------------------------------------

        case "Registro de acuerdos sindicales":
            return `
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

        case "Registro de asistencia a asambleas":
            return `
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

        case "Copia de actas de asamblea":
            return `
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

        default:
            return "";
    }
}