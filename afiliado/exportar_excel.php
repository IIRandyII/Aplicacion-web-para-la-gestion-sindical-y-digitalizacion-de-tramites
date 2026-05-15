<?php
require_once("../includes/auth_afiliado.php");
require_once("../config/db.php");

// ===============================
// HEADERS PARA DESCARGA DE EXCEL
// ===============================
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=reporte_tramites_" . date('d-m-Y') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

$id_departamento = $_SESSION['id_departamento'];
$filtro          = $_GET['filtro'] ?? 'mes';

switch ($filtro) {
    case 'dia':
        $condicionFecha = "AND DATE(fecha_creacion) = CURDATE()";
        $periodoTexto   = "Hoy " . date('d/m/Y');
        break;
    case 'semana':
        $condicionFecha = "AND YEARWEEK(fecha_creacion, 1) = YEARWEEK(CURDATE(), 1)";
        $periodoTexto   = "Esta semana";
        break;
    case 'anio':
        $condicionFecha = "AND YEAR(fecha_creacion) = YEAR(CURDATE())";
        $periodoTexto   = "Año " . date('Y');
        break;
    default:
        $condicionFecha = "AND MONTH(fecha_creacion) = MONTH(CURDATE()) AND YEAR(fecha_creacion) = YEAR(CURDATE())";
        $periodoTexto   = date('F Y');
}

// ===============================
// CONSULTA 1: TRÁMITES POR ESTADO
// ===============================
$sql = "SELECT estado, COUNT(id_tramite) AS total
        FROM tramites
        WHERE id_departamento = ? $condicionFecha
        GROUP BY estado";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_departamento);
$stmt->execute();
$porEstado = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// ===============================
// CONSULTA 2: TRÁMITES POR TIPO
// ===============================
$sql = "SELECT tipo_tramite, COUNT(id_tramite) AS total
        FROM tramites
        WHERE id_departamento = ? $condicionFecha
        GROUP BY tipo_tramite
        ORDER BY total DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_departamento);
$stmt->execute();
$porTipo = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// ===============================
// CONSULTA 3: TRÁMITES POR MES
// ===============================
$nombresMeses = [1=>"Enero",2=>"Febrero",3=>"Marzo",4=>"Abril",5=>"Mayo",6=>"Junio",7=>"Julio",8=>"Agosto",9=>"Septiembre",10=>"Octubre",11=>"Noviembre",12=>"Diciembre"];

$sql = "SELECT MONTH(fecha_creacion) AS mes, COUNT(id_tramite) AS total
        FROM tramites
        WHERE id_departamento = ? AND YEAR(fecha_creacion) = YEAR(CURDATE())
        GROUP BY mes ORDER BY mes";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_departamento);
$stmt->execute();
$porMes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// ===============================
// CONSULTA 4: TRÁMITES POR ESTADO Y MES
// ===============================
$sql = "SELECT MONTH(fecha_creacion) AS mes, estado, COUNT(id_tramite) AS total
        FROM tramites
        WHERE id_departamento = ? AND YEAR(fecha_creacion) = YEAR(CURDATE())
        GROUP BY mes, estado ORDER BY mes";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_departamento);
$stmt->execute();
$porEstadoMes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<html>
<head>
    <meta charset="UTF-8">
    <style>
        body  { font-family: Arial; font-size: 12px; }
        h2    { background-color: #002855; color: white; padding: 8px; }
        h3    { background-color: #2563eb; color: white; padding: 5px; margin-top: 20px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 10px; }
        th    { background-color: #002855; color: white; padding: 8px; text-align: center; border: 1px solid #ccc; }
        td    { padding: 6px 10px; border: 1px solid #ccc; text-align: center; }
        tr:nth-child(even) { background-color: #dbeafe; }
        .periodo { font-size: 11px; color: #666; margin-bottom: 15px; }
    </style>
</head>
<body>

<h2>Reporte de Trámites — Sección 49</h2>
<p class="periodo">Período: <?= $periodoTexto ?> | Generado: <?= date('d/m/Y H:i') ?></p>

<!-- HOJA 1: POR ESTADO -->
<h3>📊 Trámites por estado</h3>
<table>
    <tr>
        <th>Estado</th>
        <th>Total</th>
    </tr>
    <?php foreach ($porEstado as $fila): ?>
    <tr>
        <td><?= $fila['estado'] ?></td>
        <td><?= $fila['total'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- HOJA 2: POR TIPO -->
<h3>📋 Trámites por tipo</h3>
<table>
    <tr>
        <th>Tipo de trámite</th>
        <th>Total</th>
    </tr>
    <?php foreach ($porTipo as $fila): ?>
    <tr>
        <td><?= $fila['tipo_tramite'] ?></td>
        <td><?= $fila['total'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- HOJA 3: POR MES -->
<h3>📅 Trámites por mes (año actual)</h3>
<table>
    <tr>
        <th>Mes</th>
        <th>Total</th>
    </tr>
    <?php foreach ($porMes as $fila): ?>
    <tr>
        <td><?= $nombresMeses[$fila['mes']] ?></td>
        <td><?= $fila['total'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- HOJA 4: POR ESTADO Y MES -->
<h3>📈 Trámites por estado y mes</h3>
<table>
    <tr>
        <th>Mes</th>
        <th>Estado</th>
        <th>Total</th>
    </tr>
    <?php foreach ($porEstadoMes as $fila): ?>
    <tr>
        <td><?= $nombresMeses[$fila['mes']] ?></td>
        <td><?= $fila['estado'] ?></td>
        <td><?= $fila['total'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>