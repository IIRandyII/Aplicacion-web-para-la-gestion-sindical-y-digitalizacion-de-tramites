<?php
require_once("../includes/auth_usuario.php");
require_once __DIR__ . "/../config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// ======================
// DATOS GENERALES
// ======================
$id_departamento = $_POST['id_departamento'];
$tipo_tramite   = $_POST['tipo_tramite'];
$nombre         = $_POST['nombre_completo'];
$ficha          = $_POST['numero_ficha'];
$categoria      = $_POST['categoria'];
$turno          = $_POST['turno'];
$email          = $_POST['email'];
$curp           = $_POST['curp'];
$telefono       = $_POST['telefono'];

$documentoRuta = null;

if (!empty($_FILES['documento_respaldo']['name'])) {

    $archivo = $_FILES['documento_respaldo'];

    if ($archivo['error'] === 0) {

        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

        if ($extension === "pdf") {

            $nombreArchivo = uniqid() . "_" . basename($archivo['name']);
            $directorio = __DIR__ . "/../uploads/tramites/";

            if (!is_dir($directorio)) {
                mkdir($directorio, 0777, true);
            }

            $rutaDestino = $directorio . $nombreArchivo;

            if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
                $documentoRuta = "uploads/tramites/" . $nombreArchivo;
            }
        } else {
            http_response_code(400);
            echo json_encode(["success" => false, "error" => "Solo se permiten archivos PDF"]);
            exit();
        }
    }
}


// ======================
// DATOS ESPECÍFICOS
// ======================
$datos_especificos = $_POST;

unset(
    $datos_especificos['id_departamento'],
    $datos_especificos['tipo_tramite'],
    $datos_especificos['nombre_completo'],
    $datos_especificos['numero_ficha'],
    $datos_especificos['categoria'],
    $datos_especificos['turno'],
    $datos_especificos['email'],
    $datos_especificos['curp'],
    $datos_especificos['telefono']
);

$datos_especificos_json = json_encode($datos_especificos, JSON_UNESCAPED_UNICODE);

// ======================
// INSERT
// ======================
$sql = "INSERT INTO tramites (
            id_usuario,
            id_departamento,
            tipo_tramite,
            nombre_completo,
            numero_ficha,
            categoria,
            turno,
            email,
            curp,
            telefono,
            datos_especificos,
            estado,
            documento_respaldo
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pendiente', ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "iissssssssss",
    $id_usuario,
    $id_departamento,
    $tipo_tramite,
    $nombre,
    $ficha,
    $categoria,
    $turno,
    $email,
    $curp,
    $telefono,
    $datos_especificos_json,
    $documentoRuta
);

if ($stmt->execute()) {

$stmtDept = $conn->prepare("SELECT nombre FROM departamentos WHERE id_departamento = ?");
$stmtDept->bind_param("i", $id_departamento);
$stmtDept->execute();
$resultDept = $stmtDept->get_result();
$deptData = $resultDept->fetch_assoc();
$nombreDepartamento = $deptData['nombre'];

    echo json_encode([
        "success"      => true,
        "id_tramite"   => $stmt->insert_id,
        "departamento" => $nombreDepartamento,
        "tramite"      => $tipo_tramite,
        "nombre"       => $nombre,
        "ficha"        => $ficha,
        "categoria"    => $categoria,
        "turno"        => $turno,
        "estado"       => "Pendiente"
    ]);

} else {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $stmt->error
    ]);
}