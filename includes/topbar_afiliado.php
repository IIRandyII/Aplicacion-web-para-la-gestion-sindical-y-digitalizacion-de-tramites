<?php
/* ===============================
   TOPBAR AFILIADO
   Incluir en cada página así:
   $tituloTopbar = "Avisos";
   include "../includes/topbar_afiliado.php";
   Requiere que $id_departamento y
   $id_usuario estén definidos.
================================ */

// ===============================
// OBTENER NOMBRE DEL DEPARTAMENTO
// ===============================
$stmtDeptTopbar = $conn->prepare("SELECT nombre FROM departamentos WHERE id_departamento = ?");
$stmtDeptTopbar->bind_param("i", $id_departamento);
$stmtDeptTopbar->execute();
$deptTopbar       = $stmtDeptTopbar->get_result()->fetch_assoc();
$nombreDeptTopbar = $deptTopbar['nombre'] ?? 'Departamento';

// ===============================
// OBTENER FOTO Y NOMBRE DEL AFILIADO
// ===============================
$stmtFotoAfiliado = $conn->prepare("SELECT nombre, foto FROM usuarios WHERE id_usuario = ?");
$stmtFotoAfiliado->bind_param("i", $id_usuario);
$stmtFotoAfiliado->execute();
$rowAfiliado          = $stmtFotoAfiliado->get_result()->fetch_assoc();
$nombreAfiliadoTopbar = $rowAfiliado['nombre'] ?? $_SESSION['nombre'];
$fotoAfiliado         = $rowAfiliado['foto']   ?? null;

// ===============================
// OBTENER INICIALES DEL NOMBRE
// Si no hay foto se muestran iniciales
// ===============================
function obtenerInicialesAfiliado($nombre) {
    if (empty(trim($nombre))) return '?';
    $partes   = explode(" ", trim($nombre));
    $inicial1 = strtoupper($partes[0][0]);
    $ultimo   = end($partes);
    $inicial2 = (count($partes) > 1 && !empty($ultimo)) ? strtoupper($ultimo[0]) : '';
    return $inicial1 . $inicial2;
}

$inicialesAfiliado = obtenerInicialesAfiliado($nombreAfiliadoTopbar);
?>

<!-- ===============================
     TOPBAR AFILIADO
================================ -->
<div class="topbar">

    <!-- Botón hamburguesa -->
    <button class="toggle-btn" id="toggleSidebar">
        <i class="fa-solid fa-bars"></i>
    </button>

    <!-- Título dinámico según la página -->
    <h2 class="topbar-titulo">
        <?php if (isset($tituloTopbar)): ?>
            <?= htmlspecialchars($nombreDeptTopbar) ?> — <?= htmlspecialchars($tituloTopbar) ?>
        <?php else: ?>
            Panel de <?= htmlspecialchars($nombreDeptTopbar) ?> — <?= htmlspecialchars($nombreAfiliadoTopbar) ?>
        <?php endif; ?>
    </h2>

    <!-- Info del afiliado -->
    <div class="topbar-usuario">
        <span class="topbar-nombre"><?= htmlspecialchars($nombreAfiliadoTopbar) ?></span>

        <?php if (!empty($fotoAfiliado)): ?>
            <img src="../<?= htmlspecialchars($fotoAfiliado) ?>"
                 alt="Foto"
                 class="topbar-foto">
        <?php else: ?>
            <div class="topbar-iniciales">
                <?= $inicialesAfiliado ?>
            </div>
        <?php endif; ?>
    </div>

</div>