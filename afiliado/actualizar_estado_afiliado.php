<?php
require_once("../includes/auth_afiliado.php");
require_once("../config/db.php");

// Respuesta en formato JSON
header("Content-Type: application/json");

// ===============================
// RECIBIR Y VALIDAR DATOS
// ===============================
$data        = json_decode(file_get_contents("php://input"), true);
$id          = intval($data['id']);
$nuevoEstado = $data['estado'];
$comentario  = trim($data['comentario'] ?? '');
$id_afiliado = $_SESSION['id_usuario'];

// ===============================
// OBTENER DEPARTAMENTO DEL AFILIADO
// ===============================
$stmt = $conn->prepare("SELECT id_departamento FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id_afiliado);
$stmt->execute();
$usuario         = $stmt->get_result()->fetch_assoc();
$id_departamento = $usuario['id_departamento'];

// ===============================
// OBTENER DATOS DEL TRÁMITE
// Se valida que pertenezca al departamento
// ===============================
$stmt = $conn->prepare("
    SELECT estado, id_usuario, tipo_tramite
    FROM tramites
    WHERE id_tramite = ? AND id_departamento = ?
");
$stmt->bind_param("ii", $id, $id_departamento);
$stmt->execute();
$tramite = $stmt->get_result()->fetch_assoc();

if (!$tramite) {
    echo json_encode(["success" => false]);
    exit;
}

$estadoAnterior     = $tramite['estado'];
$id_usuario_creador = $tramite['id_usuario'];
$tipo_tramite       = $tramite['tipo_tramite'];

// ===============================
// VERIFICAR QUE EL ESTADO CAMBIÓ
// ===============================
if ($estadoAnterior === $nuevoEstado) {
    echo json_encode(["success" => true]);
    exit;
}

// ===============================
// ACTUALIZAR ESTADO DEL TRÁMITE
// Si es Aprobado o Rechazado
// se archiva automáticamente
// ===============================
$archivado = ($nuevoEstado === 'Aprobado' || $nuevoEstado === 'Rechazado') ? 1 : 0;

$stmt = $conn->prepare("
    UPDATE tramites
    SET estado = ?, archivado = ?
    WHERE id_tramite = ? AND id_departamento = ?
");
$stmt->bind_param("siii", $nuevoEstado, $archivado, $id, $id_departamento);

if ($stmt->execute() && $stmt->affected_rows > 0) {

    // ===============================
    // REGISTRAR EN HISTORIAL
    // Incluye el comentario del afiliado
    // ===============================
    $stmtHistorial = $conn->prepare("
        INSERT INTO historial_tramites
        (id_tramite, id_afiliado, estado_anterior, estado_nuevo, comentario, fecha_cambio)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $stmtHistorial->bind_param("iisss", $id, $id_afiliado, $estadoAnterior, $nuevoEstado, $comentario);
    $stmtHistorial->execute();

    // ===============================
    // GENERAR MENSAJE DE NOTIFICACIÓN
    // El comentario se agrega al mensaje
    // si fue proporcionado por el afiliado
    // ===============================
    switch ($nuevoEstado) {

        case "En revisión":
            $titulo  = "Su trámite se encuentra en revisión";
            $mensaje = "Le informamos que su trámite de \"$tipo_tramite\" ha pasado a estado 'En revisión'. Nuestro equipo se encuentra analizándolo.";
            if (!empty($comentario)) {
                $mensaje .= " Nota: $comentario";
            }
            break;

        case "Aprobado":
            $titulo  = "Trámite aprobado";
            $mensaje = "Nos complace informarle que su trámite de \"$tipo_tramite\" ha sido aprobado exitosamente.";
            if (!empty($comentario)) {
                $mensaje .= " Nota: $comentario";
            }
            break;

        case "Rechazado":
            $titulo  = "Trámite rechazado";
            $mensaje = "Le informamos que su trámite de \"$tipo_tramite\" ha sido rechazado.";
            if (!empty($comentario)) {
                $mensaje .= " Motivo: $comentario";
            }
            break;

        default:
            $titulo  = "Actualización de trámite";
            $mensaje = "El estado de su trámite de \"$tipo_tramite\" cambió de \"$estadoAnterior\" a \"$nuevoEstado\".";
            if (!empty($comentario)) {
                $mensaje .= " Nota: $comentario";
            }
    }

    // ===============================
    // INSERTAR NOTIFICACIÓN AL USUARIO
    // ===============================
    $stmtNotif = $conn->prepare("
        INSERT INTO notificaciones
        (id_usuario, id_tramite, titulo, mensaje, leida, fecha)
        VALUES (?, ?, ?, ?, 0, NOW())
    ");
    $stmtNotif->bind_param("iiss", $id_usuario_creador, $id, $titulo, $mensaje);
    $stmtNotif->execute();

    echo json_encode(["success" => true]);

} else {
    echo json_encode(["success" => false]);
}