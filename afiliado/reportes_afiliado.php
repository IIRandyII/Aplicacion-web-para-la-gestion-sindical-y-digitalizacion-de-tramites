<?php
require_once("../includes/auth_afiliado.php");
require_once("../config/db.php");

$nombreAfiliado = $_SESSION['nombre'];
$id_departamento = $_SESSION['id_departamento'];

/* =========================
   TRÁMITES POR TIPO
   ========================= */

$sqlTipo = "SELECT tipo_tramite, COUNT(id_tramite) AS total
            FROM tramites
            WHERE id_departamento = '$id_departamento'
            GROUP BY tipo_tramite";

$resTipo = $conn->query($sqlTipo);

$tipos = [];
$totalesTipo = [];

while($fila = $resTipo->fetch_assoc()){
    $tipos[] = $fila['tipo_tramite'];
    $totalesTipo[] = $fila['total'];
}


/* =========================
   TRÁMITES POR MES
   ========================= */

$sqlMes = "SELECT MONTH(fecha_creacion) AS mes, COUNT(id_tramite) AS total
           FROM tramites
           WHERE id_departamento = '$id_departamento'
           GROUP BY mes
           ORDER BY mes";

$resMes = $conn->query($sqlMes);

$meses = [];
$totalesMes = [];

$nombresMeses = [
1=>"Enero",2=>"Febrero",3=>"Marzo",4=>"Abril",
5=>"Mayo",6=>"Junio",7=>"Julio",8=>"Agosto",
9=>"Septiembre",10=>"Octubre",11=>"Noviembre",12=>"Diciembre"
];

while($fila = $resMes->fetch_assoc()){
    $meses[] = $nombresMeses[$fila['mes']];
    $totalesMes[] = $fila['total'];
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reportes Afiliado | Sección 49</title>

<link rel="stylesheet" href="../assets/css/afiliado/reporte_afiliado.css">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">

<div class="logo">
<img src="../assets/img/logo.jpg" alt="Logo">
<span>Sección 49</span>
</div>

<nav class="menu">

<a href="dashboard_afiliado.php">
<i class="fa-solid fa-house"></i>
<span>Inicio</span>
</a>

<a href="reportes_afiliado.php" class="active">
<i class="fa-solid fa-file-lines"></i>
<span>Reportes</span>
</a>

<a href="../sesion/logout.php" class="logout">
<i class="fa-solid fa-right-from-bracket"></i>
<span>Cerrar sesión</span>
</a>

</nav>

</aside>


<!-- CONTENIDO -->
<main class="main">

<!-- TOPBAR -->
<div class="topbar">

<button class="toggle-btn" id="toggleSidebar">
<i class="fa-solid fa-bars"></i>
</button>

<h2>Reportes del Departamento - <?php echo htmlspecialchars($nombreAfiliado); ?></h2>

</div>

<!-- SECCIÓN REPORTES -->
<section class="avisos">

<div class="row">

<!-- GRAFICA TIPOS -->
<div class="col-md-6">

<div class="grafica-box">

<h4>
<i class="fa-solid fa-layer-group"></i>
Trámites por tipo
</h4>

<div style="height:300px;">
<canvas id="graficaTipos"></canvas>
</div>

</div>

</div>


<!-- GRAFICA MESES -->
<div class="col-md-6">

<div class="grafica-box">

<h4>
<i class="fa-solid fa-calendar"></i>
Trámites por mes
</h4>

<div style="height:300px;">
<canvas id="graficaMeses"></canvas>
</div>

</div>

</div>

</div>


<!-- BOTONES EXPORTAR -->

<div class="botones-reportes">
    <a href="exportar_pdf.php" class="btn btn-danger">Exportar a PDF</a>
    <a href="exportar_excel.php" class="btn btn-success">Exportar a Excel</a>
</div>

</section>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

/* =====================
   GRAFICA TIPOS
   ===================== */

const tipos = <?php echo json_encode($tipos); ?>;
const totalesTipo = <?php echo json_encode($totalesTipo); ?>;

new Chart(document.getElementById("graficaTipos"), {

type: "bar",

data: {
labels: tipos,
datasets: [{
label: "Cantidad de trámites",
data: totalesTipo
}]
},

options:{
responsive:true,
maintainAspectRatio:false,
scales:{
y:{
beginAtZero:true
}
}
}

});


/* =====================
   GRAFICA MESES
   ===================== */

const meses = <?php echo json_encode($meses); ?>;
const totalesMes = <?php echo json_encode($totalesMes); ?>;

new Chart(document.getElementById("graficaMeses"), {

type: "line",

data: {
labels: meses,
datasets: [{
label: "Trámites por mes",
data: totalesMes
}]
},

options:{
responsive:true,
maintainAspectRatio:false,
scales:{
y:{
beginAtZero:true
}
}
}

});

</script>

<script src="../assets/js/afiliado/reporte_afiliado.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>