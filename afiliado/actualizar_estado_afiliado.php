<?php 
require_once("../includes/auth_afiliado.php");
require_once("../config/db.php");

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$id = intval($data['id']);
$nuevoEstado = $data['estado'];

$id_afiliado = $_SESSION['id_usuario'];

/* 1️⃣ Obtener departamento del afiliado */
$stmt = $conn->prepare("SELECT id_departamento FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id_afiliado);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$id_departamento = $usuario['id_departamento'];

/* 2️⃣ Obtener datos del trámite */
$stmt = $conn->prepare("
    SELECT estado, id_usuario, tipo_tramite
    FROM tramites 
    WHERE id_tramite = ? AND id_departamento = ?
");
$stmt->bind_param("ii", $id, $id_departamento);
$stmt->execute();
$result = $stmt->get_result();
$tramite = $result->fetch_assoc();

if (!$tramite) {
    echo json_encode(["success" => false]);
    exit;
}

$estadoAnterior = $tramite['estado'];
$id_usuario_creador = $tramite['id_usuario'];
$tipo_tramite = $tramite['tipo_tramite'];

/* 3️⃣ Verificar que realmente cambió el estado */
if ($estadoAnterior === $nuevoEstado) {
    echo json_encode(["success" => true]);
    exit;
}

/* 4️⃣ Actualizar estado */
$stmt = $conn->prepare("
    UPDATE tramites 
    SET estado = ? 
    WHERE id_tramite = ? AND id_departamento = ?
");
$stmt->bind_param("sii", $nuevoEstado, $id, $id_departamento);

if ($stmt->execute() && $stmt->affected_rows > 0) {

    /* 5️⃣ Crear mensaje profesional */
    switch ($nuevoEstado) {

        case "En revisión":
            $titulo = "Su trámite se encuentra en revisión";
            $mensaje = "Le informamos que su trámite de \"$tipo_tramite\" ha pasado a estado 'En revisión'. Nuestro equipo se encuentra analizándolo.";
            break;

        case "Aprobado":
            $titulo = "Trámite aprobado";
            $mensaje = "Nos complace informarle que su trámite de \"$tipo_tramite\" ha sido aprobado exitosamente.";
            break;

        case "Rechazado":
            $titulo = "Trámite rechazado";
            $mensaje = "Le informamos que su trámite de \"$tipo_tramite\" ha sido rechazado. Puede revisar los detalles o comunicarse con el departamento correspondiente.";
            break;

        default:
            $titulo = "Actualización de trámite";
            $mensaje = "El estado de su trámite de \"$tipo_tramite\" cambió de \"$estadoAnterior\" a \"$nuevoEstado\".";
    }

    /* 6️⃣ Insertar notificación */
    $stmtNotif = $conn->prepare("
    INSERT INTO notificaciones 
    (id_usuario, id_tramite, titulo, mensaje, leida, fecha) 
    VALUES (?, ?, ?, ?, 0, NOW())
    ");

    $stmtNotif->bind_param(
        "iiss",
        $id_usuario_creador,
        $id,
        $titulo,
        $mensaje,
    );

    $stmtNotif->execute();

    echo json_encode(["success" => true]);

} else {
    echo json_encode(["success" => false]);
}