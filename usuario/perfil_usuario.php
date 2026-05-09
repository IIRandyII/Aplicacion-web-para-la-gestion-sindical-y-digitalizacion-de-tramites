<?php
require_once("../includes/auth_usuario.php");
require_once __DIR__ . "/../config/db.php";

// Variables de sesión y página activa
$paginaActiva = "perfil";
$id_usuario   = $_SESSION['id_usuario'];

// Obtener datos del usuario
$sql  = "SELECT * FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

// Función para obtener iniciales del nombre
function obtenerIniciales($nombre) {
    if (empty(trim($nombre))) return '?';
    $partes   = explode(" ", trim($nombre));
    $inicial1 = strtoupper($partes[0][0]);
    $ultimo   = end($partes);
    $inicial2 = (count($partes) > 1 && !empty($ultimo)) ? strtoupper($ultimo[0]) : '';
    return $inicial1 . $inicial2;
}

$iniciales       = !empty($usuario['nombre']) ? obtenerIniciales($usuario['nombre']) : '?';
$password_oculta = str_repeat('*', strlen($usuario['password']));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi perfil | Sección 49</title>

    <!-- Estilos propios -->
    <link rel="stylesheet" href="../assets/css/sidebar_usuario.css">
    <link rel="stylesheet" href="../assets/css/perfil_usuario.css">

    <!-- Librerías externas -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<!-- SIDEBAR -->
<?php include "../includes/sidebar_usuario.php"; ?>

<!-- CONTENIDO PRINCIPAL -->
<main class="main">

    <!-- TOPBAR -->
    <?php
    $tituloTopbar = "Mi perfil";
    include "../includes/topbar_usuario.php";
    ?>

    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-body">

                <form action="guardar_perfil.php" method="POST" enctype="multipart/form-data">

                    <!-- SECCIÓN: DATOS DEL USUARIO -->
                    <h3 class="section-title">Datos del usuario</h3>

                    <div class="row mb-3">

                        <!-- AVATAR / FOTO -->
                        <div class="col-md-3 text-center">

                            <?php if (!empty($usuario['foto'])): ?>
                                <!-- Foto de perfil existente -->
                                <img src="../<?= $usuario['foto'] ?>"
                                     class="profile-pic mb-3"
                                     id="previewFoto">
                            <?php else: ?>
                                <!-- Avatar con iniciales -->
                                <div class="avatar-iniciales mb-3" id="avatarIniciales">
                                    <?= $iniciales ?>
                                </div>
                                <!-- Preview oculto hasta seleccionar foto -->
                                <img src="" class="profile-pic mb-3 d-none" id="previewFoto">
                            <?php endif; ?>

                            <!-- Input de archivo oculto -->
                            <input type="file" name="foto" id="inputFoto" hidden accept="image/*">

                            <!-- Botón deshabilitado por defecto -->
                            <button type="button"
                                    class="upload-btn"
                                    id="btnCambiarFoto"
                                    disabled
                                    style="opacity:0.5; cursor:not-allowed;">
                                <i class="fa-solid fa-upload"></i> Cambiar foto
                            </button>

                        </div>

                        <!-- DATOS PERSONALES -->
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

                    <!-- SECCIÓN: CONTACTO -->
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
                            <textarea name="direccion" class="form-control"
                                      disabled><?= $usuario['direccion'] ?></textarea>
                        </div>
                    </div>

                    <!-- BOTONES EDITAR / GUARDAR -->
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

<!-- SCRIPTS -->
<script src="../assets/js/sidebar_usuario.js"></script>
<script src="../assets/js/perfil_usuario.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>