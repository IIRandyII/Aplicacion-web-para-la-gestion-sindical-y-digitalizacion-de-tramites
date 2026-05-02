<?php
session_start();

// Verificar sesión
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "admin") {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
</head>
<body>

<h1>Dashboard Administrador</h1>

<p>Bienvenido, <strong><?php echo $_SESSION["nombre"]; ?></strong></p>

<ul>
    <li>Gestionar usuarios</li>
    <li>Ver registros</li>
    <li>Panel de control</li>
</ul>

<a href="logout.php">Cerrar sesión</a>

</body>
</html>
