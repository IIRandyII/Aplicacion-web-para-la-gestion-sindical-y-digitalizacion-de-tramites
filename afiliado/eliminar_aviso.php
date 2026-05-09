<?php
require_once("../includes/auth_afiliado.php");
require_once("../config/db.php");

header("Content-Type: application/json");

// ===============================
// RECIBIR Y VALIDAR ID
// ===============================
$data            = json_decode(file_get_contents("php://input"), true);
$id              = intval($data['id']);
$id_departamento = $_SESSION['id_departamento'];

// ===============================
// ELIMINAR AVISO
// Solo puede eliminar avisos de
// su propio departamento
// ===============================
$stmt = $conn->prepare("
    DELETE FROM avisos
    WHERE id_aviso = ? AND id_departamento = ?
");
$stmt->bind_param("ii", $id, $id_departamento);

echo json_encode(["success" => $stmt->execute()]);