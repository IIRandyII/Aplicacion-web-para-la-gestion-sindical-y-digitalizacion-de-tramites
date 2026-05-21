<?php
require_once("../includes/auth_afiliado.php");
require_once("../config/db.php");

$paginaActiva    = "archivados";
$id_usuario      = $_SESSION['id_usuario'];
$id_departamento = $_SESSION['id_departamento'];

$stmt = $conn->prepare("
    SELECT 
        t.id_tramite,
        t.nombre_completo,
        t.tipo_tramite,
        t.estado,
        t.fecha_creacion,
        d.nombre AS departamento
    FROM tramites t
    JOIN departamentos d ON t.id_departamento = d.id_departamento
    WHERE t.id_departamento = ? AND t.archivado = 1
    ORDER BY t.fecha_creacion DESC
");
$stmt->bind_param("i", $id_departamento);
$stmt->execute();
$archivados = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Archivados | Sección 49</title>
    <link rel="stylesheet" href="../assets/css/afiliado/sidebar_afiliado.css">
    <link rel="stylesheet" href="../assets/css/afiliado/archivados_afiliado.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php include "../includes/sidebar_afiliado.php"; ?>

<main class="main">

    <?php
    $tituloTopbar = "Archivados";
    include "../includes/topbar_afiliado.php";
    ?>

    <section class="archivados-section">

        <div class="archivados-contenedor">
            <h4><i class="fa-solid fa-box-archive"></i> Trámites archivados</h4>

            <?php if ($archivados->num_rows > 0): ?>

                <div class="filtros-archivados">
                    <div class="buscador">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="buscarArchivado" placeholder="Buscar por nombre, tipo o ID...">
                    </div>
                    <select id="filtroEstado">
                        <option value="Todos">Todos</option>
                        <option value="Aprobado">Aprobados</option>
                        <option value="Rechazado">Rechazados</option>
                    </select>
                </div>

                <div class="table-responsive">
                    <table class="tabla-archivados" id="tablaArchivados">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Solicitante</th>
                                <th>Trámite</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($t = $archivados->fetch_assoc()): ?>
                                <tr data-estado="<?= $t['estado'] ?>">
                                    <td>#<?= $t['id_tramite'] ?></td>
                                    <td><?= htmlspecialchars($t['nombre_completo']) ?></td>
                                    <td><?= htmlspecialchars($t['tipo_tramite']) ?></td>
                                    <td>
                                        <span class="badge-estado <?= strtolower($t['estado']) ?>">
                                            <?= $t['estado'] ?>
                                        </span>
                                    </td>
                                    <td><?= date("d/m/Y H:i", strtotime($t['fecha_creacion'])) ?></td>
                                    <td>
                                        <button class="btn-ver-arch" onclick="verTramite(<?= $t['id_tramite'] ?>)">
                                            <i class="fa-solid fa-eye"></i> Ver
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINACIÓN -->
                <div class="paginacion" id="paginacion"></div>

            <?php else: ?>

                <div class="archivados-vacio">
                    <i class="fa-solid fa-box-archive"></i>
                    <p>No hay trámites archivados aún.</p>
                </div>

            <?php endif; ?>

        </div>

    </section>

<!-- MODAL VER TRÁMITE -->
<div class="modal-afiliado" id="modalTramiteArchivado">
    <div class="modal-afiliado-content">
        <span class="modal-afiliado-cerrar" id="cerrarModalArchivado">&times;</span>
        <div id="contenidoModal">Cargando...</div>
    </div>
</div>

</main>

<script src="../assets/js/afiliado/sidebar_afiliado.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/afiliado/archivados_afiliado.js"></script>
</body>
</html>