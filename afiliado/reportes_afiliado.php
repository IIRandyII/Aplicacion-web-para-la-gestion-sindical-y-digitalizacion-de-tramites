<?php
require_once("../includes/auth_afiliado.php");
require_once("../config/db.php");

// Variables de sesión y página activa
$paginaActiva    = "reportes";
$nombreAfiliado  = $_SESSION['nombre'];
$id_departamento = $_SESSION['id_departamento'];

/* ===============================
   TRÁMITES POR TIPO
   Agrupa los trámites por tipo
   para la gráfica de barras
================================ */
$sqlTipo = "SELECT tipo_tramite, COUNT(id_tramite) AS total
            FROM tramites
            WHERE id_departamento = ?
            GROUP BY tipo_tramite";

$stmtTipo = $conn->prepare($sqlTipo);
$stmtTipo->bind_param("i", $id_departamento);
$stmtTipo->execute();
$resTipo = $stmtTipo->get_result();

$tipos       = [];
$totalesTipo = [];

while ($fila = $resTipo->fetch_assoc()) {
    $tipos[]       = $fila['tipo_tramite'];
    $totalesTipo[] = $fila['total'];
}

/* ===============================
   TRÁMITES POR MES
   Agrupa los trámites por mes
   para la gráfica de línea
================================ */
$sqlMes = "SELECT MONTH(fecha_creacion) AS mes, COUNT(id_tramite) AS total
           FROM tramites
           WHERE id_departamento = ?
           GROUP BY mes
           ORDER BY mes";

$stmtMes = $conn->prepare($sqlMes);
$stmtMes->bind_param("i", $id_departamento);
$stmtMes->execute();
$resMes = $stmtMes->get_result();

$meses      = [];
$totalesMes = [];

$nombresMeses = [
    1  => "Enero",    2  => "Febrero",   3  => "Marzo",
    4  => "Abril",    5  => "Mayo",      6  => "Junio",
    7  => "Julio",    8  => "Agosto",    9  => "Septiembre",
    10 => "Octubre",  11 => "Noviembre", 12 => "Diciembre"
];

while ($fila = $resMes->fetch_assoc()) {
    $meses[]      = $nombresMeses[$fila['mes']];
    $totalesMes[] = $fila['total'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes Afiliado | Sección 49</title>

    <!-- Estilos propios -->
    <link rel="stylesheet" href="../assets/css/afiliado/sidebar_afiliado.css">
    <link rel="stylesheet" href="../assets/css/afiliado/reporte_afiliado.css">

    <!-- Librerías externas -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<!-- SIDEBAR -->
<?php include "../includes/sidebar_afiliado.php"; ?>

<!-- CONTENIDO PRINCIPAL -->
<main class="main">

    <?php
    $tituloTopbar = "Reportes";
    include "../includes/topbar_afiliado.php";
    ?>

    <!-- SECCIÓN DE REPORTES -->
    <section class="avisos">

        <div class="row">

            <!-- GRÁFICA: TRÁMITES POR TIPO -->
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

            <!-- GRÁFICA: TRÁMITES POR MES -->
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

        <!-- BOTONES DE EXPORTACIÓN -->
        <div class="botones-reportes">
            <a href="exportar_pdf.php" class="btn btn-danger">Exportar a PDF</a>
            <a href="exportar_excel.php" class="btn btn-success">Exportar a Excel</a>
        </div>

    </section>

</main>

<!-- CHART.JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    /* ===============================
       GRÁFICA: TRÁMITES POR TIPO
       Gráfica de barras
    ================================ */
    const tipos       = <?php echo json_encode($tipos); ?>;
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
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });

    /* ===============================
       GRÁFICA: TRÁMITES POR MES
       Gráfica de línea
    ================================ */
    const meses      = <?php echo json_encode($meses); ?>;
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
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });
</script>

<!-- SCRIPTS -->
<script src="../assets/js/afiliado/sidebar_afiliado.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../assets/js/afiliado/reporte_afiliado.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>