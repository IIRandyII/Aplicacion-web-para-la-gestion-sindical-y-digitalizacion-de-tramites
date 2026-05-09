<?php
/* ===============================
   TOPBAR AFILIADO
   Incluir en cada página así:
   $tituloTopbar = "Avisos";
   include "../includes/topbar_afiliado.php";
   Requiere que $id_departamento esté
   definido antes de incluir.
================================ */

// ===============================
// OBTENER NOMBRE DEL DEPARTAMENTO
// ===============================
$stmtDeptTopbar = $conn->prepare("SELECT nombre FROM departamentos WHERE id_departamento = ?");
$stmtDeptTopbar->bind_param("i", $id_departamento);
$stmtDeptTopbar->execute();
$deptTopbar          = $stmtDeptTopbar->get_result()->fetch_assoc();
$nombreDeptTopbar    = $deptTopbar['nombre'] ?? 'Departamento';
$nombreAfiliadoTopbar = $_SESSION['nombre'];
?>

<!-- ===============================
     TOPBAR AFILIADO
     Muestra departamento, título
     y nombre del afiliado
================================ -->
<div class="topbar">

    <!-- Botón hamburguesa -->
    <button class="toggle-btn" id="toggleSidebar">
        <i class="fa-solid fa-bars"></i>
    </button>

    <!-- Título dinámico según la página -->
    <h2 class="topbar-titulo">
        <?php if (isset($tituloTopbar)): ?>
            <!-- Páginas secundarias: Departamento - Título -->
            <?= htmlspecialchars($nombreDeptTopbar) ?> — <?= htmlspecialchars($tituloTopbar) ?>
        <?php else: ?>
            <!-- Dashboard: Panel de Departamento - Afiliado -->
            Panel de <?= htmlspecialchars($nombreDeptTopbar) ?> — <?= htmlspecialchars($nombreAfiliadoTopbar) ?>
        <?php endif; ?>
    </h2>

</div>