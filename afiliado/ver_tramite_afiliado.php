<?php
require_once("../includes/auth_afiliado.php");
require_once __DIR__ . "/../config/db.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>ID inválido</div>";
    exit();
}

$id = intval($_GET['id']);

$sql = "SELECT * FROM tramites WHERE id_tramite = ?";
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
        case "Pendiente": return "warning";
        case "En revisión": return "info";
        case "Aprobado": return "success";
        case "Rechazado": return "danger";
        default: return "secondary";
    }
}
?>

<div class="row">
    <div class="col-md-6">
        <h5 class="mb-3">Información general</h5>

        <p><strong>ID:</strong> <?= $tramite['id_tramite'] ?></p>
        <p><strong>Nombre:</strong> <?= htmlspecialchars($tramite['nombre_completo']) ?></p>
        <p><strong>Trámite:</strong> <?= htmlspecialchars($tramite['tipo_tramite']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($tramite['email']) ?></p>
        <p><strong>Teléfono:</strong> <?= htmlspecialchars($tramite['telefono']) ?></p>
        <p><strong>CURP:</strong> <?= htmlspecialchars($tramite['curp']) ?></p>
        <p><strong>Estado:</strong><span class="badge bg-<?= getColorEstado($tramite['estado']) ?>">
        <?= htmlspecialchars($tramite['estado']) ?>
    </span>
</p>
    </div>

    <div class="col-md-6">
        <h5 class="mb-3">Datos específicos</h5>

        <?php
        $datos = json_decode($tramite['datos_especificos'] ?? '{}', true);

        if (!empty($datos) && is_array($datos)) {
            foreach ($datos as $campo => $valor) {
                echo "<p><strong>" 
                    . htmlspecialchars(ucfirst(str_replace('_', ' ', $campo))) 
                    . ":</strong> " 
                    . htmlspecialchars($valor) 
                    . "</p>";
            }
        } else {
            echo "<p>No hay datos específicos.</p>";
        }
        ?>
    </div>
</div>

<hr>

<h5>Cambiar estado</h5>

<p>
    <strong>Estado actual:</strong>
    <span class="badge bg-<?= getColorEstado($tramite['estado']) ?>">
        <?= htmlspecialchars($tramite['estado']) ?>
    </span>
</p>

<select id="nuevoEstado" class="form-select mb-3">
    <option value="En revisión" <?= $tramite['estado'] === 'En revisión' ? 'selected' : '' ?>>En revisión</option>
    <option value="Aprobado" <?= $tramite['estado'] === 'Aprobado' ? 'selected' : '' ?>>Aprobado</option>
    <option value="Rechazado" <?= $tramite['estado'] === 'Rechazado' ? 'selected' : '' ?>>Rechazado</option>
</select>

<button class="btn btn-primary"
        onclick="actualizarEstado(<?= $tramite['id_tramite'] ?>)">
    Guardar cambios
</button>

<h5 class="mt-3">Documento adjunto</h5>

<?php if (!empty($tramite['documento_respaldo'])): ?>

    <a href="../<?= htmlspecialchars($tramite['documento_respaldo']) ?>" 
       download 
       class="btn btn-success">
        <i class="fa-solid fa-download"></i> Descargar documento
    </a>

<?php else: ?>
    <div class="alert alert-warning">
        Este trámite no tiene documento adjunto.
    </div>
<?php endif; ?>
