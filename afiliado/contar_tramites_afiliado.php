<?php
require_once("../includes/auth_afiliado.php");
require_once("../config/db.php");

header("Content-Type: application/json");

$id_afiliado = $_SESSION['id_usuario'];

/* Obtener departamento del afiliado */
$stmt = $conn->prepare("SELECT id_departamento FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id_afiliado);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

$id_departamento = $usuario['id_departamento'];

/* Contadores por estado */
$sql = "SELECT 
            COUNT(*) as total,
            SUM(estado = 'Pendiente') as pendientes,
            SUM(estado = 'En revisión') as revision,
            SUM(estado = 'Aprobado') as aprobados,
            SUM(estado = 'Rechazado') as rechazados
        FROM tramites
        WHERE id_departamento = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_departamento);
$stmt->execute();
$result = $stmt->get_result();

echo json_encode($result->fetch_assoc());
exit;