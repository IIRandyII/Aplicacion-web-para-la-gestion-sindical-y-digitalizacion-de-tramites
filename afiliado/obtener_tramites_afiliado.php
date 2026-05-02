<?php
require_once("../includes/auth_afiliado.php");
require_once("../config/db.php");

header("Content-Type: application/json");

$id_afiliado = $_SESSION['id_usuario'];
$estado = $_GET['estado'] ?? 'Todos';
$buscar = $_GET['buscar'] ?? '';
$fecha = $_GET['fecha'] ?? 'Todos';

/* Obtener departamento del afiliado */
$stmt = $conn->prepare("SELECT id_departamento FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id_afiliado);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$id_departamento = $usuario['id_departamento'];

/* Construcción dinámica */
$sql = "SELECT id_tramite, nombre_completo, tipo_tramite, estado, fecha_creacion
        FROM tramites
        WHERE id_departamento = ?";

$params = [$id_departamento];
$types = "i";

/* Filtro por estado */
if ($estado !== "Todos") {
    $sql .= " AND estado = ?";
    $params[] = $estado;
    $types .= "s";
}

/* Filtro de búsqueda */
if (!empty($buscar)) {
    $sql .= " AND (
        nombre_completo LIKE ? OR
        tipo_tramite LIKE ? OR
        id_tramite LIKE ?
    )";
    $like = "%$buscar%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= "sss";
}

/* Filtro por fecha */
switch ($fecha) {
    case "Hoy":
        $sql .= " AND DATE(fecha_creacion) = CURDATE()";
        break;
    case "Semana":
        $sql .= " AND YEARWEEK(fecha_creacion, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case "Mes":
        $sql .= " AND MONTH(fecha_creacion) = MONTH(CURDATE())
                  AND YEAR(fecha_creacion) = YEAR(CURDATE())";
        break;
    case "Anio":
        $sql .= " AND YEAR(fecha_creacion) = YEAR(CURDATE())";
        break;
}

$sql .= " ORDER BY fecha_creacion DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

echo json_encode($result->fetch_all(MYSQLI_ASSOC));
exit;