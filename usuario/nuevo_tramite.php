<?php
require_once("../includes/auth_usuario.php");
require_once __DIR__ . "/../config/db.php";

// Variables de sesión y página activa
$paginaActiva = "tramites";
$id_usuario   = $_SESSION['id_usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Trámite | Sección 49</title>

    <!-- Estilos propios -->
    <link rel="stylesheet" href="../assets/css/sidebar_usuario.css">
    <link rel="stylesheet" href="../assets/css/nuevo_tramite.css">

    <!-- Librerías externas -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<!-- SIDEBAR -->
<?php include "../includes/sidebar_usuario.php"; ?>

<!-- CONTENIDO PRINCIPAL -->
<main class="main">

    <!-- TOPBAR -->
    <?php
    $tituloTopbar = "Nuevo Tramite";
    include "../includes/topbar_usuario.php";
    ?>

    <!-- SECCIÓN PRINCIPAL DE TRÁMITES -->
    <section class="tramite-section">

        <!-- BOTÓN CREAR TRÁMITE -->
        <div class="tramite-header">
            <button id="btnAbrirModal" class="btn-primary">
                <i class="fa-solid fa-plus"></i> Crear trámite
            </button>
        </div>

        <!-- TABLA DE TRÁMITES -->
        <div class="tramites-contenedor">
            <h4><i class="fa-solid fa-list-check"></i> Mis trámites</h4>

            <!-- FILTROS -->
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

            <!-- TABLA -->
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

<!-- MODAL: CREAR NUEVO TRÁMITE -->
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

        <!-- Formulario dinámico según tipo de trámite -->
        <div id="formularioTramite"></div>
    </div>
</div>

<!-- MODAL: VER DETALLE DEL TRÁMITE -->
<div class="modal" id="modalVerTramite">
    <div class="modal-content modal-grande">
        <span class="close" id="cerrarVerTramite">&times;</span>
        <div id="detalleTramite"></div>
    </div>
</div>

        <!-- Contenido inyectado dinámicamente con JS -->
        <div id="detalleTramite"></div>
    </div>
</div>

<!-- SCRIPTS -->
<script src="../assets/js/sidebar_usuario.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../assets/js/formularios_tramite.js"></script>
<script src="../assets/js/nuevo_tramite.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>