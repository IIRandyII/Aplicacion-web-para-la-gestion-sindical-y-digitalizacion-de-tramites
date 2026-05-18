<?php
require_once("../includes/auth_usuario.php");
require_once("../config/db.php");

// Variables de sesión y página activa
$paginaActiva  = "inicio";
$nombreUsuario = $_SESSION['nombre'];
$id_usuario    = $_SESSION['id_usuario'];

// ===============================
// OBTENER AVISOS DE TODOS LOS
// DEPARTAMENTOS ordenados por fecha
// ===============================
$stmtAvisos = $conn->prepare("
    SELECT a.titulo, a.mensaje, a.fecha_creacion, d.nombre AS departamento
    FROM avisos a
    JOIN departamentos d ON a.id_departamento = d.id_departamento
    WHERE a.fecha_creacion >= NOW() - INTERVAL 5 DAY
    ORDER BY a.fecha_creacion DESC
    LIMIT 6
");
$stmtAvisos->execute();
$avisos = $stmtAvisos->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Usuario | Sección 49</title>
    <link rel="stylesheet" href="../assets/css/sidebar_usuario.css">
    <link rel="stylesheet" href="../assets/css/topbar_usuario.css">
    <link rel="stylesheet" href="../assets/css/dashboard_usuario.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<!-- SIDEBAR -->
<?php include "../includes/sidebar_usuario.php"; ?>

<!-- CONTENIDO -->
<main class="main">

    <!-- TOPBAR -->
    <?php
        $tituloTopbar = "Bienvenido, " . htmlspecialchars($nombreUsuario);
        include "../includes/topbar_usuario.php";
    ?>
    <br>

    <!-- CARDS -->
    <div class="cards-container">
        <div class="info-card tramites">
            <i class="icon fa-solid fa-folder-open"></i>
            <h3>Mis trámites</h3>
            <p id="total">0</p>
        </div>
        <div class="info-card pendientes">
            <i class="fas fa-clock icon"></i>
            <h3>Pendientes</h3>
            <p id="pendientes">0</p>
        </div>
        <div class="info-card revision">
            <i class="fas fa-file-alt icon"></i>
            <h3>En revisión</h3>
            <p id="revision">0</p>
        </div>
        <div class="info-card aprobados">
            <i class="icon fa-solid fa-check"></i>
            <h3>Aprobados</h3>
            <p id="aprobados">0</p>
        </div>
    </div>

    <br><br>

    <!-- AVISOS IMPORTANTES -->
<!-- AVISOS IMPORTANTES -->
<section class="avisos">
    <h3><i class="fa-solid fa-bullhorn"></i> Avisos importantes</h3>

    <?php if ($avisos->num_rows > 0):
        $listaAvisos = $avisos->fetch_all(MYSQLI_ASSOC);
    ?>

    <!-- CARRUSEL -->
    <div class="carrusel-wrapper">
        <button class="carrusel-btn carrusel-prev" id="btnPrev">
            <i class="fa-solid fa-chevron-left"></i>
        </button>

        <div class="carrusel-contenedor" id="carruselContenedor">
            <?php foreach ($listaAvisos as $aviso): ?>
                <div class="aviso carrusel-item" onclick="abrirModalAviso(
                    '<?= addslashes(htmlspecialchars($aviso['titulo'])) ?>',
                    '<?= addslashes(htmlspecialchars($aviso['mensaje'])) ?>',
                    '<?= addslashes(htmlspecialchars($aviso['departamento'])) ?>',
                    '<?= date("d/m/Y H:i", strtotime($aviso['fecha_creacion'])) ?>'
                )">
                    <span class="aviso-departamento">
                        <i class="fa-solid fa-building"></i>
                        <?= htmlspecialchars($aviso['departamento']) ?>
                    </span>
                    <h4><?= htmlspecialchars($aviso['titulo']) ?></h4>
                    <p><?= htmlspecialchars($aviso['mensaje']) ?></p>
                    <span class="fecha">
                        Publicado: <?= date("d/m/Y H:i", strtotime($aviso['fecha_creacion'])) ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>

        <button class="carrusel-btn carrusel-next" id="btnNext">
            <i class="fa-solid fa-chevron-right"></i>
        </button>
    </div>

    <!-- INDICADORES -->
    <div class="carrusel-indicadores" id="carruselIndicadores"></div>

    <?php else: ?>
        <div class="avisos-vacio">
            <i class="fa-solid fa-bullhorn"></i>
            <p>No hay avisos publicados por el momento.</p>
        </div>
    <?php endif; ?>

</section>

<!-- MODAL AVISO -->
<div class="modal-aviso-usuario" id="modalAvisoUsuario">
    <div class="modal-aviso-usuario-content">
        <div class="modal-aviso-usuario-header">
            <div class="modal-aviso-usuario-meta">
                <span class="aviso-departamento" id="modalAvisoDept">
                    <i class="fa-solid fa-building"></i>
                    <span id="modalAvisoDeptNombre"></span>
                </span>
            </div>
            <button class="modal-aviso-usuario-cerrar" id="cerrarModalAvisoUsuario">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-aviso-usuario-body">
            <h3 id="modalAvisoTitulo"></h3>
            <p id="modalAvisoMensaje"></p>
            <span class="fecha" id="modalAvisoFecha"></span>
        </div>
    </div>
</div>

</main>

<script src="../assets/js/sidebar_usuario.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../assets/js/dashboard_usuario.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>