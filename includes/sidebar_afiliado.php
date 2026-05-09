<?php
/* ===============================
   SIDEBAR AFILIADO
   Incluir en cada página así:
   $paginaActiva = "inicio";
   include "../includes/sidebar_afiliado.php";
================================ */
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