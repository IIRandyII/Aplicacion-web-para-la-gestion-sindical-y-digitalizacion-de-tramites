<?php
session_start();
require_once __DIR__ . "/../config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre   = $_POST["nombre"];
    $telefono = $_POST["telefono"];
    $email    = $_POST["email"];
    $password = $_POST["password"]; // SIN HASH por ahora
    $rol      = "usuario";

    // Verificar si el correo ya existe
    $check = "SELECT id_usuario FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($check);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $mensaje = "Este correo ya está registrado";
    } else {

        // Insertar usuario
        $sql = "INSERT INTO usuarios (nombre, telefono, email, password, rol)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $nombre, $telefono, $email, $password, $rol);

        if ($stmt->execute()) {
            header("Location: login.php");
            exit();
        } else {
            $mensaje = "Error al registrar usuario";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro | Sindicato Sección 49</title>
    <link rel="stylesheet" href="../assets/css/logeo_registro.css">
</head>
<body>

<div class="auth-card">

    <img src="../assets/img/logo.jpg" alt="Logo Sindicato 49">

    <h2>Registro de Usuario</h2>
    <p>Sección 49</p>

    <?php if (isset($mensaje)): ?>
        <p style="color:#C62828; margin-bottom:15px; font-size:14px;">
            <?php echo $mensaje; ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="nombre" placeholder="Nombre completo" required>
        <input type="tel" name="telefono" placeholder="Teléfono" required>
        <input type="email" name="email" placeholder="Correo electrónico" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Registrarse</button>
    </form>

    <a href="login.php">¿Ya tienes cuenta? Inicia sesión</a>
</div>

</body>
</html>
