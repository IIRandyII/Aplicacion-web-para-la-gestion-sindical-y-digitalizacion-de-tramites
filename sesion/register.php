<?php
session_start();
require_once __DIR__ . "/../config/db.php";

$error = null;

// Procesar formulario de registro
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre   = trim($_POST["nombre"]   ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    $email    = trim($_POST["email"]    ?? "");
    $password = trim($_POST["password"] ?? "");
    $rol      = "usuario";

    // Validar formato de email y que el dominio exista
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !checkdnsrr(explode('@', $email)[1], 'MX')) {
        $error = "Ingresa un correo electrónico válido.";

    } else {

        // Verificar si el correo ya está registrado
        $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Este correo ya está registrado.";

        } else {

            // Insertar nuevo usuario
            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, telefono, email, password, rol) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $nombre, $telefono, $email, $password, $rol);

            if ($stmt->execute()) {
                $_SESSION["registro_exitoso"] = "¡Registro exitoso! Ya puedes iniciar sesión.";
                header("Location: login.php");
                exit();
            } else {
                $error = "Error al registrar usuario. Intenta de nuevo.";
            }
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

    <?php if ($error): ?>
        <div class="alert alert-error" id="alerta"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text"     name="nombre"   placeholder="Nombre completo"     required>
        <input type="tel"      name="telefono" placeholder="Teléfono"            required pattern="[0-9]{10}" title="Solo números, 10 dígitos">
        <input type="email"    name="email"    placeholder="Correo electrónico"  required>
        <input type="password" name="password" placeholder="Contraseña"          required>
        <button type="submit">Registrarse</button>
    </form>

    <a href="login.php">¿Ya tienes cuenta? Inicia sesión</a>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../assets/js/alertas.js"></script>
</body>
</html>