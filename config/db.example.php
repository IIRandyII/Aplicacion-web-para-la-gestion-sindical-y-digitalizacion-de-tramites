<?php
$host = "localhost";
$usuario = "tu_usuario";
$password = "tu_contraseña";
$nombre_bd = "nombre_base_de_datos";

$conn = mysqli_connect($host, $usuario, $password, $nombre_bd);

if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>