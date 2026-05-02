<?php
require_once("../includes/auth_usuario.php");
require_once("../config/db.php");

$nombreUsuario = $_SESSION['nombre'];
$id_usuario = $_SESSION['id_usuario'];

$stmt = $conn->prepare("
    SELECT titulo, mensaje, fecha 
    FROM notificaciones 
    WHERE id_usuario = ? 
    ORDER BY fecha DESC
");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

/* 🔔 Marcar notificaciones como leídas */
$stmtMarcar = $conn->prepare("
    UPDATE notificaciones 
    SET leida = 1 
    WHERE id_usuario = ? AND leida = 0
");
$stmtMarcar->bind_param("i", $id_usuario);
$stmtMarcar->execute();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Notificaciones | Sección 49</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/notificaciones_usuario.css">
</head>
<body>

<aside class="sidebar" id="sidebar">
    <div class="logo">
        <img src="../assets/img/logo.jpg" alt="Logo">
        <span>Sección 49</span>
    </div>

    <nav class="menu">
        <a href="dashboard_usuario.php"><i class="fa-solid fa-house"></i><span>Inicio</span></a>
        <a href="perfil_usuario.php"><i class="fa-solid fa-user"></i><span>Mi perfil</span></a>
        <a href="nuevo_tramite.php"><i class="fa-solid fa-file-circle-plus"></i><span>Nuevo trámite</span></a>
        <a href="notificaciones.php" class="active"><i class="fa-solid fa-bell"></i><span>Notificaciones</span></a>
        <a href="../sesion/logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i><span>Cerrar sesión</span></a>
    </nav>
</aside>

<main class="main">

    <div class="topbar">
        <button class="toggle-btn" id="toggleSidebar">
            <i class="fa-solid fa-bars"></i>
        </button>
        <h2>Mis Notificaciones</h2>
    </div>

    <section class="notificaciones-section">
        <div class="notificaciones-container">

            <?php if ($result->num_rows > 0): ?>
                
                <?php while($notif = $result->fetch_assoc()): ?>
                    
                    <div class="notificacion-card">
                        <div class="notif-icon">
                            <i class="fa-solid fa-bell"></i>
                        </div>
                        <div class="notif-content">
                            <h5><?= htmlspecialchars($notif['titulo']) ?></h5>
                            <p><?= htmlspecialchars($notif['mensaje']) ?></p>
                            <span class="fecha">
                                <?= date("d/m/Y H:i", strtotime($notif['fecha'])) ?>
                            </span>
                        </div>
                    </div>

                <?php endwhile; ?>

            <?php else: ?>

                <div class="notificacion-card">
                    <div class="notif-content">
                        <h5>No tienes notificaciones</h5>
                        <p>Aún no hay actualizaciones de tus trámites.</p>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </section>

</main>

<script src="../assets/js/dashboard_usuario.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>