<?php
require_once("../includes/auth_afiliado.php");
require_once("../config/db.php");

// Variables de sesión y página activa
$paginaActiva    = "avisos";
$nombreAfiliado  = $_SESSION['nombre'];
$id_departamento = $_SESSION['id_departamento'];

// ===============================
// OBTENER NOMBRE DEL DEPARTAMENTO
// ===============================
$stmtDept = $conn->prepare("SELECT nombre FROM departamentos WHERE id_departamento = ?");
$stmtDept->bind_param("i", $id_departamento);
$stmtDept->execute();
$dept               = $stmtDept->get_result()->fetch_assoc();
$nombreDepartamento = $dept['nombre'];

// ===============================
// OBTENER AVISOS DEL DEPARTAMENTO
// Ordenados del más reciente al más antiguo
// ===============================
$stmtAvisos = $conn->prepare("
    SELECT id_aviso, titulo, mensaje, fecha_creacion
    FROM avisos
    WHERE id_departamento = ?
    ORDER BY fecha_creacion DESC
");
$stmtAvisos->bind_param("i", $id_departamento);
$stmtAvisos->execute();
$avisos = $stmtAvisos->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Avisos | Sección 49</title>

    <!-- Estilos propios -->
    <link rel="stylesheet" href="../assets/css/afiliado/sidebar_afiliado.css">
    <link rel="stylesheet" href="../assets/css/afiliado/avisos_afiliado.css">

    <!-- Librerías externas -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<!-- SIDEBAR -->
<?php include "../includes/sidebar_afiliado.php"; ?>

<!-- CONTENIDO PRINCIPAL -->
<main class="main">

    <?php
    $tituloTopbar = "Avisos";
    include "../includes/topbar_afiliado.php";
    ?>

    <section class="avisos-section">

        <!-- BOTÓN CREAR AVISO -->
        <div class="avisos-header">
            <button class="btn-nuevo-aviso" id="btnNuevoAviso">
                <i class="fa-solid fa-plus"></i> Nuevo aviso
            </button>
        </div>

        <!-- LISTA DE AVISOS -->
        <div class="avisos-lista">

            <?php if ($avisos->num_rows > 0): ?>

                <?php while ($aviso = $avisos->fetch_assoc()): ?>
                    <div class="aviso-card">
                        <div class="aviso-card-header">
                            <div class="aviso-info">
                                <h4><?= htmlspecialchars($aviso['titulo']) ?></h4>
                                <span class="aviso-fecha">
                                    <i class="fa-solid fa-calendar"></i>
                                    <?= date("d/m/Y H:i", strtotime($aviso['fecha_creacion'])) ?>
                                </span>
                            </div>
                            <div class="aviso-acciones">
                                <button class="btn-editar"
                                    onclick="editarAviso(
                                        <?= $aviso['id_aviso'] ?>,
                                        '<?= addslashes($aviso['titulo']) ?>',
                                        '<?= addslashes($aviso['mensaje']) ?>'
                                    )">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button class="btn-eliminar"
                                    onclick="eliminarAviso(<?= $aviso['id_aviso'] ?>)">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <p class="aviso-mensaje"><?= htmlspecialchars($aviso['mensaje']) ?></p>
                    </div>
                <?php endwhile; ?>

            <?php else: ?>

                <!-- Estado vacío -->
                <div class="avisos-vacio">
                    <i class="fa-solid fa-bullhorn"></i>
                    <p>No hay avisos publicados aún.</p>
                </div>

            <?php endif; ?>

        </div>

    </section>

</main>

<!-- MODAL CREAR/EDITAR AVISO -->
<div class="modal-aviso" id="modalAviso">
    <div class="modal-aviso-content">

        <div class="modal-aviso-header">
            <h3 id="modalAvisoTitulo">Nuevo aviso</h3>
            <button class="modal-aviso-cerrar" id="cerrarModalAviso">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="formAviso">
            <input type="hidden" id="avisoId">

            <div class="form-grupo">
                <label>Título</label>
                <input type="text" id="avisoTitulo" placeholder="Título del aviso" required>
            </div>

            <div class="form-grupo">
                <label>Mensaje</label>
                <textarea id="avisoMensaje" placeholder="Escribe el mensaje del aviso..." required></textarea>
            </div>

            <div class="form-acciones">
                <button type="button" class="btn-cancelar" id="cancelarAviso">Cancelar</button>
                <button type="submit" class="btn-guardar">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar
                </button>
            </div>
        </form>

    </div>
</div>

<!-- SCRIPTS -->
<script src="../assets/js/afiliado/sidebar_afiliado.js"></script>
<script src="../assets/js/afiliado/avisos_afiliado.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>