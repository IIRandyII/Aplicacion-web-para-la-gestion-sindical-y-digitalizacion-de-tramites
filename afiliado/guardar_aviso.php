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

// Validar y sanitizar tipo
$tiposPermitidos = ['general', 'urgente', 'informativo'];
$tipo            = in_array($data['tipo'] ?? '', $tiposPermitidos) ? $data['tipo'] : 'general';

// ===============================
// CREAR O EDITAR AVISO
// ===============================
if ($id > 0) {

    // Actualizar aviso existente
    $stmt = $conn->prepare("
        UPDATE avisos SET titulo = ?, mensaje = ?, tipo = ?
        WHERE id_aviso = ? AND id_departamento = ?
    ");
    $stmt->bind_param("sssii", $titulo, $mensaje, $tipo, $id, $id_departamento);

} else {

    // Insertar nuevo aviso
    $stmt = $conn->prepare("
        INSERT INTO avisos (id_departamento, titulo, mensaje, tipo)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("isss", $id_departamento, $titulo, $mensaje, $tipo);
}

echo json_encode(["success" => $stmt->execute()]);