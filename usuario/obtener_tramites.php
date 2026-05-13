<?php
require_once("../includes/auth_usuario.php");
require_once __DIR__ . "/../config/db.php";

// Respuesta en formato JSON
header('Content-Type: application/json');

$id_usuario = $_SESSION['id_usuario'];

// ===============================
// OBTENER TRÁMITES DEL USUARIO
// Excluye los trámites rechazados
// ya que se archivan automáticamente
// ===============================
$sql = "SELECT 
            t.id_tramite,
            d.nombre        AS departamento,
            t.tipo_tramite,
            t.nombre_completo,
            t.numero_ficha,
            t.categoria,
            t.turno,
            t.estado,
            t.fecha_creacion
        FROM tramites t
        JOIN departamentos d
            ON t.id_departamento = d.id_departamento
        WHERE t.id_usuario = ?
        AND t.estado != 'Rechazado'
        ORDER BY t.id_tramite DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();

echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
exit;