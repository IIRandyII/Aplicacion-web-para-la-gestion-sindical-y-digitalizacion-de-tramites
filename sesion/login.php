<?php
session_start();
require_once __DIR__ . "/../config/db.php";

// ===============================
// FUNCIÓN DE REDIRECCIÓN POR ROL
// Centralizada para no repetir
// la lógica en dos lugares
// ===============================
function redirigirPorRol(string $rol): void {
    if ($rol === "afiliado") {
        header("Location: ../afiliado/dashboard_afiliado.php");
    } else {
        header("Location: ../usuario/dashboard_usuario.php");
    }
    exit();
}

// Si ya hay sesión iniciada, redirigir automáticamente
if (isset($_SESSION["rol"])) {
    redirigirPorRol($_SESSION["rol"]);
}

// Capturar mensaje de registro exitoso enviado desde register.php
$toast_success = null;
if (isset($_SESSION["registro_exitoso"])) {
    $toast_success = $_SESSION["registro_exitoso"];
    unset($_SESSION["registro_exitoso"]);
}

$error = null;

// Procesar formulario de login
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email    = trim($_POST["email"]    ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($email !== "" && $password !== "") {

        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if ($password === $user["password"]) {

                // Crear sesión
                $_SESSION["id_usuario"]      = $user["id_usuario"];
                $_SESSION["nombre"]          = $user["nombre"];
                $_SESSION["rol"]             = $user["rol"];
                $_SESSION["id_departamento"] = $user["id_departamento"];

                // Actualizar fecha de acceso
                $update = $conn->prepare("UPDATE usuarios SET fecha_acceso = NOW() WHERE id_usuario = ?");
                $update->bind_param("i", $user["id_usuario"]);
                $update->execute();

                redirigirPorRol($user["rol"]);

            } else {
                $error = "Contraseña incorrecta.";
            }

        } else {
            $error = "Correo no registrado.";
        }

    } else {
        $error = "Todos los campos son obligatorios.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login | Sindicato Sección 49</title>
    <link rel="stylesheet" href="../assets/css/logeo_registro.css">
</head>
<body>

<div class="auth-card">

    <img src="../assets/img/logo.jpg" alt="Logo Sindicato 49">
    <h2>Sistema Sindical</h2>
    <p>Sección 49</p>

    <?php if ($error): ?>
        <div class="alert alert-error" id="alerta"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($toast_success): ?>
        <div class="alert alert-success" id="alerta"><?= htmlspecialchars($toast_success) ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
        <input type="email"    name="email"    placeholder="Correo"     required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Iniciar sesión</button>
    </form>

    <a href="register.php">¿Aún no tienes cuenta? Regístrate</a>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../assets/js/alertas.js"></script>
</body>
</html>