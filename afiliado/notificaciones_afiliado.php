<?php
require_once("../includes/auth_afiliado.php");
require_once("../config/db.php");

// Variables de sesión y página activa
$paginaActiva    = "notificaciones";
$id_usuario      = $_SESSION['id_usuario'];
$id_departamento = $_SESSION['id_departamento'];

// ===============================
// OBTENER NOTIFICACIONES DEL AFILIADO
// Ordenadas de más reciente a más antigua
// ===============================
$stmt = $conn->prepare("
    SELECT n.id_notificacion, n.titulo, n.mensaje, n.leida, n.fecha,
           n.id_tramite, t.tipo_tramite, t.nombre_completo
    FROM notificaciones_afiliado n
    JOIN tramites t ON n.id_tramite = t.id_tramite
    WHERE n.id_afiliado = ?
    ORDER BY n.fecha DESC
");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$notificaciones = $stmt->get_result();

// ===============================
// MARCAR TODAS COMO LEÍDAS
// Al entrar a la página se marcan
// todas las notificaciones como leídas
// ===============================
$stmtMarcar = $conn->prepare("
    UPDATE notificaciones_afiliado
    SET leida = 1
    WHERE id_afiliado = ? AND leida = 0
");
$stmtMarcar->bind_param("i", $id_usuario);
$stmtMarcar->execute();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Notificaciones | Sección 49</title>

    <!-- Estilos propios -->
    <link rel="stylesheet" href="../assets/css/afiliado/sidebar_afiliado.css">
    <link rel="stylesheet" href="../assets/css/afiliado/notificaciones_afiliado.css">

    <!-- Librerías externas -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<!-- SIDEBAR -->
<?php include "../includes/sidebar_afiliado.php"; ?>

<!-- CONTENIDO PRINCIPAL -->
<main class="main">

    <!-- TOPBAR -->
    <?php
    $tituloTopbar = "Notificaciones";
    include "../includes/topbar_afiliado.php";
    ?>

    <section class="notif-section">

        <?php if ($notificaciones->num_rows > 0): ?>

            <div class="notif-lista">
                <?php while ($notif = $notificaciones->fetch_assoc()): ?>

                    <div class="notif-card <?= $notif['leida'] ? '' : 'no-leida' ?>">

                        <div class="notif-card-header">
                            <div class="notif-info">
                                <span class="notif-icono">
                                    <i class="fa-solid fa-file-circle-plus"></i>
                                </span>
                                <div>
                                    <h4><?= htmlspecialchars($notif['titulo']) ?></h4>
                                    <span class="notif-fecha">
                                        <i class="fa-solid fa-clock"></i>
                                        <?= date("d/m/Y H:i", strtotime($notif['fecha'])) ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Botón ver trámite -->
                            <button class="btn-ver-tramite"
                                onclick="verTramite(<?= $notif['id_tramite'] ?>)">
                                <i class="fa-solid fa-eye"></i> Ver trámite
                            </button>
                        </div>

                        <p class="notif-mensaje"><?= htmlspecialchars($notif['mensaje']) ?></p>

                    </div>

                <?php endwhile; ?>
            </div>

        <?php else: ?>

            <div class="notif-vacio">
                <i class="fa-solid fa-bell-slash"></i>
                <p>No tienes notificaciones por el momento.</p>
            </div>

        <?php endif; ?>

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
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../assets/js/afiliado/sidebar_afiliado.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Abrir modal con detalle del trámite
function verTramite(id) {
    fetch("ver_tramite_afiliado.php?id=" + id)
        .then(res => res.text())
        .then(html => {
            document.getElementById("contenidoModal").innerHTML = html;
            const modal = new bootstrap.Modal(document.getElementById("modalTramite"));
            modal.show();
        })
        .catch(() => {
            document.getElementById("contenidoModal").innerHTML = `
                <div class="alert alert-danger">Error de conexión.</div>
            `;
            const modal = new bootstrap.Modal(document.getElementById("modalTramite"));
            modal.show();
        });
}
</script>

</body>
</html>