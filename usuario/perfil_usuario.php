<?php
require_once("../includes/auth_usuario.php");
require_once __DIR__ . "/../config/db.php";

$paginaActiva = "perfil";
$id_usuario   = $_SESSION['id_usuario'];

$sql  = "SELECT * FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi perfil | Sección 49</title>

    <!-- Bootstrap primero -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <!-- Estilos propios después (para sobreescribir Bootstrap) -->
    <link rel="stylesheet" href="../assets/css/sidebar_usuario.css">
    <link rel="stylesheet" href="../assets/css/topbar_usuario.css">
    <link rel="stylesheet" href="../assets/css/perfil_usuario.css">

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

    <div class="container mt-4 mb-5">
        <div class="card shadow border-0">
            <div class="card-body">

                <form action="guardar_perfil.php" method="POST" enctype="multipart/form-data">

                    <!-- SECCIÓN: DATOS DEL USUARIO -->
                    <h3 class="section-title">
                        <i class="fa-solid fa-user me-2"></i>Datos del usuario
                    </h3>

                    <div class="row mb-3 align-items-start">

                        <!-- AVATAR / FOTO -->
                        <div class="col-12 col-md-3 text-center mb-3 mb-md-0">

                            <?php if (!empty($usuario['foto'])): ?>
                                <img src="../<?= htmlspecialchars($usuario['foto']) ?>"
                                     class="profile-pic mb-2"
                                     id="previewFoto"
                                     alt="Foto de perfil">
                            <?php else: ?>
                                <div class="avatar-iniciales mb-2" id="avatarIniciales">
                                    <?= htmlspecialchars($iniciales) ?>
                                </div>
                                <img src="" class="profile-pic mb-2 d-none" id="previewFoto" alt="Foto de perfil">
                            <?php endif; ?>

                            
                            <input type="file" name="foto" id="inputFoto" hidden accept="image/*">

                            <button type="button"
                                    class="upload-btn"
                                    id="btnCambiarFoto"
                                    disabled
                                    style="opacity:0.5; cursor:not-allowed;">
                                <i class="fa-solid fa-upload"></i> Cambiar foto
                            </button>
                        </div>

                        <!-- DATOS PERSONALES -->
                        <div class="col-12 col-md-9">
                            <div class="row">

                                <div class="col-6 mb-3">
                                    <label for="nombre">Nombre</label>
                                    <input type="text"
                                           id="nombre"
                                           name="nombre"
                                           class="form-control"
                                           value="<?= htmlspecialchars($usuario['nombre']) ?>"
                                           maxlength="100"
                                           title="Solo letras y espacios"
                                           disabled>
                                </div>

                                <div class="col-6 mb-3">
                                    <label for="numero_ficha">Número de ficha</label>
                                    <input type="text"
                                           id="numero_ficha"
                                           name="numero_ficha"
                                           class="form-control"
                                           value="<?= htmlspecialchars($usuario['numero_ficha']) ?>"
                                           maxlength="10"
                                           pattern="[0-9]{1,10}"
                                           title="Solo números, máximo 10 dígitos"
                                           disabled>
                                </div>

                                <div class="col-6 mb-3">
                                    <label for="curp">CURP</label>
                                    <input type="text"
                                           id="curp"
                                           name="curp"
                                           class="form-control"
                                           value="<?= htmlspecialchars($usuario['curp']) ?>"
                                           maxlength="18"
                                           minlength="18"
                                           title="La CURP debe tener exactamente 18 caracteres"
                                           style="text-transform:uppercase"
                                           disabled>
                                </div>

                                <div class="col-6 mb-3">
                                    <label for="rfc">RFC</label>
                                    <input type="text"
                                           id="rfc"
                                           name="rfc"
                                           class="form-control"
                                           value="<?= htmlspecialchars($usuario['rfc']) ?>"
                                           maxlength="13"
                                           minlength="12"
                                           title="El RFC debe tener 12 o 13 caracteres"
                                           style="text-transform:uppercase"
                                           disabled>
                                </div>

                                <div class="col-6 mb-3">
                                    <label for="fecha_nacimiento">Fecha de nacimiento</label>
                                    <input type="date"
                                           id="fecha_nacimiento"
                                           name="fecha_nacimiento"
                                           class="form-control"
                                           value="<?= htmlspecialchars($usuario['fecha_nacimiento']) ?>"
                                           max="<?= date('Y-m-d', strtotime('-18 years')) ?>"
                                           title="Debes ser mayor de 18 años"
                                           disabled>
                                </div>

                                <div class="col-6 mb-3">
                                    <label for="password">Contraseña</label>
                                    <input type="password"
                                           id="password"
                                           class="form-control"
                                           value="<?= $password_oculta ?>"
                                           disabled>
                                </div>

                            </div>
                        </div>
                    </div>

                    

                    <!-- SECCIÓN: CONTACTO -->
                    <h3 class="section-title">
                        <i class="fa-solid fa-address-book me-2"></i>Contacto
                    </h3>

                    <div class="row">

                        <div class="col-6 mb-3">
                            <label for="telefono">Teléfono</label>
                            <input type="tel"
                                   id="telefono"
                                   name="telefono"
                                   class="form-control"
                                   value="<?= htmlspecialchars($usuario['telefono']) ?>"
                                   maxlength="10"
                                   pattern="[0-9]{10}"
                                   title="El teléfono debe tener exactamente 10 dígitos"
                                   disabled>
                        </div>

                        <div class="col-6 mb-3">
                            <label for="email">Correo electrónico</label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   class="form-control"
                                   value="<?= htmlspecialchars($usuario['email']) ?>"
                                   maxlength="100"
                                   disabled>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="direccion">Dirección</label>
                            <textarea id="direccion"
                                      name="direccion"
                                      class="form-control"
                                      maxlength="200"
                                      disabled><?= htmlspecialchars($usuario['direccion']) ?></textarea>
                        </div>

                    </div>

                    <!-- BOTONES EDITAR / GUARDAR -->
                    <div class="text-end mt-4">
                        <button type="button" id="btnEditar" class="btn btn-success">
                            <i class="fa-solid fa-pen me-1"></i> Editar
                        </button>
                        <button type="submit" id="btnGuardar" class="btn btn-primary d-none">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Guardar cambios
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

</main>

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sidebar_usuario.js"></script>
<script src="../assets/js/perfil_usuario.js"></script>

</body>
</html>