<?php
require_once("../includes/auth_usuario.php");
require_once __DIR__ . "/../config/db.php";

// Respuesta en formato JSON
header('Content-Type: application/json');

$id_usuario = $_SESSION['id_usuario'];

// ===============================
// OBTENER TRÁMITES DEL USUARIO
// Retorna todos los trámites con
// el nombre del departamento
// ordenados del más reciente al más antiguo
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
        ORDER BY t.id_tramite DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();

// Retornar todos los resultados como JSON
echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
exit;