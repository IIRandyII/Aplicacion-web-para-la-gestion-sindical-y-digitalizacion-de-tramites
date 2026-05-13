<?php
/* ===============================
   SIDEBAR AFILIADO
   Incluir en cada página así:
   $paginaActiva = "inicio";
   include "../includes/sidebar_afiliado.php";
================================ */

// Contar notificaciones no leídas del afiliado
$stmtNotifAfiliado = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM notificaciones_afiliado
    WHERE id_afiliado = ? AND leida = 0
");
$stmtNotifAfiliado->bind_param("i", $id_usuario);
$stmtNotifAfiliado->execute();
$totalNotifAfiliado = $stmtNotifAfiliado->get_result()->fetch_assoc()['total'];
?>

<aside class="sidebar" id="sidebar">
    <div class="logo">
        <img src="../assets/img/logo.jpg" alt="Logo">
        <span>Sección 49</span>
    </div>

    <nav class="menu">
        <a href="dashboard_afiliado.php" class="<?= $paginaActiva === 'inicio' ? 'active' : '' ?>">
            <i class="fa-solid fa-house"></i>
            <span>Inicio</span>
        </a>

        <a href="perfil_afiliado.php" class="<?= $paginaActiva === 'perfil' ? 'active' : '' ?>">
            <i class="fa-solid fa-user"></i>
            <span>Mi perfil</span>
        </a>

        <a href="historial_afiliado.php" class="<?= $paginaActiva === 'historial' ? 'active' : '' ?>">
            <i class="fa-solid fa-clock-rotate-left"></i>
            <span>Historial</span>
        </a>
        <a href="archivados_afiliado.php" class="<?= $paginaActiva === 'archivados' ? 'active' : '' ?>">
            <i class="fa-solid fa-box-archive"></i>
            <span>Archivados</span>
        </a>
                
        <a href="notificaciones_afiliado.php" class="<?= $paginaActiva === 'notificaciones' ? 'active' : '' ?>">
            <span class="icono-notificacion">
                <i class="fa-solid fa-bell"></i>
                <?php if ($totalNotifAfiliado > 0): ?>
                    <span class="badge-notificacion"><?= $totalNotifAfiliado ?></span>
                <?php endif; ?>
            </span>
            <span>Notificaciones</span>
        </a>

        <a href="reportes_afiliado.php" class="<?= $paginaActiva === 'reportes' ? 'active' : '' ?>">
            <i class="fa-solid fa-file-lines"></i>
            <span>Reportes</span>
        </a>

        <a href="avisos_afiliado.php" class="<?= $paginaActiva === 'avisos' ? 'active' : '' ?>">
            <i class="fa-solid fa-bullhorn"></i>
            <span>Crear avisos</span>
        </a>

        <a href="../sesion/logout.php" class="logout">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Cerrar sesión</span>
        </a>
    </nav>
</aside>