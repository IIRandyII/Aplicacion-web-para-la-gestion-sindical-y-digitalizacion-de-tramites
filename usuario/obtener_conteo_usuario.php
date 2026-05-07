<?php
require_once("../includes/auth_usuario.php");
require_once("../config/db.php");

// Respuesta en formato JSON
header("Content-Type: application/json");

$id_usuario = $_SESSION['id_usuario'];

// ===============================
// CONTEO DE TRÁMITES POR ESTADO
// Retorna el total y el conteo
// por cada estado para las cards
// del dashboard
// ===============================
$sql = "SELECT 
            COUNT(*)                      AS total,
            SUM(estado = 'Pendiente')     AS pendientes,
            SUM(estado = 'En revisión')   AS revision,
            SUM(estado = 'Aprobado')      AS aprobados
        FROM tramites
        WHERE id_usuario = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();

// Retornar resultado como JSON
echo json_encode($stmt->get_result()->fetch_assoc());
exit;