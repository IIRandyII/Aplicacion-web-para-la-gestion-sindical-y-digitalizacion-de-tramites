<?php
require_once("../includes/auth_usuario.php");
require_once("../config/db.php");

header("Content-Type: application/json");

$id_usuario = $_SESSION['id_usuario'];

$sql = "SELECT 
            COUNT(*) as total,
            SUM(estado = 'Pendiente') as pendientes,
            SUM(estado = 'En revisión') as revision,
            SUM(estado = 'Aprobado') as aprobados
        FROM tramites
        WHERE id_usuario = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();

$result = $stmt->get_result();

echo json_encode($result->fetch_assoc());
exit;