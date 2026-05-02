<?php
require_once("../includes/auth_usuario.php");
require_once __DIR__ . "/../config/db.php";

$id_usuario = $_SESSION['id_usuario'];

$nombre = $_POST['nombre'];
$telefono = $_POST['telefono'];
$email = $_POST['email'];
$numero_ficha = $_POST['numero_ficha'];
$curp = $_POST['curp'];
$rfc = $_POST['rfc'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$direccion = $_POST['direccion'];

$ruta_foto = null;

// ===== SUBIR FOTO =====
if (!empty($_FILES['foto']['name'])) {

    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $permitidas = ['jpg', 'jpeg', 'png'];

    if (in_array($ext, $permitidas)) {

        $carpeta = "../uploads/perfiles/";

        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        $nombre_foto = "usuario_" . $id_usuario . "_" . time() . "." . $ext;
        $ruta = $carpeta . $nombre_foto;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta)) {
            $ruta_foto = "uploads/perfiles/" . $nombre_foto;
        }
    }
}

// ===== UPDATE =====
if ($ruta_foto) {

    $sql = "UPDATE usuarios SET
        nombre = ?, telefono = ?, email = ?, numero_ficha = ?, curp = ?, rfc = ?,
        fecha_nacimiento = ?, direccion = ?, foto = ?
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

    $sql = "UPDATE usuarios SET
        nombre = ?, telefono = ?, email = ?, numero_ficha = ?, curp = ?, rfc = ?,
        fecha_nacimiento = ?, direccion = ?
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

header("Location: perfil_usuario.php?status=ok");
exit();


