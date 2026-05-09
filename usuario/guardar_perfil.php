<?php
require_once("../includes/auth_usuario.php");
require_once __DIR__ . "/../config/db.php";

// ===============================
// VARIABLES DE SESIÓN Y POST
// ===============================
$id_usuario      = $_SESSION['id_usuario'];

$nombre          = $_POST['nombre'];
$telefono        = $_POST['telefono'];
$email           = $_POST['email'];
$numero_ficha    = $_POST['numero_ficha'];
$curp            = $_POST['curp'];
$rfc             = $_POST['rfc'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$direccion       = $_POST['direccion'];

$ruta_foto = null;

// ===============================
// SUBIR FOTO DE PERFIL
// Solo se procesa si el usuario
// seleccionó una imagen nueva
// ===============================
if (!empty($_FILES['foto']['name'])) {

    $ext        = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $permitidas = ['jpg', 'jpeg', 'png'];

    if (in_array($ext, $permitidas)) {

        $carpeta = "../uploads/perfiles/";

        // Crear carpeta si no existe
        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        // Nombre único para evitar sobreescrituras
        $nombre_foto = "usuario_" . $id_usuario . "_" . time() . "." . $ext;
        $ruta        = $carpeta . $nombre_foto;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta)) {
            $ruta_foto = "uploads/perfiles/" . $nombre_foto;
        }
    }
}

// ===============================
// ACTUALIZAR PERFIL EN BASE DE DATOS
// Si hay foto nueva se incluye en
// el UPDATE, si no se omite
// ===============================
if ($ruta_foto) {

    // UPDATE con foto
    $sql = "UPDATE usuarios SET
                nombre = ?, telefono = ?, email = ?, numero_ficha = ?,
                curp = ?, rfc = ?, fecha_nacimiento = ?, direccion = ?, foto = ?
            WHERE id_usuario = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssssssi",
        $nombre,
        $telefono,
        $email,
        $numero_ficha,
        $curp,
        $rfc,
        $fecha_nacimiento,
        $direccion,
        $ruta_foto,
        $id_usuario
    );

} else {

    // UPDATE sin foto
    $sql = "UPDATE usuarios SET
                nombre = ?, telefono = ?, email = ?, numero_ficha = ?,
                curp = ?, rfc = ?, fecha_nacimiento = ?, direccion = ?
            WHERE id_usuario = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssssi",
        $nombre,
        $telefono,
        $email,
        $numero_ficha,
        $curp,
        $rfc,
        $fecha_nacimiento,
        $direccion,
        $id_usuario
    );
}

$stmt->execute();

// ===============================
// ACTUALIZAR SESIÓN
// Para que el nombre y foto se
// reflejen inmediatamente en el
// topbar sin necesidad de
// cerrar y volver a iniciar sesión
// ===============================
$_SESSION['nombre'] = $nombre;

if ($ruta_foto) {
    $_SESSION['foto'] = $ruta_foto;
}

// Redirigir al perfil con mensaje de éxito
header("Location: perfil_usuario.php?status=ok");
exit();