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
    const estados       = <?= json_encode($estados) ?>;
    const totalesEstado = <?= json_encode($totalesEstado) ?>;
    const tipos         = <?= json_encode($tipos) ?>;
    const totalesTipo   = <?= json_encode($totalesTipo) ?>;
</script>

<!-- SCRIPTS -->
<script src="../assets/js/afiliado/sidebar_afiliado.js"></script>
<script src="../assets/js/afiliado/reporte_afiliado.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>