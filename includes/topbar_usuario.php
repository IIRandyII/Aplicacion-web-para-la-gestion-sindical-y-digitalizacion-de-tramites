<?php
/* ===============================
   TOPBAR USUARIO
   Incluir en cada página así:
   include "../includes/topbar_usuario.php";
   Requiere que $id_usuario y $tituloTopbar
   estén definidos antes de incluir.
   Ejemplo:
   $tituloTopbar = "Bienvenido, $nombreUsuario";
================================ */

// ===============================
// OBTENER FOTO DEL USUARIO
// Se consulta la foto del usuario
// desde la base de datos usando
// el id_usuario de la sesión
// ===============================
$stmtFoto = $conn->prepare("SELECT foto, nombre FROM usuarios WHERE id_usuario = ?");
// bind_param liga el parámetro ? con la variable $id_usuario de tipo entero (i)
$stmtFoto->bind_param("i", $id_usuario);
$stmtFoto->execute();
$rowFoto = $stmtFoto->get_result()->fetch_assoc();

// Guardamos la foto y nombre en variables locales
$fotoUsuario   = $rowFoto['foto']   ?? null;
$nombreTopbar  = $rowFoto['nombre'] ?? $_SESSION['nombre'];

// ===============================
// OBTENER INICIALES DEL NOMBRE
// Si el usuario no tiene foto se
// muestran sus iniciales como avatar
// ===============================
function obtenerInicialesTopbar($nombre) {
    // Si el nombre está vacío retorna ?
    if (empty(trim($nombre))) return '?';

    // Separamos el nombre en partes por espacio
    $partes = explode(" ", trim($nombre));

    // Primera inicial del primer nombre
    $inicial1 = strtoupper($partes[0][0]);

    // Primera inicial del último apellido (si existe)
    $ultimo   = end($partes);
    $inicial2 = (count($partes) > 1 && !empty($ultimo)) ? strtoupper($ultimo[0]) : '';

    return $inicial1 . $inicial2;
}

$inicialesTopbar = obtenerInicialesTopbar($nombreTopbar);
?>

<!-- ===============================
     TOPBAR
     Barra superior con botón de menú,
     título de la página, nombre,
     foto o iniciales del usuario
================================ -->
<div class="topbar">

    <!-- Botón hamburguesa para abrir/cerrar sidebar -->
    <button class="toggle-btn" id="toggleSidebar">
        <i class="fa-solid fa-bars"></i>
    </button>

    <!-- Título dinámico de la página actual -->
    <h2 class="topbar-titulo"><?= htmlspecialchars($tituloTopbar) ?></h2>

    <!-- Info del usuario alineada a la derecha -->
    <div class="topbar-usuario">

        <!-- Nombre del usuario -->
        <span class="topbar-nombre"><?= htmlspecialchars($nombreTopbar) ?></span>

        <?php if (!empty($fotoUsuario)): ?>
            <!-- Foto de perfil si existe -->
            <img src="../<?= htmlspecialchars($fotoUsuario) ?>"
                 alt="Foto de perfil"
                 class="topbar-foto">
        <?php else: ?>
            <!-- Avatar con iniciales si no hay foto -->
            <div class="topbar-iniciales">
                <?= $inicialesTopbar ?>
            </div>
        <?php endif; ?>

    </div>

</div>