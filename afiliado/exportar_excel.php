<?php
require_once("../includes/auth_afiliado.php");
require_once("../config/db.php");

// ===============================
// HEADERS PARA DESCARGA DE EXCEL
// ===============================
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=reporte_tramites.xls");
header("Pragma: no-cache");
header("Expires: 0");

// ===============================
// OBTENER TRÁMITES DEL DEPARTAMENTO
// Se filtran por el departamento
// del afiliado en sesión
// ===============================
$id_departamento = $_SESSION['id_departamento'];

$sql  = "SELECT id_tramite, tipo_tramite, estado, fecha_creacion
         FROM tramites
         WHERE id_departamento = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_departamento);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!-- ===============================
     TABLA HTML QUE SE EXPORTA A EXCEL
================================ -->
<table border="1">
    <tr>
        <th>ID Tramite</th>
        <th>Tipo Tramite</th>
        <th>Estado</th>
        <th>Fecha</th>
    </tr>

    <?php while ($fila = $resultado->fetch_assoc()): ?>
        <tr>
            <td><?= $fila['id_tramite'] ?></td>
            <td><?= $fila['tipo_tramite'] ?></td>
            <td><?= $fila['estado'] ?></td>
            <td><?= $fila['fecha_creacion'] ?></td>
        </tr>
    <?php endwhile; ?>

</table>