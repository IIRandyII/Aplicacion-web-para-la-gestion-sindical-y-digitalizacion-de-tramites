<?php
require_once("../includes/auth_afiliado.php");
require_once("../config/db.php");

// Variables de sesión y página activa
$paginaActiva    = "inicio";
$nombreAfiliado  = $_SESSION['nombre'];
$id_departamento = $_SESSION['id_departamento'];
$id_usuario      = $_SESSION['id_usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Afiliado | Sección 49</title>
    <link rel="stylesheet" href="../assets/css/afiliado/dashboard_afiliado.css"> 
    <link rel="stylesheet" href="../assets/css/afiliado/sidebar_afiliado.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<!-- SIDEBAR -->
 <?php include "../includes/sidebar_afiliado.php"; ?>

<!-- CONTENIDO -->
<main class="main"> 

<!-- TOPBAR -->
 <?php include "../includes/topbar_afiliado.php"; ?>

    <div class="cards-container">
    <div class="info-card tramites active" onclick="cargarTramites('Todos', this)">
    <i class="icon fa-solid fa-folder-open"></i>
    <h3>Todos</h3>
    <p id="count_todos">0</p>
</div>

<div class="info-card pendientes" onclick="cargarTramites('Pendiente', this)">
    <i class="icon fa-solid fa-clock"></i>
    <h3>Pendientes</h3>
    <p id="count_pendientes">0</p>
</div>

<div class="info-card revision" onclick="cargarTramites('En revisión', this)">
    <i class="icon fa-solid fa-file-alt"></i>
    <h3>En revisión</h3>
    <p id="count_revision">0</p>
</div>

<div class="info-card aprobados" onclick="cargarTramites('Aprobado', this)">
    <i class="icon fa-solid fa-check"></i>
    <h3>Aprobados</h3>
    <p id="count_aprobados">0</p>
</div>

<div class="info-card rechazados" onclick="cargarTramites('Rechazado', this)">
    <i class="icon fa-solid fa-xmark"></i>
    <h3>Rechazados</h3>
    <p id="count_rechazados">0</p>
</div>
</div>

    <!-- TABLA -->
    <section class="avisos">
        <h3>Gestión de Trámites</h3>

        <div class="row mb-3">
    <div class="col-md-6">
        <input type="text" 
               id="buscador" 
               class="form-control" 
               placeholder="Buscar por nombre, tipo o ID...">
    </div>

    <div class="col-md-4">
        <select id="filtroFecha" class="form-select">
            <option value="Todos">Todas las fechas</option>
            <option value="Hoy">Hoy</option>
            <option value="Semana">Esta semana</option>
            <option value="Mes">Este mes</option>
            <option value="Anio">Este año</option>
        </select>
    </div>
</div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Solicitante</th>
                        <th>Tipo de trámite</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="tabla_tramites">
                    <!-- Se llena con JS -->
                </tbody>
            </table>
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

<script src="../assets/js/afiliado/sidebar_afiliado.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../assets/js/afiliado/dashboard_afiliado.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>