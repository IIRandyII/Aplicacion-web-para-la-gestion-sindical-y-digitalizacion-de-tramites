<?php
require_once("../includes/auth_afiliado.php");
require_once("../config/db.php");

// Respuesta en formato JSON
header("Content-Type: application/json");

// ===============================
// RECIBIR Y VALIDAR DATOS
// Se recibe el ID y nuevo estado
// desde el body en formato JSON
// ===============================
$data        = json_decode(file_get_contents("php://input"), true);
$id          = intval($data['id']);
$nuevoEstado = $data['estado'];
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
// Se valida que el trámite
// pertenezca al departamento
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
// Si es el mismo no se hace nada
// ===============================
if ($estadoAnterior === $nuevoEstado) {
    echo json_encode(["success" => true]);
    exit;
}

// ===============================
// ACTUALIZAR ESTADO DEL TRÁMITE
// ===============================
$stmt = $conn->prepare("
    UPDATE tramites
    SET estado = ?
    WHERE id_tramite = ? AND id_departamento = ?
");
$stmt->bind_param("sii", $nuevoEstado, $id, $id_departamento);

if ($stmt->execute() && $stmt->affected_rows > 0) {

    // ===============================
    // REGISTRAR EN HISTORIAL
    // Se guarda el cambio de estado
    // con el afiliado que lo realizó
    // ===============================
    $stmtHistorial = $conn->prepare("
        INSERT INTO historial_tramites
        (id_tramite, id_afiliado, estado_anterior, estado_nuevo, fecha_cambio)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmtHistorial->bind_param("iiss", $id, $id_afiliado, $estadoAnterior, $nuevoEstado);
    $stmtHistorial->execute();

    // ===============================
    // GENERAR MENSAJE DE NOTIFICACIÓN
    // El mensaje varía según el
    // nuevo estado del trámite
    // ===============================
    switch ($nuevoEstado) {

        case "En revisión":
            $titulo  = "Su trámite se encuentra en revisión";
            $mensaje = "Le informamos que su trámite de \"$tipo_tramite\" ha pasado a estado 'En revisión'. Nuestro equipo se encuentra analizándolo.";
            break;

        case "Aprobado":
            $titulo  = "Trámite aprobado";
            $mensaje = "Nos complace informarle que su trámite de \"$tipo_tramite\" ha sido aprobado exitosamente.";
            break;

        case "Rechazado":
            $titulo  = "Trámite rechazado";
            $mensaje = "Le informamos que su trámite de \"$tipo_tramite\" ha sido rechazado. Puede revisar los detalles o comunicarse con el departamento correspondiente.";
            break;

        default:
            $titulo  = "Actualización de trámite";
            $mensaje = "El estado de su trámite de \"$tipo_tramite\" cambió de \"$estadoAnterior\" a \"$nuevoEstado\".";
    }

    // ===============================
    // INSERTAR NOTIFICACIÓN AL USUARIO
    // Se notifica al usuario creador
    // del trámite sobre el cambio
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