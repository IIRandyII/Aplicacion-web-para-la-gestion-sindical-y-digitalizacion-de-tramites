<?php
require_once("../includes/auth_usuario.php");
require_once __DIR__ . "/../config/db.php";

header('Content-Type: application/json');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(["success" => false]);
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$id = intval($_GET['id']);

$sql = "SELECT 
            t.*, 
            d.nombre AS departamento
        FROM tramites t
        INNER JOIN departamentos d 
            ON t.id_departamento = d.id_departamento
        WHERE t.id_tramite = ? 
        AND t.id_usuario = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id, $id_usuario);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {

    $tramite = $result->fetch_assoc();

    echo json_encode([
        "success" => true,
        "tramite" => $tramite
    ]);

} else {
    echo json_encode(["success" => false]);
}

exit();