<?php
session_start();

// Verificar que sea admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../sesion/login.php");
    exit();
}

$nombreAdmin = $_SESSION['nombre'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin | Sección 49</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { background: #f4f6f9; font-family: Arial, sans-serif; }
        .contenedor {
            max-width: 600px;
            margin: 100px auto;
            background: #fff;
            padding: 40px;
            border-radius: 14px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            text-align: center;
        }
        h2 { color: #002855; margin-bottom: 10px; }
        p  { color: #666; margin-bottom: 30px; }
        .btn-logout {
            background: #C62828;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-logout:hover { background: #b71c1c; color: #fff; }
    </style>
</head>
<body>

<div class="contenedor">
    <i class="fa-solid fa-shield-halved" style="font-size:48px; color:#002855; margin-bottom:20px;"></i>
    <h2>Panel de Administración</h2>
    <p>Bienvenido, <strong><?= htmlspecialchars($nombreAdmin) ?></strong>. El panel de administración está en construcción.</p>
    <a href="../sesion/logout.php" class="btn-logout">
        <i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión
    </a>
</div>

</body>
</html>