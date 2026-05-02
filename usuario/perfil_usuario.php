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

// Obtener datos del usuario
$sql = "SELECT * FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

// Función para obtener iniciales
function obtenerIniciales($nombre) {
    if (empty(trim($nombre))) return '?';
    
    $partes = explode(" ", trim($nombre));
    $inicial1 = strtoupper($partes[0][0]);
    $ultimo = end($partes);
    $inicial2 = (count($partes) > 1 && !empty($ultimo)) ? strtoupper($ultimo[0]) : '';
    
    return $inicial1 . $inicial2;
}

$iniciales = !empty($usuario['nombre']) ? obtenerIniciales($usuario['nombre']) : '?';

// Password oculta
$password_oculta = str_repeat('*', strlen($usuario['password']));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi perfil | Sección 49</title>

    <link rel="stylesheet" href="../assets/css/perfil_usuario.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="logo">
        <img src="../assets/img/logo.jpg">
        <span>Sección 49</span>
    </div>

    <nav class="menu">
        <a href="dashboard_usuario.php"><i class="fa-solid fa-house"></i><span>Inicio</span></a>
        <a href="perfil_usuario.php" class="active"><i class="fa-solid fa-user"></i><span>Mi perfil</span></a>
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
        <a href="../sesion/logout.php" class="logout">
            <i class="fa-solid fa-right-from-bracket"></i><span>Cerrar sesión</span>
        </a>
    </nav>
</aside>

<main class="main">

    <!-- TOPBAR -->
    <div class="topbar">
        <button class="toggle-btn" id="toggleSidebar">
            <i class="fa-solid fa-bars"></i>
        </button>
        <h2>Mi perfil</h2>
    </div>

    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-body">

                <form action="guardar_perfil.php" method="POST" enctype="multipart/form-data">

                    <h3 class="section-title">Datos del usuario</h3>

                    <div class="row mb-3">
                        <div class="col-md-3 text-center">

                            <!-- AVATAR -->
                            <?php if (!empty($usuario['foto'])): ?>
                                <img src="../<?= $usuario['foto'] ?>" class="profile-pic mb-3">
                            <?php else: ?>
                                <div class="avatar-iniciales mb-3">
                                    <?= $iniciales ?>
                                </div>
                            <?php endif; ?>

                            <label class="upload-btn">
                                <i class="fa-solid fa-upload"></i> Cambiar foto
                                <input type="file" name="foto" hidden accept="image/*">
                            </label>
                        </div>

                        <div class="col-md-9">
                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label>Nombre</label>
                                    <input type="text" name="nombre" class="form-control"
                                           value="<?= $usuario['nombre'] ?>" disabled>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Número de ficha</label>
                                    <input type="text" name="numero_ficha" class="form-control"
                                           value="<?= $usuario['numero_ficha'] ?>" disabled>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>CURP</label>
                                    <input type="text" name="curp" class="form-control"
                                           value="<?= $usuario['curp'] ?>" disabled>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>RFC</label>
                                    <input type="text" name="rfc" class="form-control"
                                           value="<?= $usuario['rfc'] ?>" disabled>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Fecha de nacimiento</label>
                                    <input type="date" name="fecha_nacimiento" class="form-control"
                                           value="<?= $usuario['fecha_nacimiento'] ?>" disabled>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Password</label>
                                    <input type="password" class="form-control"
                                    value="<?= $password_oculta ?>" disabled>
                                </div>

                            </div>
                        </div>
                    </div>

                    <hr>

                    <h3 class="section-title">Contacto</h3>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Teléfono</label>
                            <input type="text" name="telefono" class="form-control"
                                   value="<?= $usuario['telefono'] ?>" disabled>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Correo electrónico</label>
                            <input type="email" name="email" class="form-control"
                                   value="<?= $usuario['email'] ?>" disabled>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Dirección</label>
                            <textarea name="direccion" class="form-control" disabled><?= $usuario['direccion'] ?></textarea>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="button" id="btnEditar" class="btn btn-secondary">
                            <i class="fa-solid fa-pen"></i> Editar
                        </button>

                        <button type="submit" id="btnGuardar" class="btn btn-primary d-none">
                            <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

</main>

<?php if (isset($_GET['status']) && $_GET['status'] === 'ok'): ?>
<script>
document.addEventListener("DOMContentLoaded", () => {

    Swal.fire({
        icon: 'success',
        title: 'Perfil actualizado',
        text: 'Tus cambios se guardaron correctamente',
        timer: 1600,
        showConfirmButton: false,

        toast: true,
        position: 'top',
        width: 300,

        background: '#ffffff',
        iconColor: '#22c55e',

        showClass: {
            popup: 'animate__animated animate__fadeInDown animate__faster'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp animate__faster'
        },

        customClass: {
            popup: 'swal-perfil',
            title: 'swal-title',
            htmlContainer: 'swal-text'
        }
    });

    const url = new URL(window.location);
    url.searchParams.delete('status');
    window.history.replaceState({}, document.title, url.pathname);

});
</script>
<?php endif; ?>

<script src="../assets/js/perfil_usuario.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>