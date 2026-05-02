<?php
require_once("../includes/auth_usuario.php");
require_once __DIR__ . "/../config/db.php";

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
    <title>Nuevo Trámite | Sección 49</title>

    <link rel="stylesheet" href="../assets/css/nuevo_tramite.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
        <a href="nuevo_tramite.php" class="active"><i class="fa-solid fa-file-circle-plus"></i><span>Nuevo trámite</span></a>

        <a href="notificaciones.php" class="notificacion-link">
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

<main class="main">

    <div class="topbar">
        <button class="toggle-btn" id="toggleSidebar">
            <i class="fa-solid fa-bars"></i>
        </button>
        <h2>Crear nuevo trámite</h2>
    </div>

    <section class="tramite-section">
        <div class="tramite-header">
            <button id="btnAbrirModal" class="btn-primary">
                <i class="fa-solid fa-plus"></i> Crear trámite
            </button>
        </div>

        <div class="tramites-contenedor">
            <h4><i class="fa-solid fa-list-check"></i> Mis trámites</h4>

            <div class="filtros-tabla">
                <div class="buscador">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="buscarTramite" placeholder="Buscar trámite...">
                </div>

                <select id="ordenFecha">
                    <option value="desc">Más reciente</option>
                    <option value="asc">Más antiguo</option>
                </select>
            </div>

            <table class="tabla-tramites">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Departamento</th>
                        <th>Trámite</th>
                        <th>Nombre</th>
                        <th>Ficha</th>
                        <th>Categoría</th>
                        <th>Turno</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody id="listaTramites">
                    <tr>
                        <td colspan="9" class="vacio">
                            Aún no has creado trámites
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

</main>

<div class="modal" id="modalTramite">
    <div class="modal-content">
        <span class="close">&times;</span>

        <h3>Nuevo trámite</h3>

        <div class="fila-selects">
            <div>
                <label>Departamento</label>
                <select id="departamento">
                    <option value="">Seleccione</option>
                    <option value="1">Secretaría de Actas</option>
                    <option value="2">Tesorería</option>
                    <option value="3">Secretaría de Ajustes</option>
                </select>
            </div>

            <div>
                <label>Trámite</label>
                <select id="tipoTramite">
                    <option value="">Seleccione</option>
                </select>
            </div>
        </div>

        <div id="formularioTramite"></div>
    </div>
</div>

<div class="modal" id="modalVerTramite">
    <div class="modal-content modal-grande">
        <span class="close" id="cerrarVerTramite">&times;</span>

        <h3>Detalle del trámite</h3>

        <div id="detalleTramite">
            <!-- aquí se inyecta la info -->
        </div>
    </div>
</div>

<script src="../assets/js/nuevo_tramite.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>