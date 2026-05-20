<?php
require_once("../includes/auth_afiliado.php");
require_once("../config/db.php");

$paginaActiva    = "historial";
$id_usuario      = $_SESSION['id_usuario'];
$id_departamento = $_SESSION['id_departamento'];

// ===============================
// PAGINACIÓN
// ===============================
$registros_por_pagina = 5;
$pagina_actual = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_actual < 1) $pagina_actual = 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Total de registros
$stmt_total = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM historial_tramites h
    JOIN tramites t ON h.id_tramite = t.id_tramite
    WHERE t.id_departamento = ?
");
$stmt_total->bind_param("i", $id_departamento);
$stmt_total->execute();
$total_registros = $stmt_total->get_result()->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);
if ($pagina_actual > $total_paginas && $total_paginas > 0) $pagina_actual = $total_paginas;

// ===============================
// OBTENER HISTORIAL DE CAMBIOS (con LIMIT y OFFSET)
// ===============================
$stmt = $conn->prepare("
    SELECT 
        h.id_historial,
        h.estado_anterior,
        h.estado_nuevo,
        h.fecha_cambio,
        t.id_tramite,
        t.tipo_tramite,
        t.nombre_completo AS solicitante,
        u.nombre AS nombre_afiliado
    FROM historial_tramites h
    JOIN tramites t ON h.id_tramite = t.id_tramite
    JOIN usuarios u ON h.id_afiliado = u.id_usuario
    WHERE t.id_departamento = ?
    ORDER BY h.fecha_cambio DESC
    LIMIT ? OFFSET ?
");
$stmt->bind_param("iii", $id_departamento, $registros_por_pagina, $offset);
$stmt->execute();
$historial = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial | Sección 49</title>
    <link rel="stylesheet" href="../assets/css/afiliado/sidebar_afiliado.css">
    <link rel="stylesheet" href="../assets/css/afiliado/historial_afiliado.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php include "../includes/sidebar_afiliado.php"; ?>

<main class="main">

    <?php
    $tituloTopbar = "Historial";
    include "../includes/topbar_afiliado.php";
    ?>

    <section class="historial-section">

        <div class="historial-contenedor">
            <h4><i class="fa-solid fa-clock-rotate-left"></i> Historial de cambios</h4>

            <?php if ($total_registros > 0): ?>

                <div class="table-responsive">
                    <table class="tabla-historial">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>ID</th>
                                <th>Solicitante</th>
                                <th>Trámite</th>
                                <th>Cambio</th>
                                <th>Realizado por</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $historial->fetch_assoc()): ?>
                                <tr>
                                    <td><?= date("d/m/Y H:i", strtotime($row['fecha_cambio'])) ?></td>
                                    <td>#<?= $row['id_tramite'] ?></td>
                                    <td><?= htmlspecialchars($row['solicitante']) ?></td>
                                    <td><?= htmlspecialchars($row['tipo_tramite']) ?></td>
                                    <td>
                                        <div class="cambio-estado">
                                            <span class="badge-estado <?= strtolower(str_replace(' ', '-', $row['estado_anterior'])) ?>">
                                                <?= $row['estado_anterior'] ?>
                                            </span>
                                            <i class="fa-solid fa-arrow-right"></i>
                                            <span class="badge-estado <?= strtolower(str_replace(' ', '-', $row['estado_nuevo'])) ?>">
                                                <?= $row['estado_nuevo'] ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($row['nombre_afiliado']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINACIÓN -->
                <?php if ($total_paginas > 1): ?>
                <div class="paginacion">
                    <!-- Botón Anterior -->
                    <?php if ($pagina_actual > 1): ?>
                        <a href="?pagina=<?= $pagina_actual - 1 ?>" class="pag-btn">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    <?php else: ?>
                        <span class="pag-btn disabled">
                            <i class="fa-solid fa-chevron-left"></i>
                        </span>
                    <?php endif; ?>

                    <!-- Números de página -->
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <a href="?pagina=<?= $i ?>" class="pag-btn <?= $i === $pagina_actual ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <!-- Botón Siguiente -->
                    <?php if ($pagina_actual < $total_paginas): ?>
                        <a href="?pagina=<?= $pagina_actual + 1 ?>" class="pag-btn">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    <?php else: ?>
                        <span class="pag-btn disabled">
                            <i class="fa-solid fa-chevron-right"></i>
                        </span>
                    <?php endif; ?>

                    <!-- Info de registros -->
                    <span class="pag-info">
                        Página <?= $pagina_actual ?> de <?= $total_paginas ?>
                        &nbsp;·&nbsp; <?= $total_registros ?> registros en total
                    </span>
                </div>
                <?php endif; ?>

            <?php else: ?>

                <div class="historial-vacio">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <p>No hay cambios registrados aún.</p>
                </div>

            <?php endif; ?>

        </div>

    </section>

</main>

<script src="../assets/js/afiliado/sidebar_afiliado.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>