<?php
$host = "localhost";
$user = "root";
$pass = "randy";
$db   = "ProyectoUni";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
