<?php
require_once("../includes/auth_afiliado.php");
require_once("../config/db.php");

// Variables de sesión y página activa
$paginaActiva    = "archivados";
$id_usuario      = $_SESSION['id_usuario'];
$id_departamento = $_SESSION['id_departamento'];

// ===============================
// OBTENER TRÁMITES ARCHIVADOS
// Solo los aprobados y rechazados
// del departamento del afiliado
// ===============================
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

    <!-- Estilos propios -->
    <link rel="stylesheet" href="../assets/css/afiliado/sidebar_afiliado.css">
    <link rel="stylesheet" href="../assets/css/afiliado/archivados_afiliado.css">

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
    $tituloTopbar = "Archivados";
    include "../includes/topbar_afiliado.php";
    ?>

    <section class="archivados-section">

        <div class="archivados-contenedor">
            <h4><i class="fa-solid fa-box-archive"></i> Trámites archivados</h4>

            <?php if ($archivados->num_rows > 0): ?>

                <!-- FILTROS -->
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
                                        <button class="btn-ver-arch"
                                            onclick="verTramite(<?= $t['id_tramite'] ?>)">
                                            <i class="fa-solid fa-eye"></i> Ver
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

            <?php else: ?>

                <div class="archivados-vacio">
                    <i class="fa-solid fa-box-archive"></i>
                    <p>No hay trámites archivados aún.</p>
                </div>

            <?php endif; ?>

        </div>

    </section>

    <!-- MODAL VER TRÁMITE -->
    <div class="modal fade" id="modalTramite" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles del trámite</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="contenidoModal">
                    Cargando...
                </div>
            </div>
        </div>
    </div>

</main>

<!-- SCRIPTS -->
<script src="../assets/js/afiliado/sidebar_afiliado.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>

// ===============================
// VER DETALLE DEL TRÁMITE
// ===============================
function verTramite(id) {
    fetch("ver_tramite_afiliado.php?id=" + id)
        .then(res => res.text())
        .then(html => {
            document.getElementById("contenidoModal").innerHTML = html;
            const modal = new bootstrap.Modal(document.getElementById("modalTramite"));
            modal.show();
        });
}

// ===============================
// FILTROS DE BÚSQUEDA Y ESTADO
// ===============================
const inputBuscar  = document.getElementById("buscarArchivado");
const selectEstado = document.getElementById("filtroEstado");

function aplicarFiltros() {
    const texto  = inputBuscar.value.toLowerCase();
    const estado = selectEstado.value;
    const filas  = document.querySelectorAll("#tablaArchivados tbody tr");

    filas.forEach(fila => {
        const contenido    = fila.textContent.toLowerCase();
        const estadoFila   = fila.dataset.estado;
        const coincideTexto  = contenido.includes(texto);
        const coincideEstado = estado === "Todos" || estadoFila === estado;

        fila.style.display = coincideTexto && coincideEstado ? "" : "none";
    });
}

inputBuscar.addEventListener("input",  aplicarFiltros);
selectEstado.addEventListener("change", aplicarFiltros);

</script>

</body>
</html>