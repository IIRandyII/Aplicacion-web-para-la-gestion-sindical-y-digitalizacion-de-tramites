<?php
require_once("../includes/auth_afiliado.php");
require_once("../config/db.php");

$id_departamento = $_SESSION['id_departamento'];

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=reporte_tramites.xls");
header("Pragma: no-cache");
header("Expires: 0");

$query = "SELECT id_tramite, tipo_tramite, estado, fecha_creacion 
FROM tramites 
WHERE id_departamento = '$id_departamento'";

$resultado = mysqli_query($conn, $query);
?>

<table border="1">
<tr>
    <th>ID Tramite</th>
    <th>Tipo Tramite</th>
    <th>Estado</th>
    <th>Fecha</th>
</tr>

<?php
while($fila = mysqli_fetch_assoc($resultado)){
?>

<tr>
    <td><?php echo $fila['id_tramite']; ?></td>
    <td><?php echo $fila['tipo_tramite']; ?></td>
    <td><?php echo $fila['estado']; ?></td>
    <td><?php echo $fila['fecha_creacion']; ?></td>
</tr>

<?php
}
?>

</table>