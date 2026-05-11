<?php
require_once("../includes/auth_afiliado.php");
require_once __DIR__ . "/../config/db.php";

// ===============================
// VARIABLES DE SESIÓN Y POST
// ===============================
$id_usuario      = $_SESSION['id_usuario'];
$nombre          = $_POST['nombre'];
$telefono        = $_POST['telefono'];
$email           = $_POST['email'];
$curp            = $_POST['curp'];
$rfc             = $_POST['rfc'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];

$ruta_foto = null;

// ===============================
// SUBIR FOTO DE PERFIL
// ===============================
if (!empty($_FILES['foto']['name'])) {

    $ext        = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $permitidas = ['jpg', 'jpeg', 'png'];

    if (in_array($ext, $permitidas)) {

        $carpeta = "../uploads/perfiles/";

        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        $nombre_foto = "afiliado_" . $id_usuario . "_" . time() . "." . $ext;
        $ruta        = $carpeta . $nombre_foto;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta)) {
            $ruta_foto = "uploads/perfiles/" . $nombre_foto;
        }
    }
}

// ===============================
// ACTUALIZAR PERFIL EN BASE DE DATOS
// ===============================
if ($ruta_foto) {

    $sql = "UPDATE usuarios SET
                nombre = ?, telefono = ?, email = ?,
                curp = ?, rfc = ?, fecha_nacimiento = ?, foto = ?
            WHERE id_usuario = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssssi",
        $nombre, $telefono, $email,
        $curp, $rfc, $fecha_nacimiento,
        $ruta_foto, $id_usuario
    );

} else {

    $sql = "UPDATE usuarios SET
                nombre = ?, telefono = ?, email = ?,
                curp = ?, rfc = ?, fecha_nacimiento = ?
            WHERE id_usuario = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssi",
        $nombre, $telefono, $email,
        $curp, $rfc, $fecha_nacimiento,
        $id_usuario
    );
}

$stmt->execute();

// ===============================
// ACTUALIZAR SESIÓN
// ===============================
$_SESSION['nombre'] = $nombre;

if ($ruta_foto) {
    $_SESSION['foto'] = $ruta_foto;
}

header("Location: perfil_afiliado.php?status=ok");
exit();