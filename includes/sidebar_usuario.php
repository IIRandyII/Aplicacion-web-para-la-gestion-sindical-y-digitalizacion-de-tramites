<?php
/* ===============================
   SIDEBAR USUARIO
   Incluir en cada página así:
   $paginaActiva = "inicio";
   include "../includes/sidebar_usuario.php";
================================ */

/* Contar notificaciones no leídas */
$stmtNotif = $conn->prepare("
    SELECT COUNT(*) AS total 
    FROM notificaciones 
    WHERE id_usuario = ? AND leida = 0
");
$stmtNotif->bind_param("i", $id_usuario);
$stmtNotif->execute();
$resultNotif = $stmtNotif->get_result();
$rowNotif    = $resultNotif->fetch_assoc();
$totalNoLeidas = $rowNotif['total'];
?>

<aside class="sidebar" id="sidebar">
    <div class="logo">
        <img src="../assets/img/logo.jpg" alt="Logo">
        <span>Sección 49</span>
    </div>

    <nav class="menu">
        <a href="dashboard_usuario.php" class="<?= $paginaActiva === 'inicio' ? 'active' : '' ?>">
            <i class="fa-solid fa-house"></i><span>Inicio</span>
        </a>
        <a href="perfil_usuario.php" class="<?= $paginaActiva === 'perfil' ? 'active' : '' ?>">
            <i class="fa-solid fa-user"></i><span>Mi perfil</span>
        </a>
        <a href="nuevo_tramite.php" class="<?= $paginaActiva === 'tramites' ? 'active' : '' ?>">
            <i class="fa-solid fa-file-circle-plus"></i><span>Nuevo trámite</span>
        </a>
        <a href="notificaciones.php" class="<?= $paginaActiva === 'notificaciones' ? 'active' : '' ?>">
            <span class="icono-notificacion">
                <i class="fa-solid fa-bell"></i>
                <?php if ($totalNoLeidas > 0): ?>
                    <span class="badge-notificacion"><?= $totalNoLeidas ?></span>
                <?php endif; ?>
            </span>
            <span>Notificaciones</span>
        </a>
        <a href="../sesion/logout.php" class="logout">
            <i class="fa-solid fa-right-from-bracket"></i><span>Cerrar sesión</span>
        </a>
    </nav>
</aside>