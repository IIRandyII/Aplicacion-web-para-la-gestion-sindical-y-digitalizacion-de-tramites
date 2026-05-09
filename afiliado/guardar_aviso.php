<?php
require_once("../includes/auth_afiliado.php");
require_once("../config/db.php");

header("Content-Type: application/json");

// ===============================
// RECIBIR DATOS DEL FORMULARIO
// ===============================
$data            = json_decode(file_get_contents("php://input"), true);
$id              = intval($data['id'] ?? 0);
$titulo          = trim($data['titulo']);
$mensaje         = trim($data['mensaje']);
$id_departamento = $_SESSION['id_departamento'];

// ===============================
// CREAR O EDITAR AVISO
// Si hay ID es edición, si no es creación
// ===============================
if ($id > 0) {

    // Actualizar aviso existente
    $stmt = $conn->prepare("
        UPDATE avisos SET titulo = ?, mensaje = ?
        WHERE id_aviso = ? AND id_departamento = ?
    ");
    $stmt->bind_param("ssii", $titulo, $mensaje, $id, $id_departamento);

} else {

    // Insertar nuevo aviso
    $stmt = $conn->prepare("
        INSERT INTO avisos (id_departamento, titulo, mensaje)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("iss", $id_departamento, $titulo, $mensaje);
}

echo json_encode(["success" => $stmt->execute()]);