<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../sesion/login.php");
    exit();
}

if ($_SESSION['rol'] !== 'afiliado') {
    header("Location: ../sesion/login.php");
    exit();
}
?>