<?php
require_once("../includes/auth_afiliado.php");
require_once("../config/db.php");

// Variables de sesión y página activa
$paginaActiva    = "historial";
$id_usuario      = $_SESSION['id_usuario'];
$id_departamento = $_SESSION['id_departamento'];

// ===============================
// OBTENER HISTORIAL DE CAMBIOS
// Del departamento del afiliado
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
");
$stmt->bind_param("i", $id_departamento);
$stmt->execute();
$historial = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial | Sección 49</title>

    <!-- Estilos propios -->
    <link rel="stylesheet" href="../assets/css/afiliado/sidebar_afiliado.css">
    <link rel="stylesheet" href="../assets/css/afiliado/historial_afiliado.css">

    <!-- Librerías externas -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<!-- SIDEBAR -->
<?php include "../includes/sidebar_afiliado.php"; ?>

<!-- CONTENIDO PRINCIPAL -->
<main class="main">

    <!-- TOPBAR -->
    <?php
    $tituloTopbar = "Historial";
    include "../includes/topbar_afiliado.php";
    ?>

    <section class="historial-section">

        <!-- TABLA DE HISTORIAL -->
        <div class="historial-contenedor">
            <h4><i class="fa-solid fa-clock-rotate-left"></i> Historial de cambios</h4>

            <?php if ($historial->num_rows > 0): ?>

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

            <?php else: ?>

                <div class="historial-vacio">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <p>No hay cambios registrados aún.</p>
                </div>

            <?php endif; ?>

        </div>

    </section>

</main>

<!-- SCRIPTS -->
<script src="../assets/js/afiliado/sidebar_afiliado.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>