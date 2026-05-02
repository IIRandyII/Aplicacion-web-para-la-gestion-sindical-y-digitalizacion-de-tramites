<?php
require_once("../includes/auth_usuario.php");
require_once("../config/db.php");

$nombreUsuario = $_SESSION['nombre'];
$id_usuario = $_SESSION['id_usuario'];

/* 🔔 Contar notificaciones no leídas */
$stmtNotif = $conn->prepare("
    SELECT COUNT(*) AS total 
    FROM notificaciones 
    WHERE id_usuario = ? AND leida = 0
");
$stmtNotif->bind_param("i", $id_usuario);
$stmtNotif->execute();
$resultNotif = $stmtNotif->get_result();
$rowNotif = $resultNotif->fetch_assoc();
$totalNoLeidas = $rowNotif['total'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Usuario | Sección 49</title>
    <link rel="stylesheet" href="../assets/css/dashboard_usuario.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="logo">
        <img src="../assets/img/logo.jpg" alt="Logo">
        <span>Sección 49</span>
    </div>

    <nav class="menu">
        <a href="dashboard_usuario.php" class="active"><i class="fa-solid fa-house"></i><span>Inicio</span></a>
        <a href="perfil_usuario.php"><i class="fa-solid fa-user"></i><span>Mi perfil</span></a>
        <a href="nuevo_tramite.php"><i class="fa-solid fa-file-circle-plus"></i><span>Nuevo trámite</span></a>
<a href="notificaciones.php" class="notificacion-link">
    <span class="icono-notificacion">
        <i class="fa-solid fa-bell"></i>
        <?php if ($totalNoLeidas > 0): ?>
            <span class="badge-notificacion">
                <?= $totalNoLeidas ?>
            </span>
        <?php endif; ?>
    </span>
    <span>Notificaciones</span>
</a>
        <a href="../sesion/logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i><span>Cerrar sesión</span></a>
    </nav>
</aside>

<!-- CONTENIDO -->
<main class="main">

        
    <!-- TOPBAR -->
    <div class="topbar">
        <button class="toggle-btn" id="toggleSidebar">
            <i class="fa-solid fa-bars"></i>
        </button>
        <h2>Bienvenido, <?php echo htmlspecialchars($nombreUsuario); ?></h2>
    </div>

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

    <!-- AVISOS -->
    <section class="avisos">
        <h3><i class="fa-solid fa-bullhorn"></i> Avisos importantes</h3>

        <div class="avisos-grid">
            <div class="aviso">
                <h4>Suspensión de labores</h4>
                <p>
                    Se informa a todos los trabajadores que el día viernes
                    habrá suspensión de labores por mantenimiento general.
                </p>
                <span class="fecha">Publicado: 20/01/2026</span>
            </div>

            <div class="aviso">
                <h4>Entrega de documentos</h4>
                <p>
                    La fecha límite para entregar documentos será el 30 de enero.
                </p>
                <span class="fecha">Publicado: 18/01/2026</span>
            </div>
        </div>
    </section>

</main>

<script src="../assets/js/dashboard_usuario.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
