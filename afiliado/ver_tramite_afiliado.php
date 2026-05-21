<?php
require_once("../includes/auth_afiliado.php");
require_once __DIR__ . "/../config/db.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>ID inválido</div>";
    exit();
}

$id   = intval($_GET['id']);
$sql  = "SELECT t.*, d.nombre AS departamento FROM tramites t JOIN departamentos d ON t.id_departamento = d.id_departamento WHERE t.id_tramite = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-danger'>Trámite no encontrado</div>";
    exit();
}

$tramite = $result->fetch_assoc();

function getColorEstado($estado) {
    switch ($estado) {
        case "Pendiente":   return "warning";
        case "En revisión": return "info";
        case "Aprobado":    return "success";
        case "Rechazado":   return "danger";
        default:            return "secondary";
    }
}

$datos = json_decode($tramite['datos_especificos'] ?? '{}', true);

$datosEspecificosHTML = "";
foreach ($datos as $campo => $valor) {
    $datosEspecificosHTML .= "
        <div class='detalle-fila'>
            <span class='detalle-label'>{$campo}</span>
            <span class='detalle-valor'>" . htmlspecialchars($valor) . "</span>
        </div>
    ";
}

$documentoHTML = !empty($tramite['documento_respaldo']) ? "
    <div class='detalle-documento'>
        <div class='archivo-box'>
            <div class='archivo-info'>
                <i class='fas fa-file-pdf'></i>
                <span>" . basename($tramite['documento_respaldo']) . "</span>
            </div>
            <a href='../" . htmlspecialchars($tramite['documento_respaldo']) . "' download class='btn-descargar'>
                <i class='fas fa-download'></i> Descargar
            </a>
        </div>
    </div>
" : "<div class='detalle-documento'><p class='sin-documento'><i class='fas fa-info-circle'></i> Sin documento adjunto</p></div>";
?>

<!-- HEADER -->
<div class="detalle-header">
    <button type="button" class="btn-close btn-close-white detalle-cerrar" data-bs-dismiss="modal"></button>
    <div class="detalle-header-info">
        <span class="detalle-id">#<?= $tramite['id_tramite'] ?></span>
        <span class="badge bg-<?= getColorEstado($tramite['estado']) ?> detalle-badge"><?= $tramite['estado'] ?></span>
    </div>
    <div class="detalle-header-meta">
        <span><i class="fas fa-building"></i> <?= htmlspecialchars($tramite['departamento']) ?></span>
        <span><i class="fas fa-file-alt"></i> <?= htmlspecialchars($tramite['tipo_tramite']) ?></span>
    </div>
</div>

<!-- BODY -->
<div class="detalle-body" style="padding: 20px;">

    <!-- INFORMACIÓN GENERAL -->
    <div class="detalle-seccion">
        <h4 class="detalle-seccion-titulo"><i class="fas fa-user"></i> Información general</h4>
        <div class="detalle-grid">
            <div class="detalle-fila"><span class="detalle-label">Nombre</span><span class="detalle-valor"><?= htmlspecialchars($tramite['nombre_completo']) ?></span></div>
            <div class="detalle-fila"><span class="detalle-label">Ficha</span><span class="detalle-valor"><?= htmlspecialchars($tramite['numero_ficha']) ?></span></div>
            <div class="detalle-fila"><span class="detalle-label">Categoría</span><span class="detalle-valor"><?= htmlspecialchars($tramite['categoria']) ?></span></div>
            <div class="detalle-fila"><span class="detalle-label">Turno</span><span class="detalle-valor"><?= htmlspecialchars($tramite['turno']) ?></span></div>
            <div class="detalle-fila"><span class="detalle-label">Email</span><span class="detalle-valor"><?= htmlspecialchars($tramite['email']) ?></span></div>
            <div class="detalle-fila"><span class="detalle-label">Teléfono</span><span class="detalle-valor"><?= htmlspecialchars($tramite['telefono']) ?></span></div>
            <div class="detalle-fila"><span class="detalle-label">CURP</span><span class="detalle-valor"><?= htmlspecialchars($tramite['curp']) ?></span></div>
        </div>
    </div>

    <!-- DATOS ESPECÍFICOS -->
    <div class="detalle-seccion">
        <h4 class="detalle-seccion-titulo"><i class="fas fa-list-alt"></i> Datos específicos</h4>
        <div class="detalle-grid"><?= $datosEspecificosHTML ?></div>
    </div>

    <!-- DOCUMENTO -->
    <div class="detalle-seccion">
        <h4 class="detalle-seccion-titulo"><i class="fas fa-paperclip"></i> Documento adjunto</h4>
        <?= $documentoHTML ?>
    </div>

    <!-- CAMBIAR ESTADO -->
<div class="detalle-seccion">
    <h4 class="detalle-seccion-titulo"><i class="fas fa-exchange-alt"></i> Actualizar estado</h4>
    <div class="detalle-grid">
        <div class="detalle-fila" style="flex-direction: column; gap: 8px;">
            <span class="detalle-label">Nuevo estado</span>
            <select id="nuevoEstado" class="form-select">
                <option value="Pendiente"   <?= $tramite['estado'] === 'Pendiente'   ? 'selected' : '' ?>>Pendiente</option>
                <option value="En revisión" <?= $tramite['estado'] === 'En revisión' ? 'selected' : '' ?>>En revisión</option>
                <option value="Aprobado"    <?= $tramite['estado'] === 'Aprobado'    ? 'selected' : '' ?>>Aprobado</option>
                <option value="Rechazado"   <?= $tramite['estado'] === 'Rechazado'   ? 'selected' : '' ?>>Rechazado</option>
            </select>
        </div>
        <div class="detalle-fila" style="flex-direction: column; gap: 8px;">
            <span class="detalle-label">Comentario <small class="text-muted">(obligatorio si se rechaza)</small></span>
            <textarea id="comentarioEstado" class="form-control" rows="3" 
                      placeholder="Escribe una justificación o comentario..."></textarea>
        </div>
    </div>
    <div style="margin-top: 12px; text-align: right;">
        <button class="btn btn-primary" onclick="actualizarEstado(<?= $tramite['id_tramite'] ?>)">
            <i class="fas fa-save"></i> Guardar cambios
        </button>
    </div>
</div>

</div>