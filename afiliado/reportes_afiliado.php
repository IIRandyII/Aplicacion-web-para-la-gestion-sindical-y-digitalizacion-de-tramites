<?php
require_once("../includes/auth_afiliado.php");
require_once("../config/db.php");

// Variables de sesión y página activa
$paginaActiva    = "reportes";
$nombreAfiliado  = $_SESSION['nombre'];
$id_departamento = $_SESSION['id_departamento'];
$id_usuario      = $_SESSION['id_usuario'];

// ===============================
// FILTRO POR FECHA
// Por defecto muestra el mes actual
// ===============================
$filtro = $_GET['filtro'] ?? 'mes';

switch ($filtro) {
    case 'dia':
        $condicionFecha = "AND DATE(fecha_creacion) = CURDATE()";
        break;
    case 'semana':
        $condicionFecha = "AND YEARWEEK(fecha_creacion, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'anio':
        $condicionFecha = "AND YEAR(fecha_creacion) = YEAR(CURDATE())";
        break;
    default: // mes
        $condicionFecha = "AND MONTH(fecha_creacion) = MONTH(CURDATE()) AND YEAR(fecha_creacion) = YEAR(CURDATE())";
}

// ===============================
// GRÁFICA 1: TRÁMITES POR ESTADO
// ===============================
$sqlEstado = "SELECT estado, COUNT(id_tramite) AS total
              FROM tramites
              WHERE id_departamento = ? $condicionFecha
              GROUP BY estado";
$stmtEstado = $conn->prepare($sqlEstado);
$stmtEstado->bind_param("i", $id_departamento);
$stmtEstado->execute();
$resEstado = $stmtEstado->get_result();

$estados       = [];
$totalesEstado = [];
while ($fila = $resEstado->fetch_assoc()) {
    $estados[]       = $fila['estado'];
    $totalesEstado[] = $fila['total'];
}

// ===============================
// GRÁFICA 2: TRÁMITES POR TIPO
// ===============================
$sqlTipo = "SELECT tipo_tramite, COUNT(id_tramite) AS total
            FROM tramites
            WHERE id_departamento = ? $condicionFecha
            GROUP BY tipo_tramite
            ORDER BY total DESC";
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

// ===============================
// GRÁFICA 3: TRÁMITES POR MES
// ===============================
$sqlMes = "SELECT MONTH(fecha_creacion) AS mes, COUNT(id_tramite) AS total
           FROM tramites
           WHERE id_departamento = ? AND YEAR(fecha_creacion) = YEAR(CURDATE())
           GROUP BY mes
           ORDER BY mes";
$stmtMes = $conn->prepare($sqlMes);
$stmtMes->bind_param("i", $id_departamento);
$stmtMes->execute();
$resMes = $stmtMes->get_result();

$nombresMeses = [1=>"Ene",2=>"Feb",3=>"Mar",4=>"Abr",5=>"May",6=>"Jun",7=>"Jul",8=>"Ago",9=>"Sep",10=>"Oct",11=>"Nov",12=>"Dic"];
$meses      = [];
$totalesMes = [];
while ($fila = $resMes->fetch_assoc()) {
    $meses[]      = $nombresMeses[$fila['mes']];
    $totalesMes[] = $fila['total'];
}

// ===============================
// GRÁFICA 4: TRÁMITES POR ESTADO Y MES
// Barras apiladas
// ===============================
$sqlApilada = "SELECT MONTH(fecha_creacion) AS mes, estado, COUNT(id_tramite) AS total
               FROM tramites
               WHERE id_departamento = ? AND YEAR(fecha_creacion) = YEAR(CURDATE())
               GROUP BY mes, estado
               ORDER BY mes";
$stmtApilada = $conn->prepare($sqlApilada);
$stmtApilada->bind_param("i", $id_departamento);
$stmtApilada->execute();
$resApilada = $stmtApilada->get_result();

$datosApilada = [];
while ($fila = $resApilada->fetch_assoc()) {
    $mes    = $nombresMeses[$fila['mes']];
    $estado = $fila['estado'];
    if (!isset($datosApilada[$estado])) {
        $datosApilada[$estado] = [];
    }
    $datosApilada[$estado][$mes] = $fila['total'];
}

// Meses únicos para el eje X
$mesesApilada = array_values($nombresMeses);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes | Sección 49</title>

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

    <section class="reportes-section">

        <!-- FILTROS -->
        <div class="filtros-reportes">
            <span class="filtros-titulo"><i class="fa-solid fa-filter"></i> Filtrar por:</span>
            <div class="filtros-btns">
                <a href="?filtro=dia"    class="btn-filtro <?= $filtro === 'dia'    ? 'activo' : '' ?>">Hoy</a>
                <a href="?filtro=semana" class="btn-filtro <?= $filtro === 'semana' ? 'activo' : '' ?>">Esta semana</a>
                <a href="?filtro=mes"    class="btn-filtro <?= $filtro === 'mes'    ? 'activo' : '' ?>">Este mes</a>
                <a href="?filtro=anio"   class="btn-filtro <?= $filtro === 'anio'   ? 'activo' : '' ?>">Este año</a>
            </div>
        </div>

        <!-- GRID DE GRÁFICAS -->
        <div class="graficas-grid">

            <!-- GRÁFICA 1: POR ESTADO -->
            <div class="grafica-box">
                <h4><i class="fa-solid fa-chart-pie"></i> Trámites por estado</h4>
                <div class="grafica-contenedor">
                    <canvas id="graficaEstado"></canvas>
                </div>
            </div>

            <!-- GRÁFICA 2: POR TIPO -->
            <div class="grafica-box">
                <h4><i class="fa-solid fa-bars"></i> Trámites por tipo</h4>
                <div class="grafica-contenedor">
                    <canvas id="graficaTipos"></canvas>
                </div>
            </div>

            <!-- GRÁFICA 3: POR MES -->
            <div class="grafica-box grafica-full">
                <h4><i class="fa-solid fa-chart-line"></i> Trámites por mes (año actual)</h4>
                <div class="grafica-contenedor">
                    <canvas id="graficaMeses"></canvas>
                </div>
            </div>

            <!-- GRÁFICA 4: BARRAS APILADAS -->
            <div class="grafica-box grafica-full">
                <h4><i class="fa-solid fa-chart-bar"></i> Trámites por estado y mes</h4>
                <div class="grafica-contenedor">
                    <canvas id="graficaApilada"></canvas>
                </div>
            </div>

        </div>

        <!-- BOTONES DE EXPORTACIÓN -->
        <div class="botones-reportes">
            <a href="exportar_pdf.php?filtro=<?= $filtro ?>" class="btn-exportar btn-pdf">
                <i class="fa-solid fa-file-pdf"></i> Exportar PDF
            </a>
            <a href="exportar_excel.php?filtro=<?= $filtro ?>" class="btn-exportar btn-excel">
                <i class="fa-solid fa-file-excel"></i> Exportar Excel
            </a>
        </div>

    </section>

</main>

<!-- CHART.JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Colores del sistema
const coloresEstado = {
    "Pendiente":   "#ffd000",
    "En revisión": "#2563eb",
    "Aprobado":    "#16a34a",
    "Rechazado":   "#dc2626"
};

// ===============================
// GRÁFICA 1: DONA - POR ESTADO
// ===============================
const estados       = <?= json_encode($estados) ?>;
const totalesEstado = <?= json_encode($totalesEstado) ?>;

new Chart(document.getElementById("graficaEstado"), {
    type: "doughnut",
    data: {
        labels: estados,
        datasets: [{
            data: totalesEstado,
            backgroundColor: estados.map(e => coloresEstado[e] ?? "#94a3b8"),
            borderWidth: 2,
            borderColor: "#fff"
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: "bottom",
                labels: { font: { size: 13 }, padding: 15 }
            }
        }
    }
});

// ===============================
// GRÁFICA 2: BARRAS HORIZONTALES - POR TIPO
// ===============================
const tipos       = <?= json_encode($tipos) ?>;
const totalesTipo = <?= json_encode($totalesTipo) ?>;

new Chart(document.getElementById("graficaTipos"), {
    type: "bar",
    data: {
        labels: tipos,
        datasets: [{
            label: "Cantidad",
            data: totalesTipo,
            backgroundColor: "rgba(0, 40, 85, 0.8)",
            borderRadius: 6,
            borderSkipped: false
        }]
    },
    options: {
        indexAxis: "y",
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true, ticks: { stepSize: 1 } },
            y: { ticks: { font: { size: 12 } } }
        }
    }
});

// ===============================
// GRÁFICA 3: LÍNEA CON ÁREA - POR MES
// ===============================
const meses      = <?= json_encode($meses) ?>;
const totalesMes = <?= json_encode($totalesMes) ?>;

new Chart(document.getElementById("graficaMeses"), {
    type: "line",
    data: {
        labels: meses,
        datasets: [{
            label: "Trámites",
            data: totalesMes,
            borderColor: "#2563eb",
            backgroundColor: "rgba(37, 99, 235, 0.15)",
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: "#002855",
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});

// ===============================
// GRÁFICA 4: BARRAS APILADAS
// ===============================
const mesesApilada  = <?= json_encode($mesesApilada) ?>;
const datosApilada  = <?= json_encode($datosApilada) ?>;
const estadosKeys   = Object.keys(datosApilada);

const datasetsApilada = estadosKeys.map(estado => ({
    label: estado,
    data: mesesApilada.map(mes => datosApilada[estado][mes] ?? 0),
    backgroundColor: coloresEstado[estado] ?? "#94a3b8",
    borderRadius: 4
}));

new Chart(document.getElementById("graficaApilada"), {
    type: "bar",
    data: {
        labels: mesesApilada,
        datasets: datasetsApilada
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: "bottom",
                labels: { font: { size: 12 }, padding: 12 }
            }
        },
        scales: {
            x: { stacked: true },
            y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});
</script>

<!-- SCRIPTS -->
<script src="../assets/js/afiliado/sidebar_afiliado.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>