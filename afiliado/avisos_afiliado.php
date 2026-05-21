<?php
require_once("../includes/auth_afiliado.php");
require_once("../config/db.php");

// Variables de sesión y página activa
$paginaActiva    = "avisos";
$nombreAfiliado  = $_SESSION['nombre'];
$id_departamento = $_SESSION['id_departamento'];
$id_usuario      = $_SESSION['id_usuario'];

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
// ===============================
$stmtAvisos = $conn->prepare("
    SELECT id_aviso, titulo, mensaje, tipo, fecha_creacion
    FROM avisos
    WHERE id_departamento = ?
    ORDER BY fecha_creacion DESC
");
$stmtAvisos->bind_param("i", $id_departamento);
$stmtAvisos->execute();
$avisos      = $stmtAvisos->get_result();
$totalAvisos = $avisos->num_rows;

// Avisos de este mes
$stmtMes = $conn->prepare("
    SELECT COUNT(*) as total FROM avisos
    WHERE id_departamento = ?
    AND MONTH(fecha_creacion) = MONTH(CURDATE())
    AND YEAR(fecha_creacion)  = YEAR(CURDATE())
");
$stmtMes->bind_param("i", $id_departamento);
$stmtMes->execute();
$avisosMes = $stmtMes->get_result()->fetch_assoc()['total'];

// Total histórico
$stmtArch = $conn->prepare("SELECT COUNT(*) as total FROM avisos WHERE id_departamento = ?");
$stmtArch->bind_param("i", $id_departamento);
$stmtArch->execute();
$totalHistorico = $stmtArch->get_result()->fetch_assoc()['total'];

// Mapa de etiquetas por tipo
$tipoLabels = [
    'general'     => 'General',
    'urgente'     => 'Urgente',
    'informativo' => 'Informativo',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Avisos | Sección 49</title>
    <link rel="stylesheet" href="../assets/css/afiliado/sidebar_afiliado.css">
    <link rel="stylesheet" href="../assets/css/afiliado/avisos_afiliado.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php include "../includes/sidebar_afiliado.php"; ?>

<main class="main">

    <?php
    $tituloTopbar = "Avisos";
    include "../includes/topbar_afiliado.php";
    ?>

    <section class="avisos-section">

        <!-- TOOLBAR -->
        <div class="avisos-toolbar">
            <div class="avisos-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="buscadorAvisos" placeholder="Buscar avisos...">
            </div>
            <button class="btn-nuevo-aviso" id="btnNuevoAviso">
                <i class="fa-solid fa-plus"></i> Nuevo aviso
            </button>
        </div>

        <!-- ESTADÍSTICAS -->
        <div class="avisos-stats">
            <div class="stat-card">
                <div class="stat-label">Total avisos</div>
                <div class="stat-val"><?= $totalAvisos ?></div>
                <div class="stat-sub">Publicados</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Este mes</div>
                <div class="stat-val"><?= $avisosMes ?></div>
                <div class="stat-sub">Avisos recientes</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Histórico</div>
                <div class="stat-val"><?= $totalHistorico ?></div>
                <div class="stat-sub">Total registrados</div>
            </div>
        </div>

        <!-- ENCABEZADO + FILTROS -->
        <div class="avisos-section-header">
            <span class="avisos-section-title">Avisos recientes</span>
            <div class="avisos-filtros">
                <button class="filtro-tab activo" data-filtro="todos">Todos</button>
                <button class="filtro-tab" data-filtro="general">General</button>
                <button class="filtro-tab" data-filtro="urgente">Urgente</button>
                <button class="filtro-tab" data-filtro="informativo">Info</button>
            </div>
        </div>

        <!-- LISTA DE AVISOS -->
        <div class="avisos-lista" id="avisosLista">

            <?php if ($avisos->num_rows > 0): ?>

                <?php while ($aviso = $avisos->fetch_assoc()):
                    $tipo  = $aviso['tipo'] ?? 'general';
                    $label = $tipoLabels[$tipo] ?? 'General';
                ?>
                    <div class="aviso-card <?= $tipo ?>" data-tipo="<?= $tipo ?>" data-id="<?= $aviso['id_aviso'] ?>">
                        <div class="aviso-card-header">
                            <div class="aviso-info">
                                <div class="aviso-titulo-row">
                                    <h4><?= htmlspecialchars($aviso['titulo']) ?></h4>
                                    <span class="aviso-badge <?= $tipo ?>"><?= $label ?></span>
                                </div>
                                <div class="aviso-meta">
                                    <span>
                                        <i class="fa-solid fa-calendar"></i>
                                        <?= date("d/m/Y", strtotime($aviso['fecha_creacion'])) ?>
                                    </span>
                                    <span>
                                        <i class="fa-solid fa-clock"></i>
                                        <?= date("H:i", strtotime($aviso['fecha_creacion'])) ?>
                                    </span>
                                    <span>
                                        <i class="fa-solid fa-user"></i>
                                        <?= htmlspecialchars($nombreAfiliado) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="aviso-acciones">
                                <button class="btn-editar"
                                    onclick="editarAviso(
                                        <?= $aviso['id_aviso'] ?>,
                                        '<?= addslashes($aviso['titulo']) ?>',
                                        '<?= addslashes($aviso['mensaje']) ?>',
                                        '<?= $tipo ?>'
                                    )"
                                    title="Editar aviso">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button class="btn-eliminar"
                                    onclick="eliminarAviso(<?= $aviso['id_aviso'] ?>)"
                                    title="Eliminar aviso">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <p class="aviso-mensaje"><?= htmlspecialchars($aviso['mensaje']) ?></p>
                    </div>
                <?php endwhile; ?>

            <?php else: ?>
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
                <label>Tipo de aviso</label>
                <select id="avisoTipo">
                    <option value="general">General</option>
                    <option value="urgente">Urgente</option>
                    <option value="informativo">Informativo</option>
                </select>
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

<script>
// Buscador en tiempo real
document.getElementById("buscadorAvisos").addEventListener("input", function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll("#avisosLista .aviso-card").forEach(card => {
        const titulo  = card.querySelector("h4").textContent.toLowerCase();
        const mensaje = card.querySelector(".aviso-mensaje").textContent.toLowerCase();
        card.style.display = (titulo.includes(q) || mensaje.includes(q)) ? "" : "none";
    });
});

// Filtros por tipo
document.querySelectorAll(".filtro-tab").forEach(tab => {
    tab.addEventListener("click", function () {
        document.querySelectorAll(".filtro-tab").forEach(t => t.classList.remove("activo"));
        this.classList.add("activo");
        const filtro = this.dataset.filtro;
        document.querySelectorAll("#avisosLista .aviso-card").forEach(card => {
            card.style.display = (filtro === "todos" || card.dataset.tipo === filtro) ? "" : "none";
        });
    });
});
</script>

</body>
</html>